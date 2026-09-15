<?php

require_once '../config.php';

// Limpa todas as informações da sessão
$_SESSION = [];

// Remove o cookie da sessão do navegador
if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

// Destrói a sessão no servidor
session_destroy();

// Volta para o login
header('Location: login.php');
exit;

?>