<?php
// php smoke.php <base url> <password> <fixture json>
// Logs in to the back end, opens every module of the menu group "neo" and the pages it links to (no actions that
// change data), the core forms the extensions extend, and the front end. Prints one line per failure.
[$_, $base, $password, $fixtureJson] = $argv;
$fx = json_decode($fixtureJson, true);
$jar = tempnam(sys_get_temp_dir(), 'smoke');
// Contao sets its CSRF cookie only for a client that sends cookies, as a browser does after its first visit.
file_put_contents($jar, "127.0.0.1\tFALSE\t/\tFALSE\t0\tprobe\t1\n");
$failures = [];
$visited = 0;
function http(string $url, array|null $post = null): array {
    global $jar;
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_FOLLOWLOCATION => true, CURLOPT_COOKIEJAR => $jar, CURLOPT_COOKIEFILE => $jar, CURLOPT_TIMEOUT => 120, CURLOPT_HTTPHEADER => ['Accept-Language: en']]);
    if (null !== $post) { curl_setopt($ch, CURLOPT_POST, true); curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post)); }
    $body = (string) curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);
    return [(int) $info['http_code'], $body, (string) $info['url']];
}
function dom(string $html): DOMXPath { $d = new DOMDocument(); @$d->loadHTML('<?xml encoding="utf-8"?>'.$html); return new DOMXPath($d); }
function check(string $label, string $url, string $from = ''): array {
    global $failures, $visited;
    $visited++;
    [$status, $body] = http($url);
    $url = preg_replace('/&rt=[^&]*/', '', $url);
    if (200 !== $status) { $failures[] = "$status $label $url".('' !== $from ? ' (link on '.preg_replace('/&rt=[^&]*/', '', $from).')' : ''); }
    elseif (preg_match('/(Fatal error|Uncaught|An error occurred|Stack trace|Warning:|Notice:|Deprecated:)/', $body, $m)) { $failures[] = "text '{$m[1]}' $label $url"; }
    return [$status, $body];
}
// Log in.
[, $html, $loginUrl] = http($base.'/contao/login');
$x = dom($html);
$form = $x->query('//form')->item(0);
$fields = [];
foreach ($x->query('.//input', $form) as $in) { if ($in->getAttribute('name')) { $fields[$in->getAttribute('name')] = $in->getAttribute('value'); } }
$fields['username'] = 'admin'; $fields['password'] = $password;
$action = $form->getAttribute('action') ?: $loginUrl;
if (!str_starts_with($action, 'http')) { $action = $base.(str_starts_with($action, '/') ? '' : '/').$action; }
[$status, $html] = http($action, $fields);
if (!str_contains($html, 'tl_navigation') && !str_contains($html, 'id="neo"')) { echo "FAIL login ($status)\n"; exit(1); }
// Modules of the group "neo".
$x = dom($html);
$modules = [];
foreach ($x->query('//*[@id="neo"]//a[@href]') as $a) {
    parse_str((string) parse_url(html_entity_decode($a->getAttribute('href')), PHP_URL_QUERY), $q);
    if (!empty($q['do'])) { $modules[$q['do']] = true; }
}
$modules = array_keys($modules);
$crossSeen = [];
for ($mi = 0; $mi < count($modules); $mi++) {
    $do = $modules[$mi];
    $queue = [[$base.'/contao?do='.$do, '']]; $seen = []; $n = 0;
    while ($queue && $n < 30) {
        [$url, $from] = array_shift($queue);
        $key = preg_replace('/[&?](rt|ref)=[^&]*/', '', $url);
        if (isset($seen[$key])) { continue; }
        $seen[$key] = true; $n++;
        [$status, $body] = check("module $do", $url, $from);
        if (200 !== $status) { continue; }
        foreach (dom($body)->query('//*[@id="main"]//a[@href] | //*[@id="tl_buttons"]//a[@href]') as $a) {
            $href = html_entity_decode($a->getAttribute('href'));
            parse_str((string) parse_url($href, PHP_URL_QUERY), $hq);
            // Modules of the extensions that are not in the menu (e.g. reached by a button) are crawled as well, and
            // every link from one module to another is opened once as it is (it must not carry over the table).
            if (isset($hq['do']) && $hq['do'] !== $do && str_starts_with((string) $hq['do'], 'neo')) {
                if (!in_array($hq['do'], $modules, true)) { $modules[] = (string) $hq['do']; }
                $cross = str_starts_with($href, 'http') ? $href : $base.(str_starts_with($href, '/') ? '' : '/').$href;
                $crossKey = preg_replace('/[&?](rt|ref)=[^&]*/', '', $cross);
                if (!isset($crossSeen[$crossKey]) && !preg_match('/act=(delete|copy|cut|paste|toggle)|key=/', $href)) { $crossSeen[$crossKey] = true; check("link from $do", $cross, $url); }
            }
            if (($hq['do'] ?? '') !== $do || preg_match('/act=(delete|copy|cut|paste|toggle|deleteAll|copyAll|cutAll)|key=|token=|&mode=|nb=1&act=create|act=create/', $href)) { continue; }
            $queue[] = [str_starts_with($href, 'http') ? $href : $base.(str_starts_with($href, '/') ? '' : '/').$href, $url];
        }
    }
}
// Core forms and lists the extensions add to.
$core = [
    'page tree' => '/contao?do=page',
    'root page' => '/contao?do=page&act=edit&id='.$fx['root'],
    'regular page' => '/contao?do=page&act=edit&id='.$fx['page'],
    'articles' => '/contao?do=article',
    'content list' => '/contao?do=article&table=tl_content&id='.$fx['article'],
    'modules' => '/contao?do=themes&table=tl_module&id='.$fx['theme'],
    'layout' => '/contao?do=themes&table=tl_layout&act=edit&id='.$fx['layout'],
    'user' => '/contao?do=user&act=edit&id=1',
    'user groups' => '/contao?do=group',
    'settings' => '/contao?do=settings',
    'maintenance' => '/contao?do=maintenance',
    'system log' => '/contao?do=log',
];
foreach ($fx['modules'] as $id) { $core['module '.$id] = '/contao?do=themes&table=tl_module&act=edit&id='.$id; }
foreach ($fx['elements'] as $id) { $core['element '.$id] = '/contao?do=article&table=tl_content&act=edit&id='.$id; }
foreach ($core as $label => $path) { check($label, $base.$path); }
// Front end, as visitor (own cookie jar) and in the preview.
$jarBackend = $jar; $jar = tempnam(sys_get_temp_dir(), 'smoke');
[$status, $body] = check('front end', $base.'/');
$markers = ['accessibility' => 'nax', 'newsletter' => 'brevo', 'chat' => 'nsa', 'cookie list' => 'ncc', 'website check' => 'nwc'];
foreach ($markers as $name => $marker) { if (200 === $status && !str_contains($body, $marker)) { $failures[] = "front end without $name ($marker)"; } }
// Spam protection of the website check: ALTCHA where Contao has it (5.4 and later), otherwise the honeypot.
$altcha = str_contains($body, '<altcha-widget') ? (str_contains($body, 'altcha.i18n') ? 'altcha3' : 'altcha') : 'honeypot';
$jar = $jarBackend;
check('preview', $base.'/preview.php/');
echo json_encode(['modules' => $modules, 'visited' => $visited, 'spam_protection' => $altcha ?? '', 'failures' => $failures], JSON_UNESCAPED_SLASHES), "\n";
exit($failures ? 1 : 0);
