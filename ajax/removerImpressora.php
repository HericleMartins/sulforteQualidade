<?php
include( "../inc/load.php");

$r = getRequest($_POST);

if ($r['tipo'] == 8){
//liberação
    SqlServer::abrirTransacao();
    $objObs = new ImpressoraLiberacaoObservacao();
    $status = $objObs->removerPor('idimpressoraLiberacao = ' . $r['id']);
    if ($status){
        $obj = new ImpressoraLiberacao($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('ILx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ILx007e')));
    }

} else if ($r['tipo'] == 9){
//análise
    SqlServer::abrirTransacao();
    $objObs = new ImpressoraAnaliseObservacao();
    $status = $objObs->removerPor('idimpressoraAnalise = ' . $r['id']);
    if ($status){
        $obj = new ImpressoraAnalise($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('IAx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx007e')));
    }
} else {
//erro
    die();
}