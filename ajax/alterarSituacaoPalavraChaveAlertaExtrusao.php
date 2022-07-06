<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objExtrusaoPalavraChaveAlerta = new ExtrusaoPalavraChaveAlerta($r['idExtrusaoPalavraChaveAlerta']);
$arrayExtrusaoPalavraChaveAlerta = $objExtrusaoPalavraChaveAlerta->carregar();

$status = $objExtrusaoPalavraChaveAlerta->editar(array('situacao' => ($arrayExtrusaoPalavraChaveAlerta['situacao'] == 1 ? NULL : 1)), 'idExtrusaoPalavraChaveAlerta = ' . $r['idExtrusaoPalavraChaveAlerta']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx007s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx008e')));
}
