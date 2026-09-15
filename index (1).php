<?php
require 'config.php';
$pageTitle = 'Início';
$pdo = db();
if ($pdo) {
    $produtos = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY id ASC LIMIT 6")->fetchAll();
} else {
    $produtos = demo_products();
}
require 'header.php';
?>
<section class="hero-premium">
    <div class="hero-photo" aria-hidden="true"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-premium-content">
        <div class="hero-copy">
            <span class="eyebrow gold">CACHAÇA ARTESANAL • 50 ML</span>
            <h1>Pequena no tamanho.<br><em>Marcante na presença.</em></h1>
            <p>A 1 Dose transforma a cachaça artesanal em uma apresentação prática, pronta para servir, expor e compartilhar.</p>
            <div class="hero-actions">
                <a href="produtos.php" class="btn btn-primary">Explorar sabores</a>
                <a href="contato.php" class="btn btn-ghost">Quero revender</a>
            </div>
        </div>
    </div>
</section>

<section class="ticker" aria-label="Diferenciais">
    <div class="container ticker-grid">
        <span>Produção própria</span><span>Ingredientes selecionados</span><span>50 ml por dose</span><span>Pronto para exposição</span>
    </div>
</section>

<section class="section story-intro">
    <div class="container split editorial-split">
        <div>
            <span class="eyebrow orange">A MARCA</span>
            <h2>Uma dose criada para caber em diferentes momentos.</h2>
        </div>
        <div class="lead-copy">
            <p>A 1 Dose nasceu com o propósito de oferecer uma experiência prática, saborosa e de alta qualidade. O processo é realizado internamente, do desenvolvimento da bebida ao envase, selagem, controle de qualidade e distribuição.</p>
            <a class="text-link" href="sobre.php">Conheça nossa história →</a>
        </div>
    </div>
</section>

<section class="section flavor-section">
    <div class="container">
        <div class="section-heading">
            <div>
                <span class="eyebrow gold">LINHA DE SABORES</span>
                <h2>Escolha a sua dose.</h2>
                <p class="section-subtitle">Seis opções em copos individuais de 50 ml.</p>
            </div>
            <a href="produtos.php" class="text-link light-link">Ver catálogo completo →</a>
        </div>
        <div class="product-grid flavor-grid">
            <?php foreach ($produtos as $p): ?>
                <article class="product-card flavor-card">
                    <a href="produto.php?id=<?= (int)$p['id'] ?>" class="product-link">
                        <div class="product-visual">
                            <img src="<?= e($p['imagem']) ?>" alt="<?= e($p['nome']) ?>">
                            <span class="view-chip">Ver sabor</span>
                        </div>
                        <div class="product-body">
                            <div class="product-meta"><span><?= e($p['volume']) ?></span><span><?= e($p['teor']) ?></span></div>
                            <h3><?= e($p['nome']) ?></h3>
                        </div>
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section process-section">
    <div class="container">
        <span class="eyebrow orange">DA PRODUÇÃO À SUA DOSE</span>
        <h2>Controle em cada etapa.</h2>
        <div class="process-grid">
            <div><strong>01</strong><span>Desenvolvimento</span></div>
            <div><strong>02</strong><span>Produção</span></div>
            <div><strong>03</strong><span>Envase</span></div>
            <div><strong>04</strong><span>Selagem</span></div>
            <div><strong>05</strong><span>Qualidade</span></div>
            <div><strong>06</strong><span>Distribuição</span></div>
        </div>
    </div>
</section>

<section class="section b2b-section">
    <div class="container b2b-card">
        <div>
            <span class="eyebrow gold">PARA O SEU NEGÓCIO</span>
            <h2>Uma apresentação feita para chamar atenção no ponto de venda.</h2>
            <p>Adegas, conveniências, empórios, distribuidoras, bares e eventos podem falar diretamente com a 1 Dose para conhecer as condições comerciais.</p>
            <a href="contato.php" class="btn btn-primary">Falar sobre revenda</a>
        </div>
        <img src="assets/img/linha-sabores.jpg" alt="Linha de sabores 1 Dose">
    </div>
</section>
<?php require 'footer.php'; ?>
