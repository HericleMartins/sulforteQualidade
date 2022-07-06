<?php
include( "../inc/load.php");

$r = getRequest($_POST);

$obj = OrdemProducaoBobina::listar('idordemProducao = ' . $r['idpai'],'numero');
if ($obj) {
    foreach ($obj as $registro) {
        $xml .= '<registro>';
        $xml .= '<id>' . $registro['idordemProducaoBobina'] . '</id>';
        $xml .= '<nome><![CDATA[' . $registro['numero'] . ']]></nome>';
        $xml .= '</registro>';
    }
}
header('Content-Type: application/xml; charset=UTF-8');
echo '<registros>';
echo $xml;
echo '</registros>';