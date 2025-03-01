<?php

namespace App\Controllers;
use App\Models\ClienteModel;

class Clientes extends BaseController
{
    /*public function index()
    {
        return view('clientes'); 
    }*/  
    
    /*public function cadastroinicial()
    {
        return view('cadastrar_cliente');
    }*/

    public function salvar()
    {
        $request = service('request');
        
        $data = $request->getJSON(true); 
        
        if (!isset($data['parametros']['nome_razao_social']) || empty(trim($data['parametros']['nome_razao_social'])) ||
            !isset($data['parametros']['cpf_cnpj']) || empty(trim($data['parametros']['cpf_cnpj']))
        ) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Nome/Razão Social e CPF/CNPJ são obrigatórios e não podem estar em branco.'
                ],
                'retorno' => []
            ]);
        }
    
        $clienteData = [
            'nome_razao_social' => $data['parametros']['nome_razao_social'],
            'cpf_cnpj' => $data['parametros']['cpf_cnpj']
        ];
    
        $clienteModel = new ClienteModel();
    
        if ($clienteModel->insert($clienteData)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Dados cadastrados com sucesso'
                ],
                'retorno' => $clienteData
            ]);
        } else {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 500,
                    'mensagem' => 'Erro ao cadastrar cliente'
                ],
                'retorno' => []
            ]);
        }
    }
    
    public function pesquisar()
    {
        $request = service('request');
        
        $data = $request->getJSON(true); 
        
        if (!isset($data['parametros']['termo']) || empty(trim($data['parametros']['termo']))) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Parâmetro de pesquisa "termo" não pode ficar vazio'
                ],
                'retorno' => []
            ]);
        }

        $termo = $data['parametros']['termo'];

        $clienteModel = new ClienteModel();
        
        $clientes = $clienteModel->like('nome_razao_social', $termo)->findAll();
        
        if (empty($clientes)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Nenhum cliente encontrado com o nome fornecido'
                ],
                'retorno' => []
            ]);
        }

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => $clientes
        ]);
    }

    /*public function pesquisarCadastrar()
    {
        $nomeCliente = $this->request->getVar('nome_razao_social');

        if ($nomeCliente) {
            $clientesModel = new ClienteModel();
            $clientes = $clientesModel->like('nome_razao_social', $nomeCliente)->findAll();

            return $this->response->setJSON($clientes);
        }

        return $this->response->setJSON([]);
    }*/

    public function listarTodos()
    {
        $clienteModel = new ClienteModel();
        $clientes = $clienteModel->findAll();
    
        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Dados retornados com sucesso'
            ],
            'retorno' => $clientes
        ]);
    }

    public function deletar($id = null)
    {

        if (empty($id)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O ID do cliente é obrigatório para exclusão.'
                ],
                'retorno' => []
            ]);
        }

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($id);

        if (!$cliente) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Cliente não encontrado'
                ],
                'retorno' => []
            ]);
        }
    
        $clienteModel->delete($id);
    
        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente deletado com sucesso'
            ],
            'retorno' => []
        ]);
    }
    
    public function editar($id)
    {
        $clienteModel = new ClienteModel();
        
        $cliente = $clienteModel->find($id);
        if (!$cliente) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Cliente não encontrado'
                ],
                'retorno' => []
            ]);
        }

        $request = service('request');
        $data = $request->getJSON(true); 

        if (empty($data['nome_razao_social']) || empty($data['cpf_cnpj'])) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O nome e o CPF/CNPJ são obrigatórios e não podem estar vazios.'
                ],
                'retorno' => []
            ]);
        }
        
        $clienteModel->update($id, $data);

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Cliente atualizado com sucesso'
            ],
            'retorno' => [
                'id' => $id,
                'dados_atualizados' => $data
            ]
        ]);
    }

    /*public function atualizar()
    {
        $request = service('request');
        
        $id = $request->getPost('id');
        $data = [
            'nome_razao_social' => $request->getPost('nome_razao_social'),
            'cpf_cnpj' => $request->getPost('cpf_cnpj')
        ];

        $clienteModel = new ClienteModel();
        $clienteModel->update($id, $data);

        return redirect()->to('/clientes/listarTodos')->with('success', 'Cliente atualizado com sucesso!');
    }*/
}