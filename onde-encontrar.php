<?php require 'config.php'; $pageTitle='Onde encontrar'; $pdo=db(); $pontos=$pdo ? $pdo->query("SELECT * FROM pontos_venda WHERE ativo=1 ORDER BY nome")->fetchAll() : []; require 'header.php'; ?>
<section class="page-hero"><div class="container"><span class="eyebrow gold">PONTOS DE VENDA</span><h1>Encontre a sua dose.</h1><p>Veja os parceiros cadastrados ou fale diretamente com a marca para localizar um ponto de venda.</p></div></section>
<section class="section"><div class="container location-grid">
<?php if($pontos): foreach($pontos as $p): ?>
<div class="location-card"><span class="location-icon">⌖</span><h3><?= e($p['nome']) ?></h3><p><?= e($p['endereco']) ?></p><?php if(!empty($p['cidade'])): ?><small><?= e($p['cidade']) ?></small><?php endif; ?></div>
<?php endforeach; else: ?>
<div class="empty-state"><h3>Pontos de venda serão adicionados em breve.</h3><p>Enquanto isso, fale diretamente com a 1 Dose.</p><a href="contato.php" class="btn btn-primary">Entrar em contato</a></div>
<?php endif; ?>
</div></section>
<?php require 'footer.php'; ?>
