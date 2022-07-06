<?php

include( "../inc/load.php");

$obj = ViewObservacaoBobina::listar('idordemProducaoBobina = ' . $_REQUEST['bobina'] . ' AND (obs IS NOT NULL OR observacoes IS NOT NULL)','dataCriacao DESC');

$r = "";

if($obj) {
    foreach ($obj as $d) {
        /*$icone = "";

        if ($d['tipo'] == 1){
            $icone = '<i class="fa fa-circle text-warning mr-xxs" aria-hidden="true"></i>';
        } else {
            $icone = '<i class="fa fa-circle text-info mr-xxs" aria-hidden="true"></i>';
        }*/

        $obs = array();

        if ($d['observacoes']) {
            $obs = explode(",", $d['observacoes']);
        }

        if ($d['obs']) {
            array_unshift($obs, $d['obs']);
        }

        $r .= "
            <tr>
                <td>" . $icone . "" . ($obs ? implode(", ", $obs) : "-")  . "</td>
                <td>" . $d['data'] . " - " . substr($d['hora'], 0, 5) . "</td>
                <td>" . $d['analista'] . "</td>
            </tr>";
    }
} else {
    $r .= "
            <tr>
                <td colspan='3' align='center'>Nenhum registro encontrado</td>
            </tr>";
}

echo $r;

