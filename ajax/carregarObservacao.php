<?php
include( "../inc/load.php");

$obj = Observacao::listar('situacao IS NULL AND o.idtipoMaquina = ' . $_REQUEST['idpai']);

if($obj) {
    foreach ($obj as $registro) {
        $xml .= '<registro>';
        $xml .= '<id>' . $registro['idobservacao'] . '</id>';
        $xml .= '<nome><![CDATA[' . $registro['observacao'] . ']]></nome>';
        $xml .= '</registro>';
    }
}

header('Content-Type: application/xml; charset=');
echo '<registros>';
echo $xml;
echo '</registros>';