<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

$objCortePalavraChaveAlerta = new CortePalavraChaveAlerta($r['idCortePalavraChaveAlerta']);
$arrayCortePalavraChaveAlerta = $objCortePalavraChaveAlerta->carregar();

$status = $objCortePalavraChaveAlerta->editar(array('situacao' => ($arrayCortePalavraChaveAlerta['situacao'] == 1 ? NULL : 1)), 'idCortePalavraChaveAlerta = ' . $r['idCortePalavraChaveAlerta']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx007s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx008e')));
}
