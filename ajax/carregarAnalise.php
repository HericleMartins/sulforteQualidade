<?php
include( "../inc/load.php");
$usuario = new Usuario();
$idUsuario = $_POST['idUsuario'];
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];
$setor = 'carregarAnalises'.ucfirst($_POST['setor']);
$retorno = $usuario->$setor($idUsuario,$dataInicial,$dataFinal);
echo json_encode($retorno);
