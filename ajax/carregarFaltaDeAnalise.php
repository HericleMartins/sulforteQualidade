<?php
include( "../inc/load.php");
$classe = ucfirst($_POST['classe']);
$funcao = $_POST['funcao'];
$idUsuario = $_POST['idUsuario'];
$obj = new $classe();
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$retorno = $obj->$funcao($idUsuario,$dataInicial,$dataFinal);
echo json_encode($retorno);
