<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$objUsuario = new Usuario();

SqlServer::abrirTransacao();

if ($r['senha'] != $r['senhaConfirma']) {
    SqlServer::cancelarTransacao();
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx003e')));
    die();
}

if($r['idusuario'] != '') { //edição

    $dados = array(
        'usuario' => utf8_decode($r['usuario']),
        'email' => $r['email'],
        'senha' => ($r['senha'] ? sha1($r['senha']) : false),
        'idgrupo' => $r['idgrupo'],
        'dataCadastro' => getData(),
        'numeroTentativa' => '0',
        'telefone' => null
    );
    $status = $objUsuario->editar($dados, 'idusuario=' . $r['idusuario']);

    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('UUx005s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx006e')));
    }

} else { //cadastro

    if (!$r['senha']) {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx004e')));
        die();
    }

    $dados = array(
        'usuario' => utf8_decode($r['usuario']),
        'email' => $r['email'],
        'senha' => sha1($r['senha']),
        'idgrupo' => $r['idgrupo'],
        'dataCadastro' => getData(),
        'numeroTentativa' => '0',
        'telefone' => null
    );
    $status = $objUsuario->cadastrar($dados);

    if ($status){
        SqlServer::confirmarTransacao();
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('UUx001s')));
    } else {
        SqlServer::cancelarTransacao();
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('UUx002e')));
    }
}