<?php

class ProdutoModel extends CI_Model
{
    public function listar()
    {
        $this->db->select('produtos.id, produtos.nome, produtos.preco_base, SUM(estoque.quantidade) AS estoque_total');
        $this->db->from('produtos');
        $this->db->join('estoque', 'estoque.produto_id = produtos.id', 'left');
        $this->db->group_by('produtos.id');
        return $this->db->get()->result_array();
    }

    public function obterVariacoes($produto_id)
    {
        return $this->db
            ->select('id, nome, preco_adicional')
            ->from('variacoes')
            ->where('produto_id', $produto_id)
            ->get()
            ->result_array();
    }

    public function salvar($dados)
    {
        $this->db->insert('produtos', $dados);
        return $this->db->insert_id();
    }

    public function atualizar($id, $dados)
    {
        $this->db->where('id', $id)->update('produtos', $dados);
    }

    public function salvarVariacoes($produto_id, $variacoes)
    {
        if (!empty($variacoes)) {
            foreach ($variacoes['nome'] as $key => $nome_variacao) {
                $dados_variacao = [
                    'produto_id' => $produto_id,
                    'nome' => $nome_variacao,
                    'preco_adicional' => $variacoes['preco_adicional'][$key], // Salva o preço adicional
                ];
                $this->db->insert('variacoes', $dados_variacao);
                $variacao_id = $this->db->insert_id();

                $dados_estoque = [
                    'produto_id' => $produto_id,
                    'variacao_id' => $variacao_id,
                    'quantidade' => $variacoes['estoque'][$key],
                ];
                $this->db->insert('estoque', $dados_estoque);
            }
        }
    }

    public function atualizarVariacoes($produto_id, $variacoes)
    {
        if (!empty($variacoes)) {
            foreach ($variacoes['nome'] as $key => $nome_variacao) {
                $variacao_existente = $this->db->get_where('variacoes', [
                    'produto_id' => $produto_id,
                    'nome' => $nome_variacao,
                ])->row_array();

                if ($variacao_existente) {
                    $this->db->where('id', $variacao_existente['id'])->update('variacoes', [
                        'preco_adicional' => $variacoes['preco_adicional'][$key], // Atualiza o preço adicional
                    ]);

                    $this->db->where('variacao_id', $variacao_existente['id'])->update('estoque', [
                        'quantidade' => $variacoes['estoque'][$key],
                    ]);
                } else {
                    $dados_variacao = [
                        'produto_id' => $produto_id,
                        'nome' => $nome_variacao,
                        'preco_adicional' => $variacoes['preco_adicional'][$key],
                    ];
                    $this->db->insert('variacoes', $dados_variacao);
                    $variacao_id = $this->db->insert_id();

                    $dados_estoque_variacao = [
                        'produto_id' => $produto_id,
                        'variacao_id' => $variacao_id,
                        'quantidade' => $variacoes['estoque'][$key],
                    ];
                    $this->db->insert('estoque', $dados_estoque_variacao);
                }
            }
        }

        // Remover variações não enviadas
        $variacoes_nomes = array_map('trim', $variacoes['nome']);
        $this->db->where('produto_id', $produto_id)
                ->where_not_in('nome', $variacoes_nomes)
                ->delete('variacoes');
    }

    public function obter($id)
    {
        // Obter informações do produto
        $this->db->select('produtos.id AS produto_id, produtos.nome, produtos.preco_base');
        $this->db->from('produtos');
        $this->db->where('produtos.id', $id);
        $produto = $this->db->get()->row_array();

        if ($produto) {
            // Obter informações das variações associadas ao produto
            $this->db->select('variacoes.id AS variacao_id, variacoes.nome, variacoes.preco_adicional, estoque.quantidade');
            $this->db->from('variacoes');
            $this->db->join('estoque', 'estoque.variacao_id = variacoes.id', 'left');
            $this->db->where('variacoes.produto_id', $id);
            $variacoes = $this->db->get()->result_array();

            // Adicionar as variações ao produto
            $produto['variacoes'] = $variacoes;
        }

        return $produto;
    }

    public function excluir($id)
    {
        // Verificar se o produto existe
        $produto = $this->db->get_where('produtos', ['id' => $id])->row_array();

        if (!$produto) {
            return false; // Retorna falso se o produto não existir
        }

        // Excluir estoques relacionados às variações do produto
        $this->db->where('produto_id', $id);
        $this->db->delete('estoque');

        // Excluir variações do produto
        $this->db->where('produto_id', $id);
        $this->db->delete('variacoes');

        // Excluir o produto em si
        $this->db->where('id', $id);
        $this->db->delete('produtos');

        return true; // Retorna true se a exclusão foi bem-sucedida
    }
}