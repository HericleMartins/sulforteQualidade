<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();
$objCortePalavraChaveAlerta = new CortePalavraChaveAlerta($r['idCortePalavraChaveAlerta']);
$status = $objCortePalavraChaveAlerta->remover();

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx005s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx006s')));
}
