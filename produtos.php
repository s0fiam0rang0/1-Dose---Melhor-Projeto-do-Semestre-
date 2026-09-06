<?php
require 'config.php';
$pageTitle='Sabores';
$pdo=db();
$produtos=$pdo ? $pdo->query("SELECT * FROM produtos WHERE ativo=1 ORDER BY id ASC")->fetchAll() : demo_products();
require 'header.php';
?>
<section class="page-hero page-hero-products">
    <div class="container">
        <span class="eyebrow gold">LINHA 1 DOSE</span>
        <h1>Seis sabores.<br>Uma dose para cada momento.</h1>
        <p>Todos disponíveis em copos individuais de 50 ml. Selecione um sabor para ver os detalhes.</p>
    </div>
</section>
<section class="section catalog-section">
    <div class="container product-grid flavor-grid catalog-grid">
        <?php foreach($produtos as $p): ?>
        <article class="product-card flavor-card">
            <a href="produto.php?id=<?= (int)$p['id'] ?>" class="product-link">
                <div class="product-visual">
                    <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>">
                    <span class="view-chip">Conhecer</span>
                </div>
                <div class="product-body">
                    <div class="product-meta"><span><?= e($p['volume']) ?></span><span><?= e($p['teor']) ?></span></div>
                    <h3><?= e($p['nome']) ?></h3>
                    <p><?= e($p['descricao']) ?></p>
                </div>
            </a>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<section class="section photo-break">
    <div class="container photo-break-grid">
        <img src="assets/img/hero-expositor.jpg" alt="Expositor 1 Dose">
        <div>
            <span class="eyebrow orange">APRESENTAÇÃO</span>
            <h2>Compacta, prática e pronta para exposição.</h2>
            <p>A embalagem comercial reúne 10 copos de 50 ml, criando uma apresentação simples para o ponto de venda.</p>
            <a href="contato.php" class="btn btn-primary">Consultar condições comerciais</a>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>
