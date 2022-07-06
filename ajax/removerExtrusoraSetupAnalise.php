<?php
include( "../inc/load.php");

$r = getRequest($_POST);

if ($r['tipo'] == 1){
//setup
    SqlServer::abrirTransacao();
    $objObs = new ExtrusoraSetupObservacao();
    $status = $objObs->removerPor('idextrusoraSetup = ' . $r['id']);
    if ($status){
        $obj = new ExtrusoraSetup($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('ESx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx007e')));
    }

} else if ($r['tipo'] == 2){
//analise
    SqlServer::abrirTransacao();
    $objObs = new ExtrusoraAnaliseObservacao();
    $status = $objObs->removerPor('idextrusoraAnalise = ' . $r['id']);
    if ($status){
        $obj = new ExtrusoraAnalise($r['id']);
        $status = $obj->remover();
    }
    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('EAx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx007e')));
    }
} else {
//erro
    die();
}