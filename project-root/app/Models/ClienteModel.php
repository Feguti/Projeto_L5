<?php

namespace App\Models;

use CodeIgniter\Model;

class ClienteModel extends Model
{
    protected $table = 'clientes'; 
    protected $primaryKey = 'id'; 
    protected $allowedFields = ['nome_razao_social', 'cpf_cnpj'];
    
    public function buscarPorNome($nome)
    {
        return $this->like('nome', $nome)->findAll();
    }
}