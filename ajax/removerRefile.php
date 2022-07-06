<?php
include( "../inc/load.php");

$r = getRequest($_POST);

if ($r['tipo'] == 6){
//liberação
    SqlServer::abrirTransacao();
    $objObs = new RefileLiberacaoObservacao();
    $status = $objObs->removerPor('idrefileLiberacao = ' . $r['id']);
    if ($status){
        $obj = new RefileLiberacao($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RSx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx007e')));
    }

} else if ($r['tipo'] == 7){
//análise
    SqlServer::abrirTransacao();
    $objObs = new RefileAnaliseObservacao();
    $status = $objObs->removerPor('idrefileAnalise = ' . $r['id']);
    if ($status){
        $obj = new RefileAnalise($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RAx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx007e')));
    }
} else {
//erro
    die();
}