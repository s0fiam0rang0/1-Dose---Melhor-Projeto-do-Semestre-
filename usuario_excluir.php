<?php

require_once '../config.php';
require_admin();

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

// Somente administradores
if (($_SESSION['admin_nivel'] ?? '') !== 'admin') {
    header('Location: index.php');
    exit;
}

// Exclusão somente por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: usuarios.php');
    exit;
}

// Verifica a proteção CSRF
if (!csrf_verify()) {
    header('Location: usuarios.php?erro=csrf');
    exit;
}

$id = (int)($_POST['id'] ?? 0);

if (!$id) {
    header('Location: usuarios.php?erro=nao_encontrado');
    exit;
}

// Não permite excluir a própria conta
if ($id === (int)$_SESSION['admin_id']) {
    header('Location: usuarios.php?erro=propria_conta');
    exit;
}

// Busca o usuário
$stmt = $pdo->prepare(
    'SELECT id, nome, nivel
     FROM usuarios
     WHERE id = ?
     LIMIT 1'
);

$stmt->execute([$id]);

$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: usuarios.php?erro=nao_encontrado');
    exit;
}

// Se for administrador, verifica quantos admins existem
if ($usuario['nivel'] === 'admin') {

    $stmt = $pdo->query(
        "SELECT COUNT(*)
         FROM usuarios
         WHERE nivel = 'admin'"
    );

    $totalAdmins = (int)$stmt->fetchColumn();

    // Nunca permite excluir o último administrador
    if ($totalAdmins <= 1) {
        header('Location: usuarios.php?erro=ultimo_admin');
        exit;
    }
}

// Exclui o usuário
$stmt = $pdo->prepare(
    'DELETE FROM usuarios
     WHERE id = ?'
);

$stmt->execute([$id]);

header('Location: usuarios.php?sucesso=excluido');
exit;