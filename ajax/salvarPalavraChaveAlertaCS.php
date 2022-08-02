<?php
include("../inc/load.php");

$r = getRequest($_POST);
$obj = new CortePalavraChaveAlerta();

SqlServer::abrirTransacao();

if ($r['idCortePalavraChaveAlerta'] != '') {

    //edição
    $id = (int)$r['idCortePalavraChaveAlerta'];

    $status = $obj->editar(
        array(
            'cortePalavraChaveAlerta' => $r['cortePalavraChaveAlerta'],
            'mensagem' => $r['mensagem']
        ),'idCortePalavraChaveAlerta = ' . $r['idCortePalavraChaveAlerta']);
} else {

    //cadastro
    $dados = array(
        'cortePalavraChaveAlerta' => $r['cortePalavraChaveAlerta'],
        'mensagem' => $r['mensagem'],
        'dataCriacao' => getData(),
        'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario']
    );

    $status = $obj->cadastrar($dados);
}

if (!$status) {
    SqlServer::cancelarTransacao();
    if ($r['idCortePalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx001e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('PCx002e')));
    }
    die();
} else {
    SqlServer::confirmarTransacao();
    if ($r['idCortePalavraChaveAlerta'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx003s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('PCx004s')));
    }
}
