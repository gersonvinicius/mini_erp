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

    public function validar()
    {
        $this->load->model('CupomModel');

        $codigo = $this->input->post('codigo');
        $subtotal = $this->input->post('subtotal');

        // Busca o cupom no banco de dados
        $cupom = $this->CupomModel->obterPorCodigo($codigo);

        // Valida se o cupom existe
        if (!$cupom) {
            echo json_encode(['error' => 'Cupom inválido ou inexistente.']);
            return;
        }

        // Valida se o cupom está expirado
        if (date('Y-m-d') > $cupom['validade']) {
            echo json_encode(['error' => 'Cupom expirado.']);
            return;
        }

        // Valida o valor mínimo do cupom
        if ($cupom['valor_minimo'] && $subtotal < $cupom['valor_minimo']) {
            echo json_encode(['error' => 'O subtotal não atende ao valor mínimo de R$ ' . number_format($cupom['valor_minimo'], 2, ',', '.') . ' para este cupom.']);
            return;
        }

        // Calcula o desconto
        $desconto = 0;
        if ($cupom['tipo'] === 'fixo') {
            $desconto = (float)$cupom['valor_desconto'];
        } elseif ($cupom['tipo'] === 'percentual') {
            $desconto = ($subtotal * (float)$cupom['valor_desconto']) / 100;
        }

        echo json_encode([
            'success' => true,
            'desconto' => $desconto,
            'tipo' => $cupom['tipo']
        ]);
    }
}