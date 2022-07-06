<?php
include( "../inc/load.php");

$obj = CodigoFaca::listar();

if($obj) {
    foreach ($obj as $registro) {
        $xml .= '<registro>';
        $xml .= '<id>' . $registro['idcodigoFaca'] . '</id>';
        $xml .= '<nome><![CDATA[' . $registro['codigoFaca'] . ']]></nome>';
        $xml .= '</registro>';
    }
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<registros>';
echo $xml;
echo '</registros>';