<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new RefileLiberacao($request['idrefileLiberacao']);
$arraySetup = $obj->carregar();

$arraySetup['picoteTexto'] = $arrRefileQualidadePicote[$arraySetup['picote']][2];
$arraySetup['novaBobinaTexto'] = $arrRefileQualidadeNovaBobina[$arraySetup['novaBobina']][2];
$arraySetup['impressaoTexto'] = $arrRefileQualidadeImpressao[$arraySetup['impressao']][2];

$objObservacao = new RefileLiberacaoObservacao();
$arrayObservacao = $objObservacao->listar('e.idrefileLiberacao = ' . $arraySetup['idrefileLiberacao'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arraySetup['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode(array_map('utf8_encode', $arraySetup));