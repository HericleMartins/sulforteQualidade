<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new RefileAnalise($request['idrefileAnalise']);
$arrayAnalise = $obj->carregar();

$arrayAnalise['picoteTexto'] = $arrRefileQualidadePicote[$arrayAnalise['picote']][2];
$arrayAnalise['novaBobinaTexto'] = $arrRefileQualidadeNovaBobina[$arrayAnalise['novaBobina']][2];
$arrayAnalise['impressaoTexto'] = $arrRefileQualidadeImpressao[$arrayAnalise['impressao']][2];

$objObservacao = new RefileAnaliseObservacao();
$arrayObservacao = $objObservacao->listar('e.idrefileAnalise = ' . $arrayAnalise['idrefileAnalise'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arrayAnalise['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode( $arrayAnalise);