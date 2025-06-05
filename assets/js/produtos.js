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
                                <td><input type="number" name="variacoes[preco_adicional][]" class="form-control" step="0.01" value="${variacao.preco_adicional}" required></td>
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
                <td><input type="number" name="variacoes[preco_adicional][]" class="form-control" step="0.01" placeholder="Preço Adicional" required></td>
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

    // CARRINHO

    let carrinhoItens = JSON.parse(sessionStorage.getItem('carrinho')) || [];

    try {
        // Tenta carregar os itens do sessionStorage
        const carrinhoSalvo = JSON.parse(sessionStorage.getItem('carrinho'));
        if (Array.isArray(carrinhoSalvo)) {
            carrinhoItens = carrinhoSalvo; // Apenas se for um array
        }
    } catch (e) {
        console.warn('Erro ao carregar carrinho do sessionStorage:', e);
        carrinhoItens = []; // Define carrinho vazio em caso de erro
    }

    // Função para carregar produtos
    function carregarProdutos() {
        $.ajax({
            url: '/produtos/listar',
            method: 'GET',
            success: function (data) {
                $('#produtos-lista').html(data); // Renderiza a tabela de produtos
            },
            error: function () {
                alert('Erro ao carregar a lista de produtos.');
            }
        });
    }

    // Atualizar o carrinho na interface
    function atualizarCarrinho() {
        // Garante que o carrinho seja sempre um array
        if (!Array.isArray(carrinhoItens)) {
            carrinhoItens = [];
        }
    
        const lista = $('#lista-carrinho');
        lista.empty(); // Limpa a lista de itens do carrinho
        let subtotal = 0;
    
        // Se o carrinho estiver vazio, exibe uma mensagem
        if (carrinhoItens.length === 0) {
            lista.append('<p class="text-center text-gray-500">Seu carrinho está vazio.</p>');
            $('#subtotal').text('R$ 0,00');
            $('#frete').text('R$ 0,00');
            return; // Sai da função
        }
    
        // Renderiza os itens do carrinho
        carrinhoItens.forEach((item, index) => {
            // Garante que o preço e o subtotal sejam valores numéricos
            const preco = parseFloat(item.preco) || 0;
            const subtotalItem = preco * item.quantidade;
        
            subtotal += subtotalItem;
        
            lista.append(`
                <div class="item-carrinho flex justify-between items-center border-b pb-2 text-sm">
                    <!-- Informações do Produto -->
                    <div>
                        <h6 class="font-bold">${item.nome} - ${item.variacaoNome}</h6>
                        <p>R$ ${preco.toFixed(2)} x ${item.quantidade}</p>
                    </div>
                    <!-- Controles de Quantidade -->
                    <div class="controle-quantidade">
                        <button class="alterar-quantidade" data-index="${index}" data-acao="diminuir">−</button>
                        <span>${item.quantidade}</span>
                        <button class="alterar-quantidade" data-index="${index}" data-acao="aumentar">+</button>
                        <button class="remover-item" data-index="${index}">✖</button>
                    </div>
                </div>
            `);
        });
    
        // Calcula o frete e o total
        const frete = calcularFrete(subtotal);
        const total = subtotal + frete;
    
        // Atualiza os valores na interface
        $('#subtotal').text(`R$ ${subtotal.toFixed(2)}`);
        $('#frete').text(`R$ ${frete.toFixed(2)}`);
        $('#total-desconto').text('R$ 0,00'); // Exemplo: pode ser ajustado para cupom de desconto
        sessionStorage.setItem('carrinho', JSON.stringify(carrinhoItens));
    }

    // Calcular frete com base no subtotal
    function calcularFrete(subtotal) {
        if (subtotal > 200) return 0; // Frete grátis para pedidos acima de R$ 200
        if (subtotal >= 52 && subtotal <= 166.59) return 15; // Valor fixo
        return 20; // Frete padrão
    }

    let variacaoSelecionada = null;

    // Quando o modal de variações é aberto
    $(document).on('click', '.btn-comprar', function () {
        const produtoId = $(this).data('id');
        const produtoNome = $(this).data('nome');

        variacaoSelecionada = {
            produtoId: produtoId,
            produtoNome: produtoNome
        };

        // Obter variações via AJAX
        $.get(`/produtos/variacoes/${produtoId}`, function (variacoes) {
            const lista = $('#lista-variacoes');
            lista.empty();

            variacoes.forEach(variacao => {
                const precoAdicional = parseFloat(variacao.preco_adicional) || 0;

                lista.append(`
                    <div class="flex items-center">
                        <input type="radio" name="variacao" value="${variacao.id}" data-nome="${variacao.nome}" data-preco="${precoAdicional}">
                        <label for="variacao-${variacao.id}" class="ml-2">${variacao.nome} (R$ ${precoAdicional.toFixed(2)})</label>
                    </div>
                `);
            });

            $('#modal-variacao').removeClass('hidden'); // Exibe o modal
        }).fail(function () {
            alert('Erro ao carregar as variações. Tente novamente.');
        });
    });

    // Adicionar variação ao carrinho
    $('#add-variacao').on('click', function () {
        const radioSelecionado = $('input[name="variacao"]:checked');
        if (!radioSelecionado.length) {
            alert('Selecione uma variação!');
            return;
        }
    
        const variacaoId = radioSelecionado.val();
        const variacaoNome = radioSelecionado.data('nome');
        const precoAdicional = parseFloat(radioSelecionado.data('preco')) || 0;
    
        const itemExistente = carrinhoItens.find(
            item => item.produtoId === variacaoSelecionada.produtoId && item.variacaoId === variacaoId
        );
    
        if (itemExistente) {
            itemExistente.quantidade += 1;
            itemExistente.subtotal = itemExistente.quantidade * itemExistente.preco;
        } else {
            carrinhoItens.push({
                produtoId: variacaoSelecionada.produtoId,
                nome: variacaoSelecionada.produtoNome,
                variacaoId,
                variacaoNome,
                preco: precoAdicional,
                quantidade: 1,
                subtotal: precoAdicional
            });
        }
    
        sessionStorage.setItem('carrinho', JSON.stringify(carrinhoItens)); // Atualiza o carrinho no armazenamento
        atualizarCarrinho(); // Atualiza a interface do carrinho
        $('#modal-variacao').addClass('hidden'); // Fecha o modal
        alert('Produto adicionado ao carrinho!');
    });

    // Alterar quantidade de itens no carrinho
    $(document).on('click', '.alterar-quantidade', function () {
        const index = $(this).data('index');
        const acao = $(this).data('acao');

        if (acao === 'aumentar') carrinhoItens[index].quantidade += 1;
        if (acao === 'diminuir' && carrinhoItens[index].quantidade > 1) carrinhoItens[index].quantidade -= 1;

        carrinhoItens[index].subtotal = carrinhoItens[index].quantidade * carrinhoItens[index].preco;
        atualizarCarrinho();
    });

    // Remover item do carrinho
    $(document).on('click', '.remover-item', function () {
        const index = $(this).data('index');
        carrinhoItens.splice(index, 1);
        sessionStorage.setItem('carrinho', JSON.stringify(carrinhoItens)); 
        atualizarCarrinho();
    });

    // Inicializar carrinho e carregar produtos
    carregarProdutos();
    atualizarCarrinho();

    // Limpar o carrinho
    $('#limpar-carrinho').on('click', function () {
        if (confirm('Tem certeza de que deseja limpar o carrinho?')) {
            carrinhoItens = []; // Reseta o carrinho
            sessionStorage.setItem('carrinho', JSON.stringify(carrinhoItens)); // Atualiza o sessionStorage
            atualizarCarrinho(); // Atualiza a interface
            $('#cupom-codigo').val('');
            $('#cliente-cep').val('');
            $('#cliente-email').val('');
            $('#cliente-nome').val('');
            $('#frete').text('R$ 0,00');
            $('#total-desconto').text('R$ 0,00');
            alert('Carrinho limpo com sucesso!');
        }
    });

    $('#validar-cep').on('click', function () {
        const cep = $('#cliente-cep').val().trim();
    
        if (!cep) {
            alert('Por favor, insira um CEP.');
            return;
        }
    
        // Consulta o endereço no ViaCEP
        $.ajax({
            url: `https://viacep.com.br/ws/${cep}/json/`,
            method: 'GET',
            success: function (endereco) {
                if (endereco.erro) {
                    alert('CEP inválido.');
                    return;
                }
    
                // Exibe o endereço
                $('#endereco-resultado').text(
                    `${endereco.logradouro}, ${endereco.bairro}, ${endereco.localidade} - ${endereco.uf}`
                );
                $('#endereco-cliente').removeClass('hidden');
            },
            error: function () {
                alert('Erro ao consultar o CEP. Tente novamente.');
            }
        });
    });

    $('#finalizar-compra').on('click', function () {
        if (carrinhoItens.length === 0) {
            alert('Seu carrinho está vazio!');
            return;
        }
    
        const clienteNome = $('#cliente-nome').val().trim();
        const clienteEmail = $('#cliente-email').val().trim();
        const clienteCep = $('#cliente-cep').val().trim();
        const cupom = $('#cupom-codigo').val().trim();
        const subtotal = carrinhoItens.reduce((acc, item) => acc + item.preco * item.quantidade, 0);
    
        if (!clienteNome || !clienteEmail || !clienteCep) {
            alert('Por favor, preencha todos os campos do cliente.');
            return;
        }
    
        // Envia os dados do pedido para o backend
        $.ajax({
            url: '/pedidos/finalizar', // Rota do método `finalizar` no controller
            method: 'POST',
            data: {
                cliente_nome: clienteNome,
                cliente_email: clienteEmail,
                cliente_cep: clienteCep,
                subtotal: subtotal,
                carrinho: carrinhoItens,
                cupom: cupom
            },
            success: function (response) {
                const data = JSON.parse(response);
    
                if (data.erro) {
                    alert(data.erro);
                    return;
                }
    
                // Exibe mensagem de sucesso
                alert('Compra efetuada com SUCESSO!');
    
                // Limpa o carrinho
                carrinhoItens = [];
                $('#lista-carrinho').empty();
                $('#lista-carrinho').append('<p class="text-center text-gray-500">Seu carrinho está vazio.</p>');
                $('#subtotal').text('R$ 0,00');
                $('#frete').text('R$ 0,00');
                $('#cliente-cep').val('');
                $('#cliente-email').val('');
                $('#cliente-nome').val('');
                $('#total-desconto').text('R$ 0,00');
            },
            error: function () {
                alert('Ocorreu um erro ao finalizar o pedido. Tente novamente.');
            }
        });
    });

    // Abrir o carrinho
    $('#abrir-carrinho').on('click', function () {
        $('#carrinho').addClass('show'); // Adiciona a classe que exibe o carrinho
    });
    
    $('#fechar-carrinho').on('click', function () {
        $('#carrinho').removeClass('show'); // Remove a classe para ocultar o carrinho
    });

    $('#aplicar-cupom').on('click', function () {
        const codigoCupom = $('#cupom-codigo').val().trim();
        const subtotal = carrinhoItens.reduce((acc, item) => acc + (item.preco * item.quantidade), 0);
    
        if (!codigoCupom) {
            alert('Digite um código de cupom.');
            return;
        }
    
        // Envia o cupom ao backend via AJAX
        $.ajax({
            url: '/cupons/validar', // Rota do método validar no controller Cupons
            method: 'POST',
            data: { codigo: codigoCupom, subtotal: subtotal },
            success: function (response) {
                const data = JSON.parse(response);
    
                if (data.error) {
                    alert(data.error);
                    $('#total-desconto').text('R$ 0,00'); // Reseta o desconto
                    return;
                }
    
                // Aplica o desconto ao carrinho
                const desconto = parseFloat(data.desconto);
                const totalComDesconto = subtotal - desconto;
    
                $('#total-desconto').text(`- R$ ${desconto.toFixed(2)}`);
                $('#subtotal').text(`R$ ${totalComDesconto.toFixed(2)}`);
                alert('Cupom aplicado com sucesso!');
            },
            error: function () {
                alert('Erro ao validar o cupom. Tente novamente.');
            }
        });
    });
});