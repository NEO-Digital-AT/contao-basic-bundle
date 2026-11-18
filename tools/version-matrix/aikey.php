<?php
// Run inside an installation: stores a placeholder AI key (never used: no request goes out in the smoke test),
// so that the chat window counts as set up.
require getcwd().'/vendor/autoload.php';
$env = [];
foreach (file(getcwd().'/.env.local') as $line) { if (preg_match('/^([A-Z_]+)="?([^"\n]*)"?$/', trim($line), $m)) { $env[$m[1]] = $m[2]; } }
$params = (new Doctrine\DBAL\Tools\DsnParser(['mysql' => 'pdo_mysql']))->parse($env['DATABASE_URL']);
$db = Doctrine\DBAL\DriverManager::getConnection($params);
$settings = new Neo\BasicBundle\Access\AccessSettings($db, new Neo\BasicBundle\Security\SecretCipher($env['APP_SECRET']));
(new Neo\BasicBundle\Ai\AiConnection($settings))->store('requesty_eu', '', 'placeholder-key');
echo "ai key stored\n";
