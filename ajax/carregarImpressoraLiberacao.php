<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new ImpressoraLiberacao($request['idimpressoraLiberacao']);
$arraySetup = $obj->carregar();

$arraySetup['sentidoDesbobinamentoTexto'] = $arrImpressaoSentidoDesbobinamento[$arraySetup['sentidoDesbobinamento']][2];
$arraySetup['testeAderenciaTintaTexto'] = $arrImpressaoTesteAderenciaTinta[$arraySetup['testeAderenciaTinta']][2];
$arraySetup['leituraCodigoBarrasTexto'] = $arrImpressaoLeituraCodigoBarras[$arraySetup['leituraCodigoBarras']][2];
$arraySetup['ladoTratamentoTexto'] = $arrImpressaoLadoTratamento[$arraySetup['ladoTratamento']][2];
$arraySetup['conferenciaArteTexto'] = $arrImpressaoConferenciaArte[$arraySetup['conferenciaArte']][2];
$arraySetup['tonalidadeTexto'] = $arrImpressaoTonalidade[$arraySetup['tonalidade']][2];
$arraySetup['analiseVisualTexto'] = $arrImpressaoAnaliseVisual[$arraySetup['analiseVisual']][2];


$objObservacao = new ImpressoraLiberacaoObservacao();
$arrayObservacao = $objObservacao->listar('e.idimpressoraLiberacao = ' . $arraySetup['idimpressoraLiberacao'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arraySetup['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode( $arraySetup);