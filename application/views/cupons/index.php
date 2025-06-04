<?php

$title = 'Gerenciamento de Cupons';
ob_start();
?>

<h1 class="mb-4">Gerenciamento de Cupons</h1>

<!-- Formulário para Cadastro/Atualização de Cupons -->
<form id="form-cupom" method="POST" class="mb-5">
    <input type="hidden" id="cupom-id" name="id"> <!-- Campo oculto para ID do Cupom -->

    <div class="mb-3">
        <label for="codigo" class="form-label">Código do Cupom:</label>
        <input type="text" id="codigo" name="codigo" class="form-control" required>
    </div>

    <div class="mb-3">
        <label for="valor_desconto" class="form-label">Desconto:</label>
        <input type="number" id="valor_desconto" name="valor_desconto" class="form-control" step="0.01" required>
    </div>

    <div class="mb-3">
        <label for="tipo" class="form-label">Tipo de Desconto:</label>
        <select id="tipo" name="tipo" class="form-select" required>
            <option value="fixo">Valor Fixo</option>
            <option value="percentual">Percentual</option>
        </select>
    </div>

    <div class="mb-3">
        <label for="valor_minimo" class="form-label">Valor Mínimo do Carrinho:</label>
        <input type="number" id="valor_minimo" name="valor_minimo" class="form-control" step="0.01">
    </div>

    <div class="mb-3">
        <label for="validade" class="form-label">Validade:</label>
        <input type="date" id="validade" name="validade" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Salvar Cupom</button>
</form>

<!-- Tabela para Listagem de Cupons -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Código</th>
            <th>Desconto</th>
            <th>Valor Mínimo</th>
            <th>Validade</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody id="cupons-lista">
        <!-- Os cupons serão carregados aqui via AJAX -->
    </tbody>
</table>

<?php
$content = ob_get_clean();
$scripts = '<script src="/assets/js/cupons.js"></script>';
include_once APPPATH . 'views/layouts/main.php';
?>