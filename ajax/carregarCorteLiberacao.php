<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new CorteLiberacao($request['idcorteLiberacao']);
$arraySetup = $obj->carregar();

$arraySetup['soldaTexto'] = $arrCorteQualidadeSolda[$arraySetup['solda']][2];
$arraySetup['sacariaTexto'] = $arrCorteQualidadeSacaria[$arraySetup['sacaria']][2];
$arraySetup['impressaoTexto'] = $arrCorteQualidadeImpressao[$arraySetup['impressao']][2];
$arraySetup['pistaTexto'] = $arrCortePista[$arraySetup['pista']][2];

$objObservacao = new CorteLiberacaoObservacao();
$arrayObservacao = $objObservacao->listar('e.idcorteLiberacao = ' . $arraySetup['idcorteLiberacao'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arraySetup['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode(array_map('utf8_encode', $arraySetup));