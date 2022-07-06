<?php
include( "../inc/load.php");

$obj = MateriaPrima::listarSalientando($_POST['idpai']);

$divisor = false;
foreach ($obj as $key => $registro) {

    if($key != 0 && !$divisor && $registro['qtd'] == 0){
        $xml .= '<registro>';
        $xml .= '<id></id>';
        $xml .= '<nome></nome>';
        $xml .= '<divisor>1</divisor>';
        $xml .= '</registro>';
        $divisor = true;
    }
    $attr = (!in_array(strtoupper($registro['possuiLote']),$arrMPnaoPossuiLote) ? "possuiLote=1" : false);
    $xml .= '<registro>';
    $xml .= '<id>'.$registro['idmateriaPrima'].'</id>';
    $xml .= '<nome><![CDATA['.$registro['materiaPrima'].']]></nome>';
    $xml .= '<attr><![CDATA[' . $attr . ']]></attr>';
    $xml .= '</registro>';
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<registros>';
echo $xml;
echo '</registros>';