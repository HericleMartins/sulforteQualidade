<?php
include( "../inc/load.php");

SqlServer::abrirTransacao();

$r = getRequest($_POST);
$obj = new ExtrusoraPresetup($r['idextrusoraPresetup']);

$objMaterias = new ExtrusoraPresetupMateria();

//exclui filhos
$status = $objMaterias->removerPor('idextrusoraPresetup = ' . $r['idextrusoraPresetup']);

if($status) {
    //exclui o pai
    $status = $obj->remover();
}

if($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('EPx005s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx006e')));
}