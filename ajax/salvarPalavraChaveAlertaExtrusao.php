<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new ExtrusaoPalavraChaveAlerta();

SqlServer::abrirTransacao();

if($r['idExtrusaoPalavraChaveAlerta'] != ''){

    //edição
    $id = (int)$r['idExtrusaoPalavraChaveAlerta'];

    $status = $obj->editar(array('extrusaoPalavraChaveAlerta' => $r['extrusaoPalavraChaveAlerta'], 'mensagem' => $r['mensagem']), 'idExtrusaoPalavraChaveAlerta = ' . $r['idExtrusaoPalavraChaveAlerta']);
} else {

    //cadastro
    $dados = array(
        'extrusaoPalavraChaveAlerta' => $r['extrusaoPalavraChaveAlerta'],
        'mensagem' => $r['mensagem'],
        'dataCriacao' => getData(),
        'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario']
    );

    $status = $obj->cadastrar($dados);
}

if(!$status){
    SqlServer::cancelarTransacao();
    if($r['idExtrusaoPalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx001e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx002e')));
    }
    die();
} else {
    SqlServer::confirmarTransacao();
    if($r['idExtrusaoPalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx003s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx004s')));
    }
}