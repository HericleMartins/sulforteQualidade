<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();
$objObservacao = new Observacao($r['idobservacao']);
$status = $objObservacao->remover();

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OBx005s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OBx006e')));
}
