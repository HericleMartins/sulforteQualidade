<?php
include( "../inc/load.php");

$obj = new OrdemProducao();
$arrayResultado = $obj->listarPendencia('quantRegistro = 0', 'numero DESC, maquina ASC');

echo retornarJsonEncode($arrayResultado);