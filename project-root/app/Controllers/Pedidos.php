<?php

namespace App\Controllers;
use App\Models\PedidoModel;
use App\Models\ProdutoModel;
use App\Models\PedidoProdutoModel;
use App\Models\ClienteModel;

class Pedidos extends BaseController
{
    /*public function index()
    {
        return view('pedidos'); 
    }*/ 
    
    /*public function cadastroinicial()
    {
        $produtoModel = new ProdutoModel();

        $produtos = $produtoModel->findAll();

        return view('cadastrar_pedido', ['produtos' => $produtos]);
    }*/

    public function salvar()
    {
        try {
            $request = service('request');
            $dados = $request->getJSON(true); 
    
            $situacao = strtoupper(trim($dados['situacao'] ?? ''));
            if (!in_array($situacao, ['EM ABERTO', 'PAGO', 'CANCELADO'])) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'Situação inválida. Os valores válidos são "EM ABERTO", "PAGO" e "CANCELADO".'
                    ],
                    'retorno' => []
                ]);
            }
    
            if (!isset($dados['cliente_id'])) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'O campo "cliente_id" é obrigatório.'
                    ],
                    'retorno' => []
                ]);
            }
    
            // Verificar se o cliente existe no banco
            $clienteModel = new ClienteModel();
            $cliente = $clienteModel->find($dados['cliente_id']);
            if (!$cliente) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'Cliente não encontrado.'
                    ],
                    'retorno' => []
                ]);
            }
    
            if (!isset($dados['produtos']) || !is_array($dados['produtos']) || count($dados['produtos']) === 0) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'A lista de produtos não pode estar vazia.'
                    ],
                    'retorno' => []
                ]);
            }
    
            if (!isset($dados['quantidade']) || !is_array($dados['quantidade']) || count($dados['quantidade']) === 0) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'A lista de quantidades não pode estar vazia.'
                    ],
                    'retorno' => []
                ]);
            }
    
            foreach ($dados['produtos'] as $produtoId) {
                if (!isset($dados['quantidade'][$produtoId]) || $dados['quantidade'][$produtoId] <= 0) {
                    return $this->response->setJSON([
                        'cabecalho' => [
                            'status' => 400,
                            'mensagem' => "O produto ID {$produtoId} deve ter uma quantidade válida maior que zero."
                        ],
                        'retorno' => []
                    ]);
                }
            }
    
            $pedidoModel = new PedidoModel();
            $pedidoId = $pedidoModel->insert([
                'situacao' => $situacao,
                'cliente_id' => $dados['cliente_id']
            ]);
    
            if (!$pedidoId) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'Erro ao cadastrar pedido.'
                    ],
                    'retorno' => []
                ]);
            }
    
            $produtoModel = new ProdutoModel();
            $produtosDetalhados = $produtoModel->whereIn('id', $dados['produtos'])->findAll();
    
            $pedidoProdutoModel = new PedidoProdutoModel();
            $valorTotal = 0;
    
            foreach ($produtosDetalhados as $produto) {
                $quantidade = $dados['quantidade'][$produto['id']];
    
                $valorProduto = $produto['preco'] * $quantidade;
                $valorTotal += $valorProduto;
    
                $pedidoProdutoModel->insert([
                    'pedido_id' => $pedidoId,
                    'produto_id' => $produto['id'],
                    'quantidade' => $quantidade,
                    'preco' => $produto['preco']
                ]);
            }
    
            $pedidoModel->update($pedidoId, ['valor' => $valorTotal]);
    
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Pedido cadastrado com sucesso!'
                ],
                'retorno' => [
                    'pedido_id' => $pedidoId,
                    'valor_total' => $valorTotal
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 500,
                    'mensagem' => 'Erro ao processar a requisição.',
                    'erro' => $e->getMessage()
                ],
                'retorno' => []
            ]);
        }
    }
    

    public function pesquisar()
    {
        $request = service('request');
        $termo = $request->getVar('termo'); 

        $pedidoModel = new PedidoModel();
        $db = \Config\Database::connect();
        $pedidos = [];

        if ($termo) {
            if (is_numeric($termo)) {
                $pedido = $pedidoModel->where('id', $termo)->first();
                if ($pedido) {
                    $pedidos[] = $pedido;
                }
            } else {
                $query = $db->query(" 
                    SELECT pedidos.*, clientes.nome_razao_social 
                    FROM pedidos 
                    JOIN clientes ON pedidos.cliente_id = clientes.id 
                    WHERE clientes.nome_razao_social LIKE '%$termo%' 
                ");
                $pedidos = $query->getResultArray();
            }
        }

        if (empty($pedidos)) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Nenhum pedido encontrado para o termo informado.'
                ],
                'retorno' => []
            ]);
        }

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedidos encontrados com sucesso.'
            ],
            'retorno' => $pedidos
        ]);
    }

    public function listarTodos()
    {
        $pedidoModel = new PedidoModel();
        
        $pedidos = $pedidoModel
            ->select('pedidos.*, clientes.nome_razao_social') 
            ->join('clientes', 'clientes.id = pedidos.cliente_id') 
            ->orderBy('pedidos.id', 'ASC')
            ->findAll(); 

        return $this->response->setJSON([
            'cabecalho' => [
                'status' => 200,
                'mensagem' => 'Pedidos encontrados com sucesso.'
            ],
            'retorno' => $pedidos
        ]);
    }

    public function deletar($id)
    {
        $pedidoModel = new PedidoModel();

        $pedido = $pedidoModel->find($id);
        if (!$pedido) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 404,
                    'mensagem' => 'Pedido não encontrado.'
                ],
                'retorno' => []
            ]);
        }

        $deleted = $pedidoModel->delete($id);

        if ($deleted) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Pedido deletado com sucesso!'
                ],
                'retorno' => []
            ]);
        } else {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 400,
                    'mensagem' => 'Erro ao deletar pedido.'
                ],
                'retorno' => []
            ]);
        }
    }

    public function editar($id)
    {
        try {
            $request = service('request');
            $pedidoModel = new PedidoModel();
            $pedido = $pedidoModel->find($id);
    
            if (!$pedido) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 404,
                        'mensagem' => 'Pedido não encontrado.'
                    ],
                    'retorno' => []
                ]);
            }
    
            $clienteIdOriginal = $pedido['cliente_id'];
    
            $dados = $request->getJSON(true);
    
            $situacao = strtoupper(trim($dados['situacao'] ?? ''));
            if (!in_array($situacao, ['EM ABERTO', 'PAGO', 'CANCELADO'])) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'Situação inválida. Os valores válidos são "EM ABERTO", "PAGO" e "CANCELADO".'
                    ],
                    'retorno' => []
                ]);
            }
    
            if (!isset($dados['produtos']) || !is_array($dados['produtos']) || count($dados['produtos']) === 0) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'A lista de produtos não pode estar vazia.'
                    ],
                    'retorno' => []
                ]);
            }
    
            if (!isset($dados['quantidade']) || !is_array($dados['quantidade']) || count($dados['quantidade']) === 0) {
                return $this->response->setJSON([
                    'cabecalho' => [
                        'status' => 400,
                        'mensagem' => 'A lista de quantidades não pode estar vazia.'
                    ],
                    'retorno' => []
                ]);
            }
    
            foreach ($dados['produtos'] as $produtoId) {
                if (!isset($dados['quantidade'][$produtoId]) || $dados['quantidade'][$produtoId] <= 0) {
                    return $this->response->setJSON([
                        'cabecalho' => [
                            'status' => 400,
                            'mensagem' => "O produto ID {$produtoId} deve ter uma quantidade válida maior que zero."
                        ],
                        'retorno' => []
                    ]);
                }
            }

            $pedidoData = [
                'situacao' => $situacao
            ];
    
            $pedidoModel->update($id, $pedidoData);
    
            $pedidoProdutoModel = new PedidoProdutoModel();
    
            $pedidoProdutoModel->where('pedido_id', $id)->delete();
    
            $produtoModel = new ProdutoModel();
            $produtosDetalhados = $produtoModel->whereIn('id', $dados['produtos'])->findAll();
    
            $produtosParaAdicionar = [];
            $valorTotal = 0;
    
            foreach ($produtosDetalhados as $produto) {
                $quantidade = $dados['quantidade'][$produto['id']];
    
                $valorProduto = $produto['preco'] * $quantidade;
                $valorTotal += $valorProduto;
    
                $produtosParaAdicionar[] = [
                    'pedido_id' => $id,
                    'produto_id' => $produto['id'],
                    'quantidade' => $quantidade,
                    'preco' => $produto['preco']
                ];
            }
    
            if (!empty($produtosParaAdicionar)) {
                $pedidoProdutoModel->insertBatch($produtosParaAdicionar);
            }
    
            $pedidoModel->update($id, ['valor' => $valorTotal]);
    
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 200,
                    'mensagem' => 'Pedido atualizado com sucesso.'
                ],
                'retorno' => [
                    'pedido_id' => $id,
                    'situacao' => $situacao,
                    'cliente_id' => $clienteIdOriginal,  
                    'valor_total' => $valorTotal,
                    'produtos' => $produtosParaAdicionar
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'cabecalho' => [
                    'status' => 500,
                    'mensagem' => 'Erro ao processar a requisição.',
                    'erro' => $e->getMessage()
                ],
                'retorno' => []
            ]);
        }
    }
    
    /*public function atualizar()
    {
        $request = service('request');
        
        $id = $request->getPost('id'); 

        $data = [
            'valor' => $request->getPost('valor'),
            'situacao' => $request->getPost('situacao'),
            'cliente_id' => $request->getPost('cliente_id')
        ];

        $pedidoModel = new PedidoModel();
        $pedidoModel->update($id, $data);

        $pedidoProdutosModel = new PedidoProdutoModel();

        $pedidoProdutosModel->where('pedido_id', $id)->delete();

        $produtos = $request->getPost('produtos');
        $quantidades = $request->getPost('quantidade');

        foreach ($produtos as $produtoId) {
            $preco = $request->getPost('preco_' . $produtoId);  

            $quantidade = $quantidades[$produtoId];

            $pedidoProdutoData = [
                'pedido_id' => $id,
                'produto_id' => $produtoId,
                'quantidade' => $quantidade,
                'preco' => $preco
            ];

            $pedidoProdutosModel->insert($pedidoProdutoData);
        }

        return redirect()->to('/pedidos/listarTodos')->with('success', 'Pedido atualizado com sucesso!');
    }*/
}