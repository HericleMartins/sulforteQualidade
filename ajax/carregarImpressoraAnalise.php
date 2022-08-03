<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new ImpressoraAnalise($request['idimpressoraAnalise']);
$arrayAnalise = $obj->carregar();

$arrayAnalise['sentidoDesbobinamentoTexto'] = $arrImpressaoSentidoDesbobinamento[$arrayAnalise['sentidoDesbobinamento']][2];
$arrayAnalise['testeAderenciaTintaTexto'] = $arrImpressaoTesteAderenciaTinta[$arrayAnalise['testeAderenciaTinta']][2];
$arrayAnalise['leituraCodigoBarrasTexto'] = $arrImpressaoLeituraCodigoBarras[$arrayAnalise['leituraCodigoBarras']][2];
$arrayAnalise['ladoTratamentoTexto'] = $arrImpressaoLadoTratamento[$arrayAnalise['ladoTratamento']][2];
$arrayAnalise['conferenciaArteTexto'] = $arrImpressaoConferenciaArte[$arrayAnalise['conferenciaArte']][2];
$arrayAnalise['tonalidadeTexto'] = $arrImpressaoTonalidade[$arrayAnalise['tonalidade']][2];
$arrayAnalise['analiseVisualTexto'] = $arrImpressaoAnaliseVisual[$arrayAnalise['analiseVisual']][2];

$objObservacao = new ImpressoraAnaliseObservacao();
$arrayObservacao = $objObservacao->listar('e.idimpressoraAnalise = ' . $arrayAnalise['idimpressoraAnalise'], 'e.idobservacao');

if($arrayObservacao) {
    foreach ($arrayObservacao as $key => $value) {
        $arrayObservacao[$key]['observacao'] = $arrayObservacao[$key]['observacao'];
    }
}

$arrayAnalise['observacoes'] = retornarJsonEncode($arrayObservacao);

echo retornarJsonEncode($arrayAnalise);