<?php

require_once '../config.php';
require_admin();

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

// Somente administradores podem excluir produtos
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    header('Location: index.php?erro=sem_permissao');
    exit;
}

// Exclusão somente por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}
// Verifica a proteção CSRF
if (!csrf_verify()) {
    header('Location: index.php?erro=csrf');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    header('Location: index.php?erro=produto_invalido');
    exit;
}

// Verifica se o produto existe
$stmt = $pdo->prepare(
    'SELECT id
     FROM produtos
     WHERE id = ?
     LIMIT 1'
);

$stmt->execute([$id]);

if (!$stmt->fetch()) {
    header('Location: index.php?erro=produto_nao_encontrado');
    exit;
}

// Exclui o produto
$stmt = $pdo->prepare(
    'DELETE FROM produtos
     WHERE id = ?'
);

$stmt->execute([$id]);

header('Location: index.php?sucesso=produto_excluido');
exit;