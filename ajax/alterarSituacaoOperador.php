<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objOperador = new Operador($r['idoperador']);
$arrayOperador = $objOperador->carregar();

$status = $objOperador->editar(array('situacao' => ($arrayOperador['situacao'] == 1 ? NULL : 1)), 'idoperador = ' . $r['idoperador']);


if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OPx010s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OPx011e')));
}
