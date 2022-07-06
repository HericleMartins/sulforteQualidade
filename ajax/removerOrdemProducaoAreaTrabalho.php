<?php
include( "../inc/load.php");

$post = getRequest($_POST);
$numero = (int)$post['idareaTrabalho'];

if($numero > 0){

    SqlServer::abrirTransacao();

    $objArea = new AreaTrabalho($numero);
    $status = $objArea->remover();

    if ($status) {
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('BBx006s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('BBx007e')));
    }
} else {
    /* retorna erro avisando que não chegou número do id da área do trabalho*/
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('BBx008e')));
}
