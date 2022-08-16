<?php
include( "../inc/load.php");

$request = getRequest($_REQUEST);

$obj = new OrdemProducao($request['idordemProducao']);
$arrayOrdem = $obj->carregar();

$arrayOrdem['larguraBalao'] = number_format($arrayOrdem['larguraBalao'], 2, ',', '');
$arrayOrdem['pesoExtrusao'] = number_format($arrayOrdem['pesoExtrusao'], 2, ',', '');

$arrayOrdem['cliente'] = truncateString($arrayOrdem['cliente'], 28);

if($request['idextrusoraPresetup'] != '') {
    $objMaterias = new ExtrusoraPresetupMateria();
    $arrayMaterias = $objMaterias->listarCompleto('s.idextrusoraPresetup = ' . $request['idextrusoraPresetup'], 'p.materiaPrima');
}

if(SEM_CONTROLE_SANFONA == $arrayOrdem['sanfona']){
    $arrayOrdem['sanfona'] = '2';
}

if(strtoupper(COM_CONTROLE_IMPRESSAO) != strtoupper($arrayOrdem['impresso'])){
    $arrayOrdem['impresso'] = '2';
}

$arrayOrdem['materias'] = retornarJsonEncode($arrayMaterias);

echo retornarJsonEncode(array_map('utf8_encode', $arrayOrdem));