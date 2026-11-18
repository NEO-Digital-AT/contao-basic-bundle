<?php
// php fixture.php <database>  — a small site using every front-end part of the extensions.
[$_, $db] = $argv;
$u = parse_url((string) getenv('MATRIX_DATABASE_URL'));
$pdo = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $u['host'], $u['port'] ?? 3306, $db), rawurldecode($u['user']), rawurldecode($u['pass'] ?? ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$columns = [];
$insert = static function (string $table, array $values) use ($pdo, &$columns): int {
    $columns[$table] ??= array_column($pdo->query('SHOW COLUMNS FROM `'.$table.'`')->fetchAll(PDO::FETCH_ASSOC), 'Field');
    $values = array_intersect_key($values + ['tstamp' => time()], array_flip($columns[$table]));
    $st = $pdo->prepare(sprintf('INSERT INTO `%s` (`%s`) VALUES (%s)', $table, implode('`,`', array_keys($values)), implode(',', array_fill(0, count($values), '?'))));
    $st->execute(array_values($values));
    return (int) $pdo->lastInsertId();
};
$theme = $insert('tl_theme', ['name' => 'Test']);
$agent = $insert('tl_neo_agent', ['title' => 'Assistant', 'published' => '1', 'active' => '1']);
$mods = [
    $insert('tl_module', ['pid' => $theme, 'type' => 'neo_accessibility', 'name' => 'Accessibility']),
    $insert('tl_module', ['pid' => $theme, 'type' => 'brevo_newsletter', 'name' => 'Newsletter']),
    $insert('tl_module', ['pid' => $theme, 'type' => 'neo_agent', 'name' => 'Chat', 'agent' => $agent]),
];
$modules = [['mod' => '0', 'col' => 'main', 'enable' => '1']];
foreach ($mods as $id) { $modules[] = ['mod' => (string) $id, 'col' => 'main', 'enable' => '1']; }
$layout = $insert('tl_layout', ['pid' => $theme, 'name' => 'Default', 'rows' => '1rw', 'cols' => '1cl', 'modules' => serialize($modules), 'template' => 'fe_page', 'viewport' => 'width=device-width,initial-scale=1']);
$root = $insert('tl_page', ['pid' => 0, 'sorting' => 128, 'type' => 'root', 'title' => 'Site', 'alias' => 'site', 'language' => 'en', 'fallback' => '1', 'published' => '1', 'includeLayout' => '1', 'layout' => $layout, 'dns' => '', 'useSSL' => 0]);
$page = $insert('tl_page', ['pid' => $root, 'sorting' => 128, 'type' => 'regular', 'title' => 'Home', 'alias' => 'index', 'published' => '1', 'robots' => 'index,follow']);
$article = $insert('tl_article', ['pid' => $page, 'sorting' => 128, 'title' => 'Home', 'alias' => 'home', 'inColumn' => 'main', 'published' => '1']);
$elements = [];
foreach ([['type' => 'text', 'text' => '<p>Hello world.</p>'], ['type' => 'neo_cookieconsent_list'], ['type' => 'neo_websitecheck']] as $i => $el) {
    $elements[] = $insert('tl_content', $el + ['ptable' => 'tl_article', 'pid' => $article, 'sorting' => 128 * ($i + 1)]);
}
echo json_encode(['theme' => $theme, 'modules' => $mods, 'layout' => $layout, 'root' => $root, 'page' => $page, 'article' => $article, 'elements' => $elements, 'agent' => $agent]), "\n";
