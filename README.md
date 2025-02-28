# 📌 Projeto API CodeIgniter 4

## 📖 Descrição
Esta é uma API RESTful desenvolvida em **PHP** utilizando o **CodeIgniter 4** e **MySQL** como banco de dados. A API fornece endpoints para gerenciar **clientes**, **produtos** e **pedidos de compra**, permitindo operações CRUD (Create, Read, Update, Delete).

## 🚀 Tecnologias Utilizadas
- **PHP** (CodeIgniter 4)
- **MySQL**
- **Composer**

---

## ⚙️ Instalação e Configuração

### 🔹 1. Clonar o Repositório
```bash
git clone https://github.com/seu-usuario/seu-repositorio.git
cd seu-repositorio
```

### 🔹 2. Instalar Dependências
Certifique-se de ter o **Composer** instalado e execute:
```bash
composer install
```

### 🔹 3. Configurar o Banco de Dados
1. **Crie um banco de dados MySQL**:
```sql
CREATE DATABASE nome_do_banco;
```
2. **Importe a estrutura do banco**:
```bash
mysql -u usuario -p nome_do_banco < database.sql
```
Caso utilize o MySQL WorkBench, é possível importar o arquivo do banco direto por ele

3. **Configure o arquivo `Database.php` localizado na pasta app/config**:
```
# Exemplo de conexão do banco, procure por esse array e atualize as informações da sua conexão:

public array $default = [

        'hostname'     => 'localhost',
        'username'     => 'seu_usuario',
        'password'     => 'sua_senha',
        'database'     => 'l5_bdd',
```
### 🔹 4. Iniciar o Servidor Local
```bash
php spark serve
```
O endereço da API será:
```
http://localhost:8080/
```

---

## 📌 Endpoints da API

### 🟢 **Clientes**
- **Criar Cliente**: `POST /clientes/salvar`
- **Buscar Cliente por nome**: `POST /clientes/pesquisar`
- **Listar Clientes**: `GET /clientes/listarTodos`
- **Deletar Cliente**: `DELETE /clientes/deletar/{id}`
- **Atualizar Cliente**: `PUT /clientes/editar/{id}`


### 🟢 **Produtos**
- **Criar Produto**: `POST /produtos/salvar`
- **Buscar Produto por nome**: `POST /produtos/pesquisar`
- **Listar Produtos**: `GET /produto/listarTodos`
- **Deletar Produto**: `DELETE /produtos/deletar/{id}`
- **Atualizar Produto**: `PUT /produtos/editar/{id}`

### 🟢 **Pedidos de Compra**
- **Criar Pedido**: `POST /pedidos/salvar`
- **Buscar Pedido por nome de cliente**: `POST /pedidos/pesquisar`
- **Listar Pedidos**: `GET /pedidos/listarTodos`
- **Deletar Pedido**: `DELETE /pedidos/deletar/{id}`
- **Atualizar Pedido**: `PUT /pedidos/editar/{id}` 

---

## 📌 Exemplo de Requisição em que parâmetros são necessários

### **Cadastrar um Cliente (POST endereçoDaAPI/clientes/salvar)**
```json
{
    "parametros":
    {
        "nome_razao_social": "Cliente Exemplo",
        "cpf_cnpj": "12345678900"
    }
}
```

### **Pesquisar um cliente (POST endereçoDaAPI/clientes/pesquisar)**
```json
{
    "parametros": {
        "termo": "Nome do cliente"
    }
}

```
### **Editar um Cliente (PUT endereçoDaAPI/clientes/editar/id)**
```json
{
    "nome_razao_social": "Novo Nome do Cliente",
    "cpf_cnpj": "12345678900"
}

```

### **Cadastrar um Produto (POST endereçoDaAPI/produtos/salvar)**
```json
{
    "parametros":
    {
        "nome": "Produto Exemplo",
        "descricao": "Descrição do produto",
        "preco": 100.50
    }
}
```

### **Pesquisar um produto (POST endereçoDaAPI/produtos/pesquisar)**
```json
{
    "parametros": {
        "termo": "Nome do produto"
    }
}

```
### **Editar um produto (PUT endereçoDaAPI/produtos/editar/id)**
```json
{
    "nome": "Produto Atualizado",
    "descricao": "Descrição atualizada",
    "preco": 120.99
}

```

### **Cadastrar um Pedido (POST endereçoDaAPI/pedidos/salvar)**
```json
{
    "situacao": "EM ABERTO",
    "cliente_id": 9,
    "produtos": [2, 3],
    "quantidade": {
        "2": 3,
        "3": 5
    }
}
```

### **Pesquisar um pedido por nome do cliente (POST endereçoDaAPI/pedidos/pesquisar)**
```json
{
    "parametros": {
        "termo": "Nome do cliente"
    }
}

```
### **Editar um Pedido (PUT endereçoDaAPI/pedidos/editar/id)**
```json
{
    "situacao": "PAGO",
    "produtos": [2, 3, 5, 6],
    "quantidade": {
        "2": 3,
        "3": 5,
        "5": 1,
        "6": 6
    }
}

```

## 🛠️ Testando a API
Recomendo a utilização do Postman para testar os Endpoints.


