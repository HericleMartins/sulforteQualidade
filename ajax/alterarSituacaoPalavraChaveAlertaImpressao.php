<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objImpressaoPalavraChaveAlerta = new ImpressaoPalavraChaveAlerta($r['idImpressaoPalavraChaveAlerta']);
$arrayImpressaoPalavraChaveAlerta = $objImpressaoPalavraChaveAlerta->carregar();

$status = $objImpressaoPalavraChaveAlerta->editar(array('situacao' => ($arrayImpressaoPalavraChaveAlerta['situacao'] == 1 ? NULL : 1)), 'idImpressaoPalavraChaveAlerta = ' . $r['idImpressaoPalavraChaveAlerta']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx007s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx008e')));
}
