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
}