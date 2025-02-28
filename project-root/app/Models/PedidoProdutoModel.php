<?php

namespace App\Models;

use CodeIgniter\Model;

class PedidoProdutoModel extends Model
{
    protected $table = 'pedido_produtos'; 
    protected $primaryKey = 'id'; 
    protected $allowedFields = ['pedido_id', 'produto_id', 'quantidade', 'preco_unitario']; 
}