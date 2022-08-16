<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new ExtrusoraAnalise($request['idextrusoraAnalise']);
$arrayAnalise = $obj->carregar();

$arrayAnalise['impressaoTexto'] = $arrExtrusaoTratamentoImpressao[$arrayAnalise['impressao']][2];
$arrayAnalise['faceamentoTexto'] = $arrExtrusaoFaceamento[$arrayAnalise['faceamento']][2];

$objObservacao = new ExtrusoraAnaliseObservacao();
$arrayObservacao = $objObservacao->listar('e.idextrusoraAnalise = ' . $arrayAnalise['idextrusoraAnalise'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arrayAnalise['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode(array_map('utf8_encode', $arrayAnalise));