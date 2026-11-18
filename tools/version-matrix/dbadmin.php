<?php
// php dbadmin.php create|drop <name> — a database of the matrix on the server in MATRIX_DATABASE_URL.
[$_, $action, $name] = $argv;
$u = parse_url((string) getenv('MATRIX_DATABASE_URL'));
$pdo = new PDO(sprintf('mysql:host=%s;port=%d', $u['host'], $u['port'] ?? 3306), rawurldecode($u['user']), rawurldecode($u['pass'] ?? ''));
$pdo->exec('DROP DATABASE IF EXISTS `'.$name.'`');
if ('create' === $action) {
    $pdo->exec('CREATE DATABASE `'.$name.'` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
}
echo "$action $name ok\n";
