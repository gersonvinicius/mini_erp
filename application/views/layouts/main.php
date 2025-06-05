<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'Mini ERP' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Carrinho */
        #carrinho {
            position: fixed;
            top: 0;
            right: 0;
            width: 400px; /* Largura do carrinho */
            height: 100%; /* Altura total da tela */
            background-color: white; /* Fundo branco */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Sombra */
            z-index: 1000; /* Garante que o carrinho esteja acima de outros elementos */
            transform: translateX(100%); /* Inicialmente fora da tela */
            transition: transform 0.3s ease-in-out; /* Animação para entrada */
            padding: 10px; /* Espaçamento interno */
        }

        #carrinho.show {
            transform: translateX(0); /* Mostra o carrinho */
        }

        /* Botões no Carrinho */
        #carrinho button {
            font-size: 14px; /* Tamanho da fonte dos botões */
            padding: 8px 10px; /* Espaçamento interno */
        }

        /* Estilo para cada item no carrinho */
        .item-carrinho {
            display: flex; /* Layout horizontal */
            justify-content: space-between; /* Espaço entre as seções */
            align-items: center; /* Alinha os itens verticalmente */
            padding: 10px 0; /* Espaçamento interno */
            border-bottom: 1px solid #e5e7eb; /* Linha separadora */
        }

        /* Estilo para os controles de quantidade */
        .controle-quantidade {
            display: flex; /* Layout horizontal */
            align-items: center; /* Centraliza os itens verticalmente */
            gap: 5px; /* Espaçamento entre os botões */
        }

        /* Botões no carrinho */
        .remover-item,
        .alterar-quantidade {
            font-size: 14px; /* Tamanho do texto/ícone */
            width: 30px; /* Largura dos botões */
            height: 30px; /* Altura dos botões */
            display: flex; /* Flexbox para centralizar o conteúdo */
            align-items: center; /* Centraliza verticalmente */
            justify-content: center; /* Centraliza horizontalmente */
            background-color: #2563eb; /* Cor de fundo azul */
            color: white; /* Cor do texto branca */
            border: none; /* Remove bordas padrão */
            border-radius: 4px; /* Bordas arredondadas */
            cursor: pointer; /* Define o cursor como ponteiro */
            padding: 0; /* Remove padding interno */
        }

        /* Alteração de cor no hover */
        .remover-item:hover,
        .alterar-quantidade:hover {
            background-color: #1d4ed8; /* Azul mais escuro no hover */
        }

        #carrinho .item-carrinho {
            display: flex; /* Usa Flexbox para layout horizontal */
            justify-content: space-between; /* Distribui os itens horizontalmente */
            align-items: center; /* Centraliza os itens verticalmente */
            padding: 10px 0; /* Espaçamento interno */
            border-bottom: 1px solid #e5e7eb; /* Linha para separar os itens */
        }

        /* Botão de Abrir o Carrinho */
        #abrir-carrinho {
            z-index: 1001; /* Garante que o botão esteja acima do carrinho */
            position: fixed;
            top: 5%; /* Posição no topo */
            right: 5%; /* Posição na lateral direita */
            background-color: #2563eb; /* Fundo azul */
            color: white; /* Texto branco */
            padding: 15px; /* Espaçamento interno */
            border-radius: 50%; /* Botão circular */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2); /* Sombra */
            cursor: pointer; /* Mouse como ponteiro */
        }

        /* Botões Gerais */
        button {
            color: #fff; /* Cor do texto */
            background-color: #2563eb; /* Fundo azul */
            border: none; /* Remove bordas padrão */
            padding: 8px 12px; /* Espaçamento interno */
            border-radius: 4px; /* Bordas arredondadas */
            cursor: pointer; /* Mouse como ponteiro */
        }

        button:hover {
            background-color: #1d4ed8; /* Azul mais escuro no hover */
        }

        button:disabled {
            background-color: #d1d5db; /* Fundo cinza para botões desabilitados */
            color: #9ca3af; /* Texto cinza claro */
            cursor: not-allowed; /* Cursor de bloqueado */
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- Renderiza o conteúdo da view -->
        <?= isset($content) ? $content : '' ?>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Scripts Específicos -->
    <?= isset($scripts) ? $scripts : '' ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- InputMask JS -->
    <script src="https://cdn.jsdelivr.net/npm/inputmask@5.0.9/dist/jquery.inputmask.min.js"></script>
</body>
</html>