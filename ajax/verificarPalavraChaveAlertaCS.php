<?php
include( "../inc/load.php");

$objOS = new OrdemProducao();
$arrayOrdem = $objOS->carregarPor('numero = ' . $_POST['numeroOrdemProducao']);
$r = verificarPalavraChaveAlertaCS($arrayOrdem['item']);
if ($r['status']){
    echo retornarJsonEncode(array('status' => 1, 'msg' => $r['mensagemPalavraChave'] ? $r['mensagemPalavraChave'] : TEXTO_ALERTA_AVISO_CS));
} else {
    echo retornarJsonEncode(array('status' => 0, 'msg' => 'ok'));
}