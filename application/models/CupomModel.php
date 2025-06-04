<?php

class CupomModel extends CI_Model
{
    public function listar()
    {
        return $this->db->get('cupons')->result_array();
    }

    public function obter($id)
    {
        return $this->db->get_where('cupons', ['id' => $id])->row_array();
    }

    public function salvar($dados)
    {
        $this->db->insert('cupons', $dados);
        return $this->db->insert_id();
    }

    public function atualizar($id, $dados)
    {
        $this->db->where('id', $id)->update('cupons', $dados);
    }

    public function excluir($id)
    {
        $this->db->where('id', $id)->delete('cupons');
    }
}