<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new Observacao();

SqlServer::abrirTransacao();

if($r['idobservacao'] != ''){

    //edi��o
    $id = (int)$r['idobservacao'];

    $status = $obj->editar(array('observacao' => $r['observacao'], 'idtipoMaquina' => $r['idtipoMaquina']), 'idobservacao = ' . $r['idobservacao']);
} else {

    //cadastro
    $dados = array(
        'observacao'        => $r['observacao'],
        'idtipoMaquina'     => $r['idtipoMaquina'],
        'dataCriacao'       => getData(),
        'idusuario'         => $_SESSION[SESSAO_SISTEMA]['idusuario']
    );

    $status = $obj->cadastrar($dados);
}

if(!$status){
    SqlServer::cancelarTransacao();
    if($r['idobservacao'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OBx001e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OBx002e')));
    }
    die();
} else {
    SqlServer::confirmarTransacao();
    if($r['idobservacao'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OBx003s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OBx004s')));
    }
}