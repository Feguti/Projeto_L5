<?php

namespace App\Controllers;
use App\Models\ProdutoModel;

class Produtos extends BaseController
{
    /*public function index()
    {
        return view('produtos'); 
    } */ 
    
    /*public function cadastroinicial()
    {
        return view('cadastrar_produto');
    }*/

    public function salvar()
    { 
        $request = service('request');
        $nome = trim($request->getVar('nome'));
        $descricao = trim($request->getVar('descricao'));
        $preco = $request->getVar('preco');

        // Valida se nome e preço foram informados
        if (empty($nome) || empty($preco)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O nome e o preço do produto são obrigatórios.'
                ],
                'retorno' => []
            ]);
        }

        // Verifica se o preço é um número válido e maior que zero
        if (!is_numeric($preco) || floatval($preco) <= 0) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O preço deve ser um número válido e maior que zero.'
                ],
                'retorno' => []
            ]);
        }

        $data = [
            'nome' => $nome,
            'descricao' => $descricao,
            'preco' => floatval($preco)
        ];
        $data = [
            'nome' => $request->getVar('nome'),
            'descricao' => $request->getVar('descricao'),
            'preco' => $request->getVar('preco')
        ];

        $produtoModel = new ProdutoModel();

        if ($produtoModel->insert($data)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Produto cadastrado com sucesso'
                ],
                'retorno' => $data
            ]);
        } else {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Erro ao cadastrar produto'
                ],
                'retorno' => []
            ]);
        }
    }

    public function pesquisar()
    {
        $request = service('request');
        $termo = $request->getVar('termo'); 

        $produtoModel = new ProdutoModel();
        $produtos = [];

        if ($termo) {
            if (is_numeric($termo)) {
                $produto = $produtoModel->where('id', $termo)->first();
                if ($produto) {
                    $produtos[] = $produto;
                }
            } else {
                $produtos = $produtoModel->like('nome', $termo)->findAll();
            }
        }
        if (empty($produtos)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Nenhum produto encontrado para o termo informado.'
                ],
                'retorno' => []
            ]);
        }
        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produtos encontrados com sucesso'
            ],
            'retorno' => $produtos
        ]);
    }

    public function listarTodos()
    {
        $produtoModel = new ProdutoModel();
        $produtos = $produtoModel->findAll(); 

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produtos listados com sucesso'
            ],
            'retorno' => $produtos
        ]);
    }

    public function deletar($id)
    {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($id);

        if (!$produto) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado'
                ],
                'retorno' => []
            ]);
        }

        $produtoModel->delete($id);

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto deletado com sucesso'
            ],
            'retorno' => []
        ]);
    }

    public function editar($id)
    {
        $produtoModel = new ProdutoModel();
        $produto = $produtoModel->find($id);

        if (!$produto) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Produto não encontrado'
                ],
                'retorno' => []
            ]);
        }

        $request = service('request');
        $data = $request->getJSON(true); 

        
        $nome = isset($data['nome']) ? trim($data['nome']) : '';
        $preco = isset($data['preco']) ? $data['preco'] : '';

       
        if (empty($nome) || empty($preco)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O nome e o preço do produto são obrigatórios.'
                ],
                'retorno' => []
            ]);
        }

        
        if (!is_numeric($preco) || floatval($preco) <= 0) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'O preço deve ser um número válido e maior que zero.'
                ],
                'retorno' => []
            ]);
        }

        
        $updateData = [
            'nome' => $nome,
            'preco' => floatval($preco),
        ];

        
        if (isset($data['descricao'])) {
            $updateData['descricao'] = trim($data['descricao']);
        }

        $produtoModel->update($id, $data);

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Produto atualizado com sucesso'
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
            'nome' => $request->getPost('nome'),
            'descricao' => $request->getPost('descricao'),
            'preco' => $request->getPost('preco')
        ];

        $produtoModel = new ProdutoModel();
        $produtoModel->update($id, $data);

        return redirect()->to('/produtos/listarTodos')->with('success', 'Produto atualizado com sucesso!');
    }*/
}