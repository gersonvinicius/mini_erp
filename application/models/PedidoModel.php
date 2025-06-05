<?php

class PedidoModel extends CI_Model
{
    public function salvarPedido($dados)
    {
        $this->db->insert('pedidos', $dados);
        return $this->db->insert_id();
    }

    public function salvarItem($dados)
    {
        $this->db->insert('itens_pedido', $dados);
    }

    public function removerPedido($pedidoId)
    {
        $this->db->where('id', $pedidoId);
        return $this->db->delete('pedidos'); // Retorna TRUE se a exclusão foi bem-sucedida
    }

    public function atualizarStatus($pedidoId, $status)
    {
        $this->db->where('id', $pedidoId);
        return $this->db->update('pedidos', ['status' => $status]); // Retorna TRUE se a atualização foi bem-sucedida
    }
}