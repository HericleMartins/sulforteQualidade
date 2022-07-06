<?php
include( "../inc/load.php");
$usuario = new Usuario();
$idUsuario = $_GET['idUsuario'];
$dataInicial = $_GET['dataInicial'];
$dataFinal = $_GET['dataFinal'];
echo $_GET['setor'];
$setor = 'carregarAnalises'.ucfirst($_GET['setor']);
echo '<br>'.$setor.'<br>';
$retorno = $usuario->$setor($idUsuario,$dataInicial,$dataFinal);
echo json_encode($retorno);
