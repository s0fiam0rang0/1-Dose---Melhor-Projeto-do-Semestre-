<?php

require '../config.php';
require_admin();

$pdo = db();

if (!$pdo) {
    die('Banco não configurado.');
}

$id = (int)($_GET['id'] ?? 0);

$p = [
    'nome' => '',
    'descricao' => '',
    'volume' => '',
    'teor' => '',
    'imagem' => '',
    'ativo' => 1
];


// Se estiver editando, busca o produto
if ($id) {

    $st = $pdo->prepare(
        'SELECT * FROM produtos WHERE id = ?'
    );

    $st->execute([$id]);

    $produtoEncontrado = $st->fetch();

    if (!$produtoEncontrado) {
        header('Location: index.php');
        exit;
    }

    $p = $produtoEncontrado;
}


$erro = '';


// Recebe o formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Proteção CSRF
    if (!csrf_verify()) {

        $erro = 'Não foi possível validar a solicitação. Tente novamente.';

    } else {

        // Mantém a imagem antiga caso nenhuma nova seja enviada
        $imagem = $p['imagem'];


        // Verifica se uma nova imagem foi enviada
        if (
            isset($_FILES['imagem']) &&
            $_FILES['imagem']['error'] !== UPLOAD_ERR_NO_FILE
        ) {

            if ($_FILES['imagem']['error'] === UPLOAD_ERR_OK) {

                $arquivo = $_FILES['imagem'];

                // Tamanho máximo: 5 MB
                $tamanhoMaximo = 5 * 1024 * 1024;

                if ($arquivo['size'] > $tamanhoMaximo) {

                    $erro = 'A imagem deve possuir no máximo 5 MB.';

                } else {

                    // Descobre a extensão informada no arquivo
                    $extensao = strtolower(
                        pathinfo(
                            $arquivo['name'],
                            PATHINFO_EXTENSION
                        )
                    );

                    $extensoesPermitidas = [
                        'jpg',
                        'jpeg',
                        'png',
                        'webp'
                    ];


                    // Verifica a extensão
                    if (
                        !in_array(
                            $extensao,
                            $extensoesPermitidas,
                            true
                        )
                    ) {

                        $erro =
                            'Formato de imagem não permitido. Use JPG, JPEG, PNG ou WEBP.';

                    } else {

                        /*
                         * Verifica o tipo REAL do arquivo.
                         * Não confia apenas na extensão.
                         */

                        if (!function_exists('finfo_open')) {

                            $erro =
                                'Não foi possível validar o tipo da imagem.';

                        } else {

                            $finfo = finfo_open(FILEINFO_MIME_TYPE);

                            $tipoMime = finfo_file(
                                $finfo,
                                $arquivo['tmp_name']
                            );

                            finfo_close($finfo);


                            $tiposPermitidos = [
                                'image/jpeg' => [
                                    'jpg',
                                    'jpeg'
                                ],

                                'image/png' => [
                                    'png'
                                ],

                                'image/webp' => [
                                    'webp'
                                ]
                            ];


                            // Verifica se o MIME é realmente de imagem permitida
                            if (
                                !isset(
                                    $tiposPermitidos[$tipoMime]
                                )
                            ) {

                                $erro =
                                    'O arquivo enviado não é uma imagem válida.';

                            } elseif (
                                !in_array(
                                    $extensao,
                                    $tiposPermitidos[$tipoMime],
                                    true
                                )
                            ) {

                                /*
                                 * Exemplo:
                                 * arquivo PNG renomeado manualmente para .jpg
                                 */

                                $erro =
                                    'A extensão do arquivo não corresponde ao tipo real da imagem.';

                            } else {

                                $pasta =
                                    '../assets/img/produtos/';


                                // Cria a pasta caso ela não exista
                                if (!is_dir($pasta)) {

                                    if (
                                        !mkdir(
                                            $pasta,
                                            0755,
                                            true
                                        )
                                    ) {

                                        $erro =
                                            'Não foi possível criar a pasta de imagens.';
                                    }
                                }


                                if (!$erro) {

                                    /*
                                     * Gera um nome aleatório.
                                     * Não utiliza o nome original
                                     * enviado pelo usuário.
                                     */

                                    $nomeArquivo =
                                        'produto_' .
                                        time() .
                                        '_' .
                                        bin2hex(
                                            random_bytes(8)
                                        ) .
                                        '.' .
                                        $extensao;


                                    $destino =
                                        $pasta .
                                        $nomeArquivo;


                                    // Salva a imagem
                                    if (
                                        move_uploaded_file(
                                            $arquivo['tmp_name'],
                                            $destino
                                        )
                                    ) {

                                        $imagem =
                                            'assets/img/produtos/' .
                                            $nomeArquivo;

                                    } else {

                                        $erro =
                                            'Não foi possível salvar a imagem.';
                                    }
                                }
                            }
                        }
                    }
                }

            } else {

                $erro =
                    'Erro ao enviar a imagem.';
            }
        }


        // Se não ocorreu erro, salva os dados
        if (!$erro) {

            $nome =
                trim($_POST['nome'] ?? '');

            $descricao =
                trim($_POST['descricao'] ?? '');

            $volume =
                trim($_POST['volume'] ?? '');

            $teor =
                trim($_POST['teor'] ?? '');

            $ativo =
                isset($_POST['ativo'])
                    ? 1
                    : 0;


            // Nome e descrição obrigatórios
            if (
                $nome === '' ||
                $descricao === ''
            ) {

                $erro =
                    'Preencha o nome e a descrição do produto.';

            } else {

                $dados = [
                    $nome,
                    $descricao,
                    $volume,
                    $teor,
                    $imagem,
                    $ativo
                ];


                // Edita produto existente
                if ($id) {

                    $st = $pdo->prepare(
                        'UPDATE produtos
                         SET nome = ?,
                             descricao = ?,
                             volume = ?,
                             teor = ?,
                             imagem = ?,
                             ativo = ?
                         WHERE id = ?'
                    );

                    $dados[] = $id;

                    $st->execute($dados);

                } else {

                    // Cadastra novo produto
                    $st = $pdo->prepare(
                        'INSERT INTO produtos
                        (
                            nome,
                            descricao,
                            volume,
                            teor,
                            imagem,
                            ativo
                        )
                        VALUES (?, ?, ?, ?, ?, ?)'
                    );

                    $st->execute($dados);
                }


                header('Location: index.php');
                exit;
            }
        }
    }
}

?>

<!doctype html>

<html lang="pt-BR">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        <?= $id ? 'Editar' : 'Novo' ?> produto | 1 Dose
    </title>

    <link
        rel="stylesheet"
        href="../assets/css/style.css"
    >

</head>


<body class="admin-body">

<div class="form-card">

    <a
        class="back-link"
        href="index.php"
    >
        ← Voltar
    </a>


    <h1>
        <?= $id ? 'Editar' : 'Cadastrar' ?> produto
    </h1>


    <?php if ($erro): ?>

        <p
            style="
                padding:12px;
                margin:15px 0;
                border-radius:8px;
                background:#ffe5e5;
                color:#8b0000;
            "
        >
            <?= e($erro) ?>
        </p>

    <?php endif; ?>


    <form
        method="post"
        enctype="multipart/form-data"
    >

        <?= csrf_field() ?>


        <label>

            Nome

            <input
                name="nome"
                value="<?= e($p['nome']) ?>"
                required
            >

        </label>


        <label>

            Descrição

            <textarea
                name="descricao"
                rows="5"
                required
            ><?= e($p['descricao']) ?></textarea>

        </label>


        <div class="form-row">

            <label>

                Volume

                <input
                    name="volume"
                    value="<?= e($p['volume']) ?>"
                    placeholder="Ex.: 50 ml"
                >

            </label>


            <label>

                Teor alcoólico

                <input
                    name="teor"
                    value="<?= e($p['teor']) ?>"
                    placeholder="Ex.: 20% vol."
                >

            </label>

        </div>


        <label>

            Imagem do produto

            <input
                type="file"
                name="imagem"
                accept="image/jpeg,image/png,image/webp"
            >

        </label>


        <small>
            Formatos permitidos: JPG, PNG ou WEBP.
            Máximo de 5 MB.
        </small>


        <?php if (!empty($p['imagem'])): ?>

            <div style="margin:15px 0;">

                <p>
                    Imagem atual:
                </p>

                <img
                    src="../<?= e($p['imagem']) ?>"
                    alt="<?= e($p['nome']) ?>"
                    style="
                        max-width:200px;
                        width:100%;
                        height:auto;
                        border-radius:10px;
                    "
                >

            </div>

        <?php endif; ?>


        <label class="check">

            <input
                type="checkbox"
                name="ativo"
                <?= $p['ativo'] ? 'checked' : '' ?>
            >

            Exibir no site

        </label>


        <button
            class="btn btn-primary"
            type="submit"
        >
            Salvar produto
        </button>

    </form>

</div>

</body>

</html>