<?php
include( "../inc/load.php");

$obj = new Usuario($_SESSION[SESSAO_SISTEMA]['idusuario']);
$array = $obj->carregarDadosSenha();

$senha = isset($_POST['senha']) ? trim(addslashes(str_replace(' ', '', $_POST['senha']))) : FALSE;

if (!strcmp($array['senha'], sha1($senha))) {
    $status = true;
    $msg = false;
} else {
    $status = false;
    $msg = montarMensagemRetorno('PCx009e');
}

echo retornarJsonEncode(array('status' => $status, 'msg' => $msg));
