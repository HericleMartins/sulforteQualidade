<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();
$objRefilePalavraChaveAlerta = new RefilePalavraChaveAlerta($r['idRefilePalavraChaveAlerta']);
$status = $objRefilePalavraChaveAlerta->remover();

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx005s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx006s')));
}
