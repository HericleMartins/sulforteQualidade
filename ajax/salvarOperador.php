<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new Operador();

if(!(int)$r['codigo'] > 0){
    //C�digo ERP deve ser num�rico inteiro
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OPx001e')));
    die;
}

SqlServer::abrirTransacao();

if($r['idoperador'] != ''){

    //edi��o
    $id = (int)$r['idoperador'];

    $arrayOperador = Operador::listar('codigo = ' . $r['codigo'] . ' AND idoperador <> ' . $id . ' AND o.idtipoMaquina = ' . $r['idtipoMaquina']);

    if($arrayOperador){
        SqlServer::cancelarTransacao();
        //C�digo ERP j� cadastrado para outro operador
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OPx002e')));
        die;
    }

    $status = $obj->editar(array('codigo' => $r['codigo'], 'operador' => $r['operador'], 'idtipoMaquina' => $r['idtipoMaquina']), 'idoperador = ' . $r['idoperador']);
} else {

    $arrayOperador = Operador::listar('codigo = ' . $r['codigo'] . ' AND o.idtipoMaquina = ' . $r['idtipoMaquina']);

    if($arrayOperador){
        SqlServer::cancelarTransacao();
        //C�digo ERP j� cadastrado para outro operador
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OPx003e')));
        die;
    }
    //cadastro
    $dados = array(
        'codigo'            => $r['codigo'],
        'operador'          => $r['operador'],
        'naoMostrar'        => '0',
        'idtipoMaquina'     => $r['idtipoMaquina'],
        'dataCriacao'       => getData(),
        'idusuario'         => $_SESSION[SESSAO_SISTEMA]['idusuario']
    );

    $status = $obj->cadastrar($dados);
}

if(!$status){
    SqlServer::cancelarTransacao();
    if($r['idoperador'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OPx004e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('OPx005e')));
    }
    die();
} else {
    SqlServer::confirmarTransacao();
    if($r['idoperador'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OPx006s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('OPx007s')));
    }
}