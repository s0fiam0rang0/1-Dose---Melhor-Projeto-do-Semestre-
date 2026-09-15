<?php

session_start();


const DB_HOST = 'sql109.infinityfree.com';
const DB_NAME = 'if0_42749836_1dose';
const DB_USER = 'if0_42749836';

// Coloque aqui a senha atual do banco
const DB_PASS = 'lat3nNRchQry';


function db(): ?PDO
{
    static $pdo = null;
    static $attempted = false;

    if ($attempted) {
        return $pdo;
    }

    $attempted = true;

    try {

        $pdo = new PDO(
            'mysql:host=' . DB_HOST .
            ';dbname=' . DB_NAME .
            ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );

    } catch (PDOException $e) {

        $pdo = null;
    }

    return $pdo;
}


function e(string $value): string
{
    return htmlspecialchars(
        $value,
        ENT_QUOTES,
        'UTF-8'
    );
}


function is_admin(): bool
{
    return !empty($_SESSION['admin_id']);
}


function require_admin(): void
{
    // 30 minutos de inatividade
    $tempoLimite = 30 * 60;

    // Verifica se existe usuário logado
    if (!is_admin()) {

        header('Location: login.php');
        exit;
    }

    // Verifica se a sessão expirou por inatividade
    if (
        isset($_SESSION['ultima_atividade']) &&
        (time() - $_SESSION['ultima_atividade']) > $tempoLimite
    ) {

        // Limpa os dados da sessão
        $_SESSION = [];

        // Remove o cookie da sessão
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

        // Destrói a sessão
        session_destroy();

        // Volta para o login
        header('Location: login.php?expirou=1');
        exit;
    }

    // Busca os dados atuais do usuário no banco
    $pdo = db();

    if (!$pdo) {

        $_SESSION = [];

        session_destroy();

        header('Location: login.php');
        exit;
    }

    $stmt = $pdo->prepare(
        'SELECT id, nome, nivel
         FROM usuarios
         WHERE id = ?
         LIMIT 1'
    );

    $stmt->execute([
        (int)$_SESSION['admin_id']
    ]);

    $usuarioAtual = $stmt->fetch();

    // Se a conta não existir mais,
    // encerra a sessão
    if (!$usuarioAtual) {

        $_SESSION = [];

        session_destroy();

        header('Location: login.php');
        exit;
    }

    // Atualiza os dados da sessão
    // conforme os dados atuais do banco
    $_SESSION['admin_nome'] = $usuarioAtual['nome'];
    $_SESSION['admin_nivel'] = $usuarioAtual['nivel'];

    // Reinicia a contagem dos 30 minutos
    $_SESSION['ultima_atividade'] = time();
}


function demo_products(): array
{
    return [

        [
            'id' => 1,
            'nome' => 'Coco',
            'descricao' => 'Bebida alcoólica mista sabor coco, apresentada em dose individual de 50 ml.',
            'volume' => '50 ml',
            'teor' => '20% vol.',
            'imagem' => 'assets/img/coco.jpg',
            'ativo' => 1
        ],

        [
            'id' => 2,
            'nome' => 'Bananinha',
            'descricao' => 'Bebida alcoólica mista sabor banana, apresentada em dose individual de 50 ml.',
            'volume' => '50 ml',
            'teor' => '20% vol.',
            'imagem' => 'assets/img/bananinha.jpg',
            'ativo' => 1
        ],

        [
            'id' => 3,
            'nome' => 'Umburana',
            'descricao' => 'Bebida alcoólica mista da linha 1 Dose, apresentada em dose individual de 50 ml.',
            'volume' => '50 ml',
            'teor' => '20% vol.',
            'imagem' => 'assets/img/umburana.jpg',
            'ativo' => 1
        ],

        [
            'id' => 4,
            'nome' => 'Branca Pura',
            'descricao' => 'Cachaça da linha 1 Dose em apresentação individual de 50 ml.',
            'volume' => '50 ml',
            'teor' => '33% vol.',
            'imagem' => 'assets/img/branca-pura.jpg',
            'ativo' => 1
        ],

        [
            'id' => 5,
            'nome' => 'Canelinha',
            'descricao' => 'Bebida alcoólica mista sabor canela, apresentada em dose individual de 50 ml.',
            'volume' => '50 ml',
            'teor' => '20% vol.',
            'imagem' => 'assets/img/canelinha.jpg',
            'ativo' => 1
        ],

        [
            'id' => 6,
            'nome' => 'Amarelinha Ouro',
            'descricao' => 'Cachaça da linha 1 Dose em apresentação individual de 50 ml.',
            'volume' => '50 ml',
            'teor' => '33% vol.',
            'imagem' => 'assets/img/amarelinha-ouro.jpg',
            'ativo' => 1
        ]

    ];
}


// Gera e guarda um token CSRF na sessão
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {

        $_SESSION['csrf_token'] =
            bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}


// Cria o campo oculto para usar nos formulários
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' .
        e(csrf_token()) .
        '">';
}


// Confere se o token recebido é válido
function csrf_verify(): bool
{
    $tokenRecebido =
        $_POST['csrf_token'] ?? '';

    if (
        empty($_SESSION['csrf_token']) ||
        $tokenRecebido === ''
    ) {
        return false;
    }

    return hash_equals(
        $_SESSION['csrf_token'],
        $tokenRecebido
    );
}

?>