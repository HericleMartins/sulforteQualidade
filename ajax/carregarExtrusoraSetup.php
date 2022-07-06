<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new ExtrusoraSetup($request['idextrusoraSetup']);
$arraySetup = $obj->carregar();

$arraySetup['impressaoTexto'] = $arrExtrusaoTratamentoImpressao[$arraySetup['impressao']][2];
$arraySetup['faceamentoTexto'] = $arrExtrusaoFaceamento[$arraySetup['faceamento']][2];

$objObservacao = new ExtrusoraSetupObservacao();
$arrayObservacao = $objObservacao->listar('e.idextrusoraSetup = ' . $arraySetup['idextrusoraSetup'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = utf8_encode($arrayObservacao[$key]['observacao']);
    }
}

$arraySetup['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode(array_map('utf8_encode', $arraySetup));