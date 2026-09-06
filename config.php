<?php
session_start();

const DB_HOST = 'localhost';
const DB_NAME = 'onedose';
const DB_USER = 'root';
const DB_PASS = '';

function db(): ?PDO {
    static $pdo = null;
    static $attempted = false;
    if ($attempted) return $pdo;
    $attempted = true;

    try {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    } catch (PDOException $e) {
        $pdo = null;
    }

    return $pdo;
}

function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function is_admin(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!is_admin()) {
        header('Location: login.php');
        exit;
    }
}

function demo_products(): array {
    return [
        ['id'=>1,'nome'=>'Coco','descricao'=>'Bebida alcoólica mista sabor coco, apresentada em dose individual de 50 ml.','volume'=>'50 ml','teor'=>'20% vol.','imagem'=>'assets/img/coco.jpg','ativo'=>1],
        ['id'=>2,'nome'=>'Bananinha','descricao'=>'Bebida alcoólica mista sabor banana, apresentada em dose individual de 50 ml.','volume'=>'50 ml','teor'=>'20% vol.','imagem'=>'assets/img/bananinha.jpg','ativo'=>1],
        ['id'=>3,'nome'=>'Umburana','descricao'=>'Bebida alcoólica mista da linha 1 Dose, apresentada em dose individual de 50 ml.','volume'=>'50 ml','teor'=>'20% vol.','imagem'=>'assets/img/umburana.jpg','ativo'=>1],
        ['id'=>4,'nome'=>'Branca Pura','descricao'=>'Cachaça da linha 1 Dose em apresentação individual de 50 ml.','volume'=>'50 ml','teor'=>'33% vol.','imagem'=>'assets/img/branca-pura.jpg','ativo'=>1],
        ['id'=>5,'nome'=>'Canelinha','descricao'=>'Bebida alcoólica mista sabor canela, apresentada em dose individual de 50 ml.','volume'=>'50 ml','teor'=>'20% vol.','imagem'=>'assets/img/canelinha.jpg','ativo'=>1],
        ['id'=>6,'nome'=>'Amarelinha Ouro','descricao'=>'Cachaça da linha 1 Dose em apresentação individual de 50 ml.','volume'=>'50 ml','teor'=>'33% vol.','imagem'=>'assets/img/amarelinha-ouro.jpg','ativo'=>1]
    ];
}
?>
