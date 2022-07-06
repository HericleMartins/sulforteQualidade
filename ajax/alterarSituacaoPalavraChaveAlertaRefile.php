<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objRefilePalavraChaveAlerta = new RefilePalavraChaveAlerta($r['idRefilePalavraChaveAlerta']);
$arrayRefilePalavraChaveAlerta = $objRefilePalavraChaveAlerta->carregar();

$status = $objRefilePalavraChaveAlerta->editar(array('situacao' => ($arrayRefilePalavraChaveAlerta['situacao'] == 1 ? NULL : 1)), 'idRefilePalavraChaveAlerta = ' . $r['idRefilePalavraChaveAlerta']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx007s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx008e')));
}
