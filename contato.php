<?php
require 'config.php'; $pageTitle='Contato'; $msg=''; $type='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $nome=trim($_POST['nome']??''); $email=trim($_POST['email']??''); $mensagem=trim($_POST['mensagem']??'');
    if($nome && filter_var($email,FILTER_VALIDATE_EMAIL) && $mensagem){
        $pdo=db();
        if($pdo){$st=$pdo->prepare("INSERT INTO contatos (nome,email,mensagem) VALUES (?,?,?)");$st->execute([$nome,$email,$mensagem]);$msg='Mensagem enviada com sucesso!';$type='success';}
        else {$msg='O site está em modo de demonstração. Configure o banco para gravar mensagens.';$type='info';}
    } else {$msg='Preencha nome, e-mail válido e mensagem.';$type='error';}
}
require 'header.php';
?>
<section class="page-hero"><div class="container"><span class="eyebrow gold">CONTATO E PARCERIAS</span><h1>Vamos colocar a 1 Dose no seu negócio?</h1><p>Use o formulário para falar sobre revenda, eventos, pontos de venda ou outras parcerias.</p></div></section>
<section class="section"><div class="container contact-grid">
<div class="contact-info"><span class="eyebrow orange">FALE COM A 1 DOSE</span><h2>Contato direto.</h2><p>Substitua os dados abaixo pelos contatos oficiais da empresa quando o cliente confirmar.</p><div class="contact-item"><strong>Instagram</strong><span>@1dose.cachaca</span></div><div class="contact-item"><strong>WhatsApp</strong><span>(11) 00000-0000</span></div><div class="contact-item"><strong>E-mail</strong><span>contato@1dose.com.br</span></div></div>
<form method="post" class="contact-form">
<?php if($msg): ?><div class="alert <?= e($type) ?>"><?= e($msg) ?></div><?php endif; ?>
<label>Nome<input type="text" name="nome" required></label><label>E-mail<input type="email" name="email" required></label><label>Mensagem<textarea name="mensagem" rows="6" required placeholder="Conte um pouco sobre seu interesse na 1 Dose"></textarea></label><button class="btn btn-primary" type="submit">Enviar mensagem</button>
</form>
</div></section>
<?php require 'footer.php'; ?>
