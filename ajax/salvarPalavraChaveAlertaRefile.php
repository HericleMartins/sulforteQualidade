<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new RefilePalavraChaveAlerta();

SqlServer::abrirTransacao();

if($r['idRefilePalavraChaveAlerta'] != ''){

    //edição
    $id = (int)$r['idRefilePalavraChaveAlerta'];

    $status = $obj->editar(array('refilePalavraChaveAlerta' => utf8_decode($r['refilePalavraChaveAlerta']), 'mensagem' => utf8_decode($r['mensagem'])), 'idRefilePalavraChaveAlerta = ' . $r['idRefilePalavraChaveAlerta']);
} else {

    //cadastro
    $dados = array(
        'refilePalavraChaveAlerta' => utf8_decode($r['refilePalavraChaveAlerta']),
        'mensagem' => utf8_decode($r['mensagem']),
        'dataCriacao' => getData(),
        'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario']
    );

    $status = $obj->cadastrar($dados);
}

if(!$status){
    SqlServer::cancelarTransacao();
    if($r['idRefilePalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx001e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx002e')));
    }
    die();
} else {
    SqlServer::confirmarTransacao();
    if($r['idRefilePalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx003s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx004s')));
    }
}