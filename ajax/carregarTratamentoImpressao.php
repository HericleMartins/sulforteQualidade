<?php
include( "../inc/load.php");

foreach ($arrExtrusaoTratamentoImpressao as $registro) {
    $xml .= '<registro>';
    $xml .= '<id>'.$registro[1].'</id>';
    $xml .= '<nome><![CDATA[' . $registro[2] . ']]></nome>';
    $xml .= '</registro>';
}

header('Content-Type: application/xml; charset=UTF-8');
echo '<registros>';
echo $xml;
echo '</registros>';