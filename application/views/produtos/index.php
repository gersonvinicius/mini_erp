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
            <p>Adicione variações e controle seus estoques.</p>
            <table class="table table-bordered" id="tabela-variacoes">
                <thead class="table-light">
                    <tr>
                        <th>Nome da Variação</th>
                        <th>Estoque</th>
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

<?php
$content = ob_get_clean();
$scripts = '<script src="/assets/js/produtos.js"></script>';
include_once APPPATH . 'views/layouts/main.php';
?>