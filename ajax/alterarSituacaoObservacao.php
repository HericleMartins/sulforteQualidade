<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objObservacao = new Observacao($r['idobservacao']);
$arrayObservacao = $objObservacao->carregar();

$status = $objObservacao->editar(array('situacao' => ($arrayObservacao['situacao'] == 1 ? NULL : 1)), 'idobservacao = ' . $r['idobservacao']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OBx007s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OBx008e')));
}
