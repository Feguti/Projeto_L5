<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/clientes', 'Clientes::index');
//$routes->get('/cadastrar_cliente','Clientes::cadastroinicial');
$routes->post('/clientes/salvar', 'Clientes::salvar');
$routes->get('/clientes/listarTodos', 'Clientes::listarTodos');
$routes->delete('/clientes/deletar/(:num)', 'Clientes::deletar/$1');
$routes->put('/clientes/editar/(:num)', 'Clientes::editar/$1');
//$routes->post('/clientes/atualizar', 'Clientes::atualizar');
$routes->post('/clientes/pesquisar', 'Clientes::pesquisar');
//$routes->get('/clientes/pesquisarCadastrar', 'Clientes::pesquisarCadastrar');



$routes->get('/produtos', 'Produtos::index');
//$routes->get('/cadastrar_produto','Produtos::cadastroinicial');
$routes->post('/produtos/salvar', 'Produtos::salvar');
$routes->get('/produtos/listarTodos', 'Produtos::listarTodos');
$routes->delete('/produtos/deletar/(:num)', 'Produtos::deletar/$1');
$routes->put('/produtos/editar/(:num)', 'Produtos::editar/$1');
//$routes->post('/produtos/atualizar', 'Produtos::atualizar');
$routes->post('/produtos/pesquisar', 'Produtos::pesquisar');

$routes->get('/pedidos', 'Pedidos::index');
//$routes->get('/cadastrar_pedido','Pedidos::cadastroinicial');
$routes->post('/pedidos/salvar', 'Pedidos::salvar');
$routes->get('/pedidos/listarTodos', 'Pedidos::listarTodos');
$routes->delete('/pedidos/deletar/(:num)', 'Pedidos::deletar/$1');
$routes->put('/pedidos/editar/(:num)', 'Pedidos::editar/$1');
//$routes->post('/pedidos/atualizar', 'Pedidos::atualizar');
$routes->post('/pedidos/pesquisar', 'Pedidos::pesquisar');


