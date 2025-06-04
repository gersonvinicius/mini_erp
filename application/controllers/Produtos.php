<?php

class Produtos extends CI_Controller
{
    public function index()
    {
        // Carrega a view principal de produtos
        $this->load->view('produtos/index');
    }

    public function listar()
    {
        // Carrega o modelo
        $this->load->model('ProdutoModel');

        // Lista os produtos com o estoque
        $produtos = $this->ProdutoModel->listar();

        // Monta a tabela
        foreach ($produtos as $produto) {
            echo "<tr>
                    <td>{$produto['id']}</td>
                    <td>{$produto['nome']}</td>
                    <td>R$ {$produto['preco_base']}</td>
                    <td>{$produto['estoque_total']}</td>
                    <td>
                        <button class='btn btn-warning btn-sm btn-editar' data-id='{$produto['id']}'>Editar</button>
                        <button class='btn btn-danger btn-sm btn-excluir' data-id='{$produto['id']}'>Excluir</button>
                    </td>
                </tr>";
        }
    }

    public function salvar()
    {
        $this->load->model('ProdutoModel');

        // Dados do formulário
        $id = $this->input->post('id'); // ID do produto (para edição)
        $nome = $this->input->post('nome');
        $preco_base = $this->input->post('preco');
        $variacoes = $this->input->post('variacoes'); // Variações (array de nome e estoque)

        // Dados do produto
        $produto = [
            'nome' => $nome,
            'preco_base' => $preco_base,
        ];

        if ($id) {
            // Atualizar produto e variações
            $this->ProdutoModel->atualizar($id, $produto);
            $this->ProdutoModel->atualizarVariacoes($id, $variacoes);
        } else {
            // Criar produto e variações
            $produto_id = $this->ProdutoModel->salvar($produto);
            $this->ProdutoModel->salvarVariacoes($produto_id, $variacoes);
        }
    }

    public function obter($id)
    {
        // Carrega o modelo
        $this->load->model('ProdutoModel');

        // Busca o produto pelo ID
        $produto = $this->ProdutoModel->obter($id);

        if ($produto) {
            // Retorna o produto e suas variações como JSON
            echo json_encode($produto);
        } else {
            // Retorna um erro caso o produto não seja encontrado
            http_response_code(404);
            echo json_encode(['erro' => 'Produto não encontrado']);
        }
    }

    public function excluir($id)
    {
        // Carrega o modelo
        $this->load->model('ProdutoModel');

        // Exclui o produto pelo ID
        $this->ProdutoModel->excluir($id);
    }
}