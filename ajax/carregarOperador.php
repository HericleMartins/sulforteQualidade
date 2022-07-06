<?php
include( "../inc/load.php");

$obj = Operador::listar('naoMostrar = 0 AND situacao IS NULL AND o.idtipoMaquina = ' . $_REQUEST['idpai']);

if($obj) {
    foreach ($obj as $registro) {
        $xml .= '<registro>';
        $xml .= '<id>' . $registro['idoperador'] . '</id>';
        $xml .= '<nome><![CDATA[' . $registro['operador'] . ']]></nome>';
        $xml .= '</registro>';
    }
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<registros>';
echo $xml;
echo '</registros>';