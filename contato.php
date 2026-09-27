<?php
// Número de WhatsApp da equipe de vendas
$numeroWhatsApp = "5511948643060";

// Mensagem que aparecerá automaticamente no WhatsApp
$mensagem = "Olá! Gostaria de mais informações sobre os produtos da 1 Dose.";

// Cria o link do WhatsApp
$linkWhatsApp = "https://wa.me/" . $numeroWhatsApp . "?text=" . urlencode($mensagem);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Fale Conosco</title>

    <style>
        .whatsapp-btn {
            display: inline-block;
            background-color: #25D366;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 8px;
            font-family: Arial, sans-serif;
            font-weight: bold;
        }

        .whatsapp-btn:hover {
            background-color: #1ebe5d;
        }
    </style>
</head>

<body>

    <a href="<?php echo $linkWhatsApp; ?>"
       class="whatsapp-btn"
       target="_blank"
       rel="noopener noreferrer">

        📞 Fale conosco!

    </a>

</body>

</html>