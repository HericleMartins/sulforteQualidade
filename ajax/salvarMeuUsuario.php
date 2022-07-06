<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$objUsuario = new Usuario();

SqlServer::abrirTransacao();

$objUsuario = new Usuario($_SESSION[SESSAO_SISTEMA]['idusuario']);
$arrayUsuario = $objUsuario->carregarDadosSenha();

if (!$r['senhaAtual']){
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx009e')));
    die();
}
if (sha1($r['senhaAtual']) != $arrayUsuario['senha']) {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx010e')));
    die();
}

if ($r['novaSenha'] != $r['senhaConfirma']) {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx013e')));
    die();
}

$dados = array(
    'usuario' => utf8_decode($r['usuario']),
    'senha' => ($r['novaSenha'] ? sha1($r['novaSenha']) : false)
);

$status = $objUsuario->editar($dados, 'idusuario=' . $_SESSION[SESSAO_SISTEMA]['idusuario']);

if ($status){
    SqlServer::confirmarTransacao();
    echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('UUx012s')));
} else {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx011e')));
}