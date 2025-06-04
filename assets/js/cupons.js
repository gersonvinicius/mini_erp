$(document).ready(function () {
    function carregarCupons() {
        $.ajax({
            url: '/cupons/listar',
            method: 'GET',
            success: function (data) {
                $('#cupons-lista').html(data);
            },
            error: function () {
                alert('Erro ao carregar os cupons.');
            }
        });
    }

    // Carregar cupons ao abrir a página
    carregarCupons();

    // Preencher formulário para edição
    $(document).on('click', '.btn-editar', function () {
        const id = $(this).data('id');

        $.ajax({
            url: '/cupons/obter/' + id,
            method: 'GET',
            success: function (cupom) {
                cupom = JSON.parse(cupom);

                $('#cupom-id').val(cupom.id);
                $('#codigo').val(cupom.codigo);
                $('#valor_desconto').val(cupom.valor_desconto);
                $('#tipo_desconto').val(cupom.tipo_desconto);
                $('#valor_minimo').val(cupom.valor_minimo);
                $('#validade').val(cupom.validade);
            },
            error: function () {
                alert('Erro ao carregar os dados do cupom.');
            }
        });
    });

    // Remover cupom
    $(document).on('click', '.btn-excluir', function () {
        const id = $(this).data('id');

        if (confirm('Tem certeza que deseja excluir este cupom?')) {
            $.ajax({
                url: '/cupons/excluir/' + id,
                method: 'POST',
                success: function () {
                    alert('Cupom excluído com sucesso!');
                    carregarCupons();
                },
                error: function () {
                    alert('Erro ao excluir o cupom.');
                }
            });
        }
    });

    // Salvar cupom
    $('#form-cupom').on('submit', function (e) {
        e.preventDefault();

        const dados = $(this).serialize();

        $.ajax({
            url: '/cupons/salvar',
            method: 'POST',
            data: dados,
            success: function () {
                alert('Cupom salvo com sucesso!');
                carregarCupons();
                $('#form-cupom')[0].reset();
                $('#cupom-id').val('');
            },
            error: function () {
                alert('Erro ao salvar o cupom.');
            }
        });
    });
});