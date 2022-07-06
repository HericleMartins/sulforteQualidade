<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objUsuario = new Usuario($r['idusuario']);
$arrayUsuario = $objUsuario->carregar();

$status = $objUsuario->editar(array('status' => ($arrayUsuario['status'] == 1 ? NULL : 1)), 'idusuario = ' . $r['idusuario']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('UUx007s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx008e')));
}