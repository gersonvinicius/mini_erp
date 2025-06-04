<?php

class Cupons extends CI_Controller
{
    public function index()
    {
        $this->load->view('cupons/index');
    }

    public function listar()
    {
        $this->load->model('CupomModel');
        $cupons = $this->CupomModel->listar();

        foreach ($cupons as $cupom) {
            $validade_formatada = date('d/m/Y', strtotime($cupom['validade'])); // Formata a validade

            echo "<tr>
                    <td>{$cupom['codigo']}</td>
                    <td>{$cupom['valor_desconto']} " . ($cupom['tipo'] === 'percentual' ? '%' : 'R$') . "</td>
                    <td>" . ($cupom['valor_minimo'] ? 'R$ ' . $cupom['valor_minimo'] : 'Sem mínimo') . "</td>
                    <td>{$validade_formatada}</td> <!-- Exibe a data formatada -->
                    <td>
                        <button class='btn btn-warning btn-sm btn-editar' data-id='{$cupom['id']}'>Editar</button>
                        <button class='btn btn-danger btn-sm btn-excluir' data-id='{$cupom['id']}'>Excluir</button>
                    </td>
                </tr>";
        }
    }

    public function salvar()
    {
        $this->load->model('CupomModel');

        $id = $this->input->post('id');
        $dados = [
            'codigo' => $this->input->post('codigo'),
            'valor_desconto' => $this->input->post('valor_desconto'),
            'tipo' => $this->input->post('tipo'),
            'valor_minimo' => $this->input->post('valor_minimo'),
            'validade' => $this->input->post('validade'),
        ];

        if ($id) {
            $this->CupomModel->atualizar($id, $dados);
        } else {
            $this->CupomModel->salvar($dados);
        }
    }

    public function obter($id)
    {
        $this->load->model('CupomModel');
        $cupom = $this->CupomModel->obter($id);
        echo json_encode($cupom);
    }

    public function excluir($id)
    {
        $this->load->model('CupomModel');
        $this->CupomModel->excluir($id);
    }
}