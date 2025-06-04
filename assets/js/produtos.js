$(document).ready(function () {
    // Função para carregar produtos
    function carregarProdutos() {
        $.ajax({
            url: '/produtos/listar', // Rota para listar produtos
            method: 'GET',
            success: function (data) {
                $('#produtos-lista').html(data); // Renderiza a tabela
            },
            error: function () {
                alert('Erro ao carregar a lista de produtos.');
            }
        });
    }

    // Carregar produtos ao abrir a página
    carregarProdutos();

    // Preencher formulário para edição
    $(document).on('click', '.btn-editar', function () {
        const id = $(this).data('id');
    
        $.ajax({
            url: '/produtos/obter/' + id, // Rota para buscar um produto
            method: 'GET',
            success: function (produto) {
                console.log('Produto retornado:', produto);
                produto = JSON.parse(produto);
    
                // Preenche os campos do formulário
                $('#produto-id').val(produto.produto_id);
                $('#nome').val(produto.nome);
                $('#preco').val(produto.preco_base);
    
                // Limpa a tabela de variações antes de carregar novamente
                $('#tabela-variacoes tbody').empty();
    
                // Preenche as variações, se existirem
                if (produto.variacoes && produto.variacoes.length > 0) {
                    produto.variacoes.forEach(variacao => {
                        const linha = `
                            <tr>
                                <td><input type="text" name="variacoes[nome][]" class="form-control" value="${variacao.nome}" required></td>
                                <td><input type="number" name="variacoes[estoque][]" class="form-control" value="${variacao.quantidade}" required></td>
                                <td><button type="button" class="btn btn-danger remover-variacao">Remover</button></td>
                            </tr>`;
                        $('#tabela-variacoes tbody').append(linha);
                    });
                }
            },
            error: function () {
                alert('Erro ao carregar os dados do produto.');
            }
        });
    });

    $('#adicionar-variacao').on('click', function () {
        const linha = `
            <tr>
                <td><input type="text" name="variacoes[nome][]" class="form-control" placeholder="Ex.: Tamanho P" required></td>
                <td><input type="number" name="variacoes[estoque][]" class="form-control" placeholder="Quantidade" required></td>
                <td><button type="button" class="btn btn-danger remover-variacao">Remover</button></td>
            </tr>`;
        $('#tabela-variacoes tbody').append(linha);
    });

    // Remover variação
    $(document).on('click', '.remover-variacao', function () {
        $(this).closest('tr').remove();
    });

    // Função para excluir produto
    $(document).on('click', '.btn-excluir', function () {
        const id = $(this).data('id');

        if (confirm('Tem certeza que deseja excluir este produto?')) {
            $.ajax({
                url: '/produtos/excluir/' + id, // Rota para exclusão
                method: 'POST',
                success: function () {
                    alert('Produto excluído com sucesso!');
                    carregarProdutos();
                },
                error: function () {
                    alert('Erro ao excluir o produto.');
                }
            });
        }
    });

    $('#form-produto').on('submit', function (e) {
        e.preventDefault();
    
        const dados = $(this).serialize();
    
        $.ajax({
            url: '/produtos/salvar',
            method: 'POST',
            data: dados,
            success: function () {
                alert('Produto salvo com sucesso!');
    
                // Resetar o formulário
                $('#form-produto')[0].reset();
    
                // Resetar a tabela de variações
                $('#tabela-variacoes tbody').empty();
    
                // Limpar o campo oculto do ID do produto
                $('#produto-id').val('');
    
                // Recarregar a lista de produtos
                carregarProdutos();
            },
            error: function () {
                alert('Erro ao salvar o produto.');
            }
        });
    });
});