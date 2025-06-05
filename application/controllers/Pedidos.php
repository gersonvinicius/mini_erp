<?php

class Pedidos extends CI_Controller
{
    public function calcularFrete($subtotal)
    {
        if ($subtotal > 200) {
            return 0.00; // Frete grátis
        } elseif ($subtotal >= 52 && $subtotal <= 166.59) {
            return 15.00; // Frete promocional
        } else {
            return 20.00; // Frete padrão
        }
    }

    public function finalizar()
    {
        $this->load->model('PedidoModel');

        $dados = $this->input->post();
        $carrinho = $dados['carrinho'];
        $subtotal = $dados['subtotal'];
        $frete = $this->calcularFrete($subtotal);
        $total = $subtotal + $frete;

        // Validação do CEP
        $cep = $dados['cliente_cep'];
        $endereco = file_get_contents("https://viacep.com.br/ws/{$cep}/json/");
        $endereco = json_decode($endereco, true);

        if (isset($endereco['erro'])) {
            echo json_encode(['erro' => 'CEP inválido']);
            return;
        }

        $pedido = [
            'cliente_nome' => $dados['cliente_nome'],
            'cliente_email' => $dados['cliente_email'],
            'cliente_cep' => $cep,
            'cliente_endereco' => "{$endereco['logradouro']}, {$endereco['bairro']}, {$endereco['localidade']}-{$endereco['uf']}",
            'subtotal' => $subtotal,
            'frete' => $frete,
            'total' => $total,
            'status' => 'pendente'
        ];

        $pedido_id = $this->PedidoModel->salvarPedido($pedido);

        // Salvar itens do pedido
        foreach ($carrinho as $item) {
            $this->PedidoModel->salvarItem([
                'pedido_id' => $pedido_id,
                'produto_id' => $item['id'],
                'variacao_id' => $item['variacao_id'] ?? null,
                'quantidade' => $item['quantidade'],
                'preco' => $item['preco'],
                'subtotal' => $item['subtotal']
            ]);
        }

        echo json_encode(['sucesso' => 'Pedido finalizado com sucesso']);
    }
}