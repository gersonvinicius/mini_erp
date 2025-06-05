<?php

$title = 'Gerenciamento de Produtos';
ob_start();
?>

<h1 class="mb-4">Gerenciamento de Produtos</h1>

<!-- Formulário para Cadastro/Atualização de Produtos -->
<form id="form-produto" method="POST" class="mb-5">
    <input type="hidden" id="produto-id" name="id"> <!-- Campo oculto para ID do Produto (usado na edição) -->

    <!-- Informações Básicas do Produto -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Informações do Produto</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="nome" class="form-label">Nome do Produto:</label>
                    <input type="text" id="nome" name="nome" class="form-control" placeholder="Ex.: Camiseta" required>
                </div>
                <div class="col-md-6">
                    <label for="preco" class="form-label">Preço Base (R$):</label>
                    <input type="number" id="preco" name="preco" class="form-control" step="0.01" required>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Variações -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Variações do Produto</h5>
        </div>
        <div class="card-body">
            <p>Adicione variações e controle seus estoques e preços adicionais.</p>
            <table class="table table-bordered" id="tabela-variacoes">
                <thead class="table-light">
                    <tr>
                        <th>Nome da Variação</th>
                        <th>Estoque</th>
                        <th>Preço Adicional (R$)</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Linhas de variações serão adicionadas aqui dinamicamente -->
                </tbody>
            </table>
            <button type="button" id="adicionar-variacao" class="btn btn-success">Adicionar Variação</button>
        </div>
    </div>

    <!-- Botão Salvar -->
    <div class="text-end">
        <button type="submit" class="btn btn-primary">Salvar Produto</button>
    </div>
</form>

<!-- Tabela para Listagem de Produtos -->
<div class="card">
    <div class="card-header bg-dark text-white">
        <h5 class="mb-0">Lista de Produtos</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Preço</th>
                    <th>Estoque Total</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody id="produtos-lista">
                <!-- Os produtos serão carregados aqui via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<script id="produto-template" type="text/template">
    <tr>
        <td>{id}</td>
        <td>{nome}</td>
        <td>R$ {preco_base}</td>
        <td>{estoque_total}</td>
        <td>
            <button class="btn btn-warning btn-sm btn-editar" data-id="{id}">Editar</button>
            <button class="btn btn-danger btn-sm btn-excluir" data-id="{id}">Excluir</button>
            <button class="btn btn-success btn-sm btn-comprar" data-id="{id}" data-nome="{nome}" data-preco="{preco_base}">Comprar</button>
        </td>
    </tr>
</script>

<!-- Carrinho Fixo -->
<div id="carrinho" class="fixed top-0 right-0 w-80 bg-white shadow-lg hidden">
    <!-- Cabeçalho do Carrinho -->
    <div class="flex justify-between items-center bg-gray-800 text-white p-3">
        <h2 class="text-lg font-bold">Carrinho</h2>
        <button id="fechar-carrinho" class="text-red-500 text-xl">✖</button>
    </div>

    <!-- Itens no Carrinho -->
    <div class="p-3">
        <h3 class="text-md font-semibold mb-3">Itens no carrinho:</h3>
        <div id="lista-carrinho" class="space-y-3">
            <!-- Os itens serão gerados dinamicamente -->
        </div>

        <!-- Cupom -->
        <div class="mt-4">
            <label for="cupom-codigo" class="block text-sm font-medium text-gray-700">Cupom:</label>
            <input type="text" id="cupom-codigo" class="form-input mt-1 w-full border-gray-300">
            <button id="aplicar-cupom" class="mt-2 w-full bg-blue-600 text-white p-2 rounded">Aplicar</button>
        </div>

        <div class="mt-4">
            <label for="cep" class="block text-sm font-medium text-gray-700">CEP:</label>
            <input type="text" id="cliente-cep" class="form-input mt-1 w-full border-gray-300" placeholder="Digite o CEP">
            <button id="validar-cep" class="mt-2 w-full bg-green-600 text-white p-2 rounded">Calcular</button>
        </div>

        <!-- Campo para o endereço (aparece após validar o CEP) -->
        <div id="endereco-cliente" class="mt-4 hidden">
            <label class="block text-sm font-medium text-gray-700">Endereço:</label>
            <p id="endereco-resultado" class="text-sm text-gray-800 mt-1"></p>
        </div>

        <!-- Campos de Nome e Email -->
        <div class="mt-4">
            <label for="cliente-nome" class="block text-sm font-medium text-gray-700">Nome:</label>
            <input type="text" id="cliente-nome" class="form-input mt-1 w-full border-gray-300">
            <br>
            <label for="cliente-email" class="block text-sm font-medium text-gray-700 mt-4">Email:</label>
            <input type="email" id="cliente-email" class="form-input mt-1 w-full border-gray-300">
        </div>

        <!-- Resumo -->
        <div class="mt-4 border-t pt-3">
            <div class="flex justify-between text-sm">
                <span>Desconto:</span>
                <span id="total-desconto">R$ 0,00</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Frete:</span>
                <span id="frete">R$ 0,00</span>
            </div>
            <div class="flex justify-between text-sm">
                <span>Subtotal:</span>
                <span id="subtotal">R$ 0,00</span>
            </div>
        </div>

        <!-- Finalizar Pedido -->
        <button id="finalizar-compra" class="mt-4 w-full bg-blue-600 text-white p-3 rounded">Finalizar</button>
        <button id="limpar-carrinho" class="mt-2 w-full bg-red-600 text-white p-3 rounded">
            Limpar Carrinho
        </button>
    </div>
</div>

<!-- Botão para Expandir o Carrinho -->
<button id="abrir-carrinho" class="fixed top-5 right-5 bg-blue-600 text-white p-3 rounded-full shadow-lg">
    🛒
</button>

<div id="modal-variacao" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-5 rounded shadow-lg w-96">
        <h3 class="text-lg font-bold mb-3">Selecione uma Variação</h3>
        <div id="lista-variacoes" class="space-y-3"></div>
        <button id="add-variacao" class="mt-4 bg-green-600 text-white p-2 rounded w-full">Adicionar ao Carrinho</button>
    </div>
</div>

<?php
$content = ob_get_clean();
$scripts = '<script src="/assets/js/produtos.js"></script>';
include_once APPPATH . 'views/layouts/main.php';
?>