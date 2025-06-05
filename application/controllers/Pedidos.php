<?php

class Pedidos extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('CupomModel');
        $this->load->model('PedidoModel');
    }

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
        try {
            $this->load->model('PedidoModel');
            $this->load->model('CupomModel'); // Carrega a model de Cupom

            $dados = $this->input->post();
            $carrinho = $dados['carrinho'] ?? [];
            $clienteNome = $dados['cliente_nome'] ?? '';
            $clienteEmail = $dados['cliente_email'] ?? '';
            $clienteCep = $dados['cliente_cep'] ?? '';
            $subtotal = $dados['subtotal'] ?? 0;
            $cupomCodigo = $dados['cupom'] ?? ''; // Recebe o código do cupom enviado pelo frontend
            $frete = $this->calcularFrete($subtotal);
            $total = $subtotal + $frete;

            if (empty($carrinho) || count($carrinho) === 0) {
                echo json_encode(['erro' => 'Seu carrinho está vazio!']);
                return;
            }

            if (empty($clienteCep) || !preg_match('/^[0-9]{5}-?[0-9]{3}$/', $clienteCep)) {
                echo json_encode(['erro' => 'Por favor, preencha um CEP válido no formato 00000-000 ou 00000000.']);
                return;
            }

            if (empty($clienteNome)) {
                echo json_encode(['erro' => 'Por favor, preencha o nome do cliente.']);
                return;
            }

            if (empty($clienteEmail) || !filter_var($clienteEmail, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['erro' => 'Por favor, preencha um e-mail válido.']);
                return;
            }

            // Validação do CEP com a API ViaCEP
            $endereco = file_get_contents("https://viacep.com.br/ws/{$clienteCep}/json/");
            $endereco = json_decode($endereco, true);

            if (isset($endereco['erro'])) {
                echo json_encode(['erro' => 'CEP inválido.']);
                return;
            }

            // Verifica o cupom
            $cupom = null;
            if (!empty($cupomCodigo)) {
                $cupom = $this->CupomModel->obterPorCodigo($cupomCodigo);

                if (!$cupom) {
                    echo json_encode(['erro' => 'Cupom inválido.']);
                    return;
                }
            }

            // Cria o pedido
            $pedido = [
                'cliente_nome' => $clienteNome,
                'cliente_email' => $clienteEmail,
                'cliente_cep' => $clienteCep,
                'cliente_endereco' => "{$endereco['logradouro']}, {$endereco['bairro']}, {$endereco['localidade']}-{$endereco['uf']}",
                'subtotal' => $subtotal,
                'frete' => $frete,
                'total' => $total,
                'cupom_id' => $cupom['id'] ?? null,
                'status' => 'pendente'
            ];

            $pedido_id = $this->PedidoModel->salvarPedido($pedido);

            // Salvar itens do pedido
            foreach ($carrinho as $item) {
                $this->PedidoModel->salvarItem([
                    'pedido_id' => $pedido_id,
                    'produto_id' => $item['produtoId'],
                    'variacao_id' => $item['variacaoId'] ?? null,
                    'quantidade' => $item['quantidade'],
                    'preco' => $item['preco'],
                    'subtotal' => $item['subtotal']
                ]);
            }

            $enderecoFormatado = "{$endereco['logradouro']}, {$endereco['bairro']}, {$endereco['localidade']}-{$endereco['uf']}";
            $this->enviarEmailConfirmacao($clienteEmail, $clienteNome, $enderecoFormatado, $total);

            echo json_encode(['sucesso' => 'Pedido finalizado com sucesso']);
        } catch (Exception $e) {
            // Captura qualquer erro inesperado e retorna uma mensagem amigável
            log_message('error', 'Erro ao finalizar o pedido: ' . $e->getMessage());
            echo json_encode(['erro' => 'Ocorreu um erro inesperado ao processar o pedido. Por favor, tente novamente mais tarde.']);
        }
    }

    private function enviarEmailConfirmacao($clienteEmail, $clienteNome, $endereco, $total)
    {
        // Carrega a biblioteca de e-mail
        $this->load->library('email');

        // Configuração do Mailtrap
        $config = array(
            'protocol'    => 'smtp',
            'smtp_host'   => 'sandbox.smtp.mailtrap.io', // Servidor SMTP do Mailtrap
            'smtp_port'   => 2525, // Porta do Mailtrap
            'smtp_user'   => '04249aae5d6022', // Usuário SMTP
            'smtp_pass'   => '39a92a96096452', // Senha SMTP
            'smtp_crypto' => 'tls', // Protocolo de criptografia
            'mailtype'    => 'html', // Tipo do e-mail: HTML
            'charset'     => 'utf-8', // Codificação do conteúdo
            'wordwrap'    => TRUE, // Quebra automática de linhas
            'crlf'        => "\r\n", // Quebra de linha (compatível com SMTP)
            'newline'     => "\r\n", // Nova linha (compatível com SMTP)
        );

        // Inicializa a biblioteca de e-mail com as configurações
        $this->email->initialize($config);

        // Define os dados do e-mail
        $this->email->from('no-reply@erp.com', 'ERP'); // Remetente
        $this->email->to($clienteEmail); // Destinatário
        $this->email->subject('Confirmação do Pedido'); // Assunto
        $this->email->message("
            <h1>Obrigado por sua compra, {$clienteNome}!</h1>
            <p>Seu pedido foi recebido com sucesso.</p>
            <p><strong>Endereço de Entrega:</strong> {$endereco}</p>
            <p><strong>Total da Compra:</strong> R$ {$total}</p>
            <p>Atenciosamente,<br>Equipe ERP</p>
        ");

        // Tenta enviar o e-mail
        try {
            if (!$this->email->send()) {
                $erro = $this->email->print_debugger(); // Captura os detalhes do erro
                log_message('error', 'Erro ao enviar e-mail: ' . $erro); // Registra no log
                echo '<pre>' . $erro . '</pre>'; // Exibe o erro no navegador (para testes)
                return ['status' => false, 'erro' => $erro];
            }
            return ['status' => true];
        } catch (Exception $e) {
            log_message('error', 'Exceção ao enviar e-mail: ' . $e->getMessage());
            echo '<pre>Erro: ' . $e->getMessage() . '</pre>'; // Exibe o erro no navegador (para testes)
            return ['status' => false, 'erro' => $e->getMessage()];
        }
    }
}