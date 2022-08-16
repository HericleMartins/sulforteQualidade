<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new CorteAnalise($request['idcorteAnalise']);
$arrayAnalise = $obj->carregar();

$arrayAnalise['soldaTexto'] = $arrCorteQualidadeSolda[$arrayAnalise['solda']][2];
$arrayAnalise['sacariaTexto'] = $arrCorteQualidadeSacaria[$arrayAnalise['sacaria']][2];
$arrayAnalise['impressaoTexto'] = $arrCorteQualidadeImpressao[$arrayAnalise['impressao']][2];
$arrayAnalise['pistaTexto'] = $arrCortePista[$arrayAnalise['pista']][2];

$objObservacao = new CorteAnaliseObservacao();
$arrayObservacao = $objObservacao->listar('e.idcorteAnalise = ' . $arrayAnalise['idcorteAnalise'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arrayAnalise['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode(array_map('utf8_encode', $arrayAnalise));