<?php if (!isset($pageTitle)) $pageTitle = '1 Dose'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="1 Dose - Cachaças artesanais em doses individuais de 50 ml.">
    <title><?= e($pageTitle) ?> | 1 Dose</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div id="ageGate" class="age-gate" aria-hidden="true">
    <div class="age-card">
        <img src="assets/img/logo-1dose.jpg" alt="Logo 1 Dose">
        <span class="eyebrow orange">CONSUMO RESPONSÁVEL</span>
        <h2>Você tem 18 anos ou mais?</h2>
        <p>Este site contém informações sobre bebidas alcoólicas e é destinado exclusivamente a maiores de 18 anos.</p>
        <div class="age-actions">
            <button id="ageYes" class="btn btn-primary">Sim, tenho 18+</button>
            <button id="ageNo" class="btn btn-outline">Não</button>
        </div>
    </div>
</div>
<header class="site-header">
    <div class="container nav-wrap">
        <a href="index.php" class="brand"><img src="assets/img/logo-1dose.jpg" alt="1 Dose"></a>
        <button class="menu-toggle" aria-label="Abrir menu">☰</button>
        <nav class="nav-links">
            <a href="index.php">Início</a>
            <a href="sobre.php">A marca</a>
            <a href="produtos.php">Sabores</a>
            <a href="onde-encontrar.php">Onde encontrar</a>
            <a href="contato.php" class="nav-cta">Fale com a 1 Dose</a>
        </nav>
    </div>
</header>
<main>
