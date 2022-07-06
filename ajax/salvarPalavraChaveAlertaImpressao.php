<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new ImpressaoPalavraChaveAlerta();

SqlServer::abrirTransacao();

if($r['idImpressaoPalavraChaveAlerta'] != ''){

    //edição
    $id = (int)$r['idImpressaoPalavraChaveAlerta'];

    $status = $obj->editar(array('impressaoPalavraChaveAlerta' => utf8_decode($r['impressaoPalavraChaveAlerta']), 'mensagem' => utf8_decode($r['mensagem'])), 'idImpressaoPalavraChaveAlerta = ' . $r['idImpressaoPalavraChaveAlerta']);
} else {

    //cadastro
    $dados = array(
        'impressaoPalavraChaveAlerta' => utf8_decode($r['impressaoPalavraChaveAlerta']),
        'mensagem' => utf8_decode($r['mensagem']),
        'dataCriacao' => getData(),
        'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario']
    );

    $status = $obj->cadastrar($dados);
}

if(!$status){
    SqlServer::cancelarTransacao();
    if($r['idImpressaoPalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx001e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx002e')));
    }
    die();
} else {
    SqlServer::confirmarTransacao();
    if($r['idImpressaoPalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx003s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx004s')));
    }
}