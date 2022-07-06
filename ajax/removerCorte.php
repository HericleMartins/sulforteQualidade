<?php
include( "../inc/load.php");

$r = getRequest($_POST);

if ($r['tipo'] == 4){
//liberação
    SqlServer::abrirTransacao();
    $objObs = new CorteLiberacaoObservacao();
    $status = $objObs->removerPor('idcorteLiberacao = ' . $r['id']);
    if ($status){
        $obj = new CorteLiberacao($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('CSx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx007e')));
    }

} else if ($r['tipo'] == 5){
//análise
    SqlServer::abrirTransacao();
    $objObs = new CorteAnaliseObservacao();
    $status = $objObs->removerPor('idcorteAnalise = ' . $r['id']);
    if ($status){
        $obj = new CorteAnalise($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('CAx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx007e')));
    }
} else {
//erro
    die();
}