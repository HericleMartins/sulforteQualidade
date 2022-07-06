<?php
include( "../inc/load.php");
$idUsuario = $_POST['idUsuario'];
$dataInicial = $_POST['dataInicial'];
$dataFinal = $_POST['dataFinal'];

$objOrdemProducaoBobina = new OrdemProducaoBobina();
$aBobinasNaoAnalisadas = $objOrdemProducaoBobina->quantidadeBobinasNaoAnalisadas($dataInicial,$dataFinal);

$objOrdemProducao = new OrdemProducao();
$aOrdensPendentes = $objOrdemProducao->quantidadePendencia($dataInicial,$dataFinal);
$aOrdensPreSemSetup = $objOrdemProducao->semPreSetup($idUsuario,$dataInicial,$dataFinal);

$objUsuario = new Usuario();
$aIndicesUsuario = $objUsuario->carregarIndiceAnalista($idUsuario,$dataInicial,$dataFinal);



$arrayResultado = array();
$ordensPendentes['ordensPendentes'] = $aOrdensPendentes != false ? sizeof($aOrdensPendentes): 0;

$resultado['quantidadeBobinasNaoAnalisadas'] = $aBobinasNaoAnalisadas[0]['quantidadeBobinasNaoAnalisadas'];
$resultado['ordensPendentes'] = $ordensPendentes['ordensPendentes'];
$resultado['quantidadeSemPreSetup'] = $aOrdensPreSemSetup[0]['quantidadeSemPreSetup'];
$resultado['indiceAnalista'] = $aIndicesUsuario;
echo json_encode($resultado);