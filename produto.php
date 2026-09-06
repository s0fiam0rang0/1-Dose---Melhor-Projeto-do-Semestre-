<?php
require 'config.php';
$id = (int)($_GET['id'] ?? 0);
$pdo = db();
$produto = null;

if ($pdo && $id) {
    $st = $pdo->prepare("SELECT * FROM produtos WHERE id=? AND ativo=1");
    $st->execute([$id]);
    $produto = $st->fetch();
} else {
    foreach (demo_products() as $item) {
        if ((int)$item['id'] === $id) { $produto = $item; break; }
    }
}

if (!$produto) {
    header('Location: produtos.php');
    exit;
}

$pageTitle = $produto['nome'];
require 'header.php';
?>
<section class="section product-detail-section">
    <div class="container product-detail">
        <div class="detail-photo"><img src="<?= e($produto['imagem']) ?>" alt="<?= e($produto['nome']) ?>"></div>
        <div class="detail-copy">
            <a href="produtos.php" class="back-link-site">← Todos os sabores</a>
            <span class="eyebrow orange">1 DOSE • <?= e($produto['volume']) ?></span>
            <h1><?= e($produto['nome']) ?></h1>
            <p><?= e($produto['descricao']) ?></p>
            <div class="detail-specs">
                <div><small>APRESENTAÇÃO</small><strong><?= e($produto['volume']) ?></strong></div>
                <div><small>GRADUAÇÃO</small><strong><?= e($produto['teor']) ?></strong></div>
                <div><small>LINHA</small><strong>1 Dose</strong></div>
            </div>
            <a href="contato.php" class="btn btn-primary">Tenho interesse</a>
        </div>
    </div>
</section>
<section class="section related-section">
    <div class="container">
        <div class="section-heading"><div><span class="eyebrow orange">CONTINUE EXPLORANDO</span><h2>Outros sabores</h2></div></div>
        <div class="mini-flavors">
            <?php foreach (demo_products() as $item): if ((int)$item['id'] === $id) continue; ?>
                <a href="produto.php?id=<?= (int)$item['id'] ?>"><img src="<?= e($item['imagem']) ?>" alt="<?= e($item['nome']) ?>"><span><?= e($item['nome']) ?></span></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php require 'footer.php'; ?>
