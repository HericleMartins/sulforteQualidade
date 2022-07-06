<?php
ini_set('max_execution_time', 1000);
ini_set('memory_limit', '1000M');

include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/consultarRegistro.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Consultar registros");

$post = getRequest($_POST);

montarSelect(TipoMaquina::listar(null, 'idtipoMAquina'), 'ListaMaquina', ($post['idtipoMaquina'] ? $post['idtipoMaquina'] : false),true);

if($post['idtipoMaquina']){
    montarSelect($arrEtapaMaquinaSelect[$post['idtipoMaquina']], 'ListaEtapa', $post['tipo'], true);
} else {
    montarSelect(array(1 => array('', '', 'Selecione um tipo de máquina')), 'ListaEtapa', false,false);
}

$where = '';

//gravando filtro em sessão para excel--------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['filtro-numeroOP'] = $post['numeroOP'];
$_SESSION[SESSAO_SISTEMA]['filtro-periodoInicial'] = $post['periodoInicial'];
$_SESSION[SESSAO_SISTEMA]['filtro-periodoFinal'] = $post['periodoFinal'];
$_SESSION[SESSAO_SISTEMA]['filtro-tipo'] = $post['tipo'];
$_SESSION[SESSAO_SISTEMA]['filtro-idtipoMaquina'] = $post['idtipoMaquina'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------
$filtro = false;

if($post['numeroOP'] != ''){
    $where .= 'numeroOp = ' . $post['numeroOP'] . ' AND ';
    $objTpl->assign("numeroOP", $post['numeroOP']);
    $filtro = true;
}

if($post['periodoInicial'] != ''){
    $where .= ' CAST(dataCriacao AS DATE) >= "' . $post['periodoInicial'] . '" AND ';
    $objTpl->assign("periodoInicial", formatardata($post['periodoInicial'], '/'));
    $filtro = true;
}

if($post['periodoFinal'] != ''){
    $where .= ' CAST(dataCriacao AS DATE) <= "' . $post['periodoFinal'] . '" AND ';
    $objTpl->assign("periodoFinal", formatardata($post['periodoFinal'], '/'));
    $filtro = true;
}

if($post['tipo'] != ''){
    $where .= ' tipo = "' . $post['tipo'] . '" AND ';
    $objTpl->assign("tipo", $post['tipo']);
    $filtro = true;
}

if($post['idtipoMaquina'] != ''){
    $where .= ' idtipoMaquina = "' . $post['idtipoMaquina'] . '" AND ';
    $objTpl->assign("idtipoMaquina", $post['idtipoMaquina']);
    $filtro = true;
}

//paginação-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaConsulta'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

if (!$filtro) {

    $where .= ' CAST(dataCriacao AS DATE) >= "' . date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))) . '" AND ';
    $objTpl->assign("periodoInicial", formatarData(date('Y-m-d', mktime(0, 0, 0, date("m"), date("d")-7, date("Y"))), '/'));

}

ViewDetalheRegistro::listarPaginacao($where, 'CAST(numeroOP AS INT) DESC, dataCriacao DESC');

$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaConsulta'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaConsulta'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {



        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaConsulta']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginação-----------------------------------------------------------------------------------------------------------------

//if ($filtro) {
    $arrayRegistros = ViewDetalheRegistro::listar($where, 'CAST(numeroOP AS INT) DESC, dataCriacao DESC', $_SESSION[SESSAO_SISTEMA]['paginaConsulta']);
//} else {
//    $arrayRegistros = ViewDetalheRegistro::listarLimite($where, 'CAST(numeroOP AS INT) DESC, dataCriacao DESC', $_SESSION[SESSAO_SISTEMA]['paginaConsulta']);
//}

$idordemProducao = 0;
if($arrayRegistros){
    foreach ($arrayRegistros as $k => $v){
        if($idordemProducao != $v['idordemProducao']){
            $objTpl->newBlock("listarOP");
            $objTpl->assign("numero-op", $v['numeroOP']);
            $objTpl->assign("nome-cliente", $v['cliente']);

            $idordemProducao = $v['idordemProducao'];
        }

        $objTpl->newBlock("listarMovimentos");

        $objTpl->assign("classe-maquina", $v['idtipoMaquina']);
        $objTpl->assign("maquina", $v['tipoMaquina']. " " . str_pad($v['maquina'], 2, 0, STR_PAD_LEFT));
        $objTpl->assign("etapa", $arrEtapaMaquina[$v['tipo']]);

        $detalhe = '';
        if($v['tipo'] == 1){

            if($v['semVistoria'] == '1'){
                $detalhe = 'Pré-setup sem vistoria do analista';
            } else {
                $detalhe = $v['detalhe'] . ($v['observacaoPresetup'] ? '; Obs: ' . $v['observacaoPresetup'] : false);
            }
            $objTpl->assign("icone", 'cogs');

        } else if($v['tipo'] == 2){

            if($v['semVistoria'] == '1'){
                $detalhe = 'Setup sem vistoria do analista';
            } else if($v['tipoMedida'] == '1'){
                $detalhe = $v['numero'] .
                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Espessura: ' . number_format($v['espessuraMin'], 2, ',', '') . ' - ' . number_format($v['espessuraMax'], 2, ',', '') .
                    ($v['sanfonaEsq'] != '' || $v['sanfonaDir'] != '' ?
                        ', Sanfona: ' . number_format($v['sanfonaEsq'], 2, ',', '') . ' - ' . number_format($v['sanfonaDir'], 2, ',', '') : '') .

                    ($v['faceamento'] != '' ?
                        ', Faceamento: ' . $arrExtrusaoFaceamento[$v['faceamento']][2] : '') .

                    ($v['tratImpressao'] != '' ?
                        ', Tratamento impressão: ' . $arrExtrusaoTratamentoImpressao[$v['tratImpressao']][2] : '') .

                    ', Operador: ' . $v['operador'];
            } else {
                $detalhe = $v['numero'] .
                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Espessura média: ' . number_format($v['espessuraMedia'], 2, ',', '') . ' (' . number_format($v['espessuraLargura'], 2, ',', '') . 'x' . number_format($v['espessuraComprimento'], 2, ',', '') . 'x' . number_format($v['espessuraPeso'], 2, ',', ''). ')' .
                    ($v['sanfonaEsq'] != '' || $v['sanfonaDir'] != '' ?
                        ', Sanfona: ' . number_format($v['sanfonaEsq'], 2, ',', '') . ' - ' . number_format($v['sanfonaDir'], 2, ',', '') : '') .

                    ($v['faceamento'] != '' ?
                        ', Faceamento: ' . $arrExtrusaoFaceamento[$v['faceamento']][2] : '') .

                    ($v['tratImpressao'] != '' ?
                        ', Tratamento impressão: ' . $arrExtrusaoTratamentoImpressao[$v['tratImpressao']][2] : '') .

                    ', Operador: ' . $v['operador'];
            }

            $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $objTpl->assign("observacoes", substr($obs, 0, -2));

            $objTpl->assign("icone", 'check-circle-o');

        } else if($v['tipo'] == 3){
            if($v['semAnalise'] == '1'){
                $detalhe = 'Bobina ' . $v['numero'] . ' não analisada';
            } else if($v['tipoMedida'] == '2'){
                $detalhe = 'Bobina ' . $v['numero'] .
                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Espessura média: ' . number_format($v['espessuraMedia'], 2, ',', '') . ' (' . number_format($v['espessuraLargura'], 2, ',', '') . 'x' . number_format($v['espessuraComprimento'], 2, ',', '') . 'x' . number_format($v['espessuraPeso'], 2, ',', ''). ')' .
                    ($v['sanfonaEsq'] != '' || $v['sanfonaDir'] != '' ?
                        ', Sanfona: ' . number_format($v['sanfonaEsq'], 2, ',', '') . ' - ' . number_format($v['sanfonaDir'], 2, ',', '') : '') .

                    ($v['faceamento'] != '' ?
                        ', Faceamento: ' . $arrExtrusaoFaceamento[$v['faceamento']][2] : '') .

                    ($v['tratImpressao'] != '' ?
                        ', Tratamento impressão: ' . $arrExtrusaoTratamentoImpressao[$v['tratImpressao']][2] : '') .

                    ', Operador: ' . $v['operador'];
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .
                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Espessura: ' . number_format($v['espessuraMin'], 2, ',', '') . ' - ' . number_format($v['espessuraMax'], 2, ',', '') .
                    ($v['sanfonaEsq'] != '' || $v['sanfonaDir'] != '' ?
                    ', Sanfona: ' . number_format($v['sanfonaEsq'], 2, ',', '') . ' - ' . number_format($v['sanfonaDir'], 2, ',', '') : '') .

                    ($v['faceamento'] != '' ?
                        ', Faceamento: ' . $arrExtrusaoFaceamento[$v['faceamento']][2] : '') .

                    ($v['tratImpressao'] != '' ?
                        ', Tratamento impressão: ' . $arrExtrusaoTratamentoImpressao[$v['tratImpressao']][2] : '') .

                    ', Operador: ' . $v['operador'];
            }


            $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ' );
            $objTpl->assign("observacoes", substr($obs, 0, -2));
            $objTpl->assign("icone", 'eye');

        } else if($v['tipo'] == 4) {

            if ($v['semVistoria'] == '1') {
                $detalhe = 'Liberação sem vistoria do analista';
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .

                    ($v['reinspecao'] == 1 ?
                        ' <span style="font-size: 7pt">(R)</span>' : '') .

                    ($v['pista'] != '' ? ' (' . $arrCortePista[$v['pista']][2] . ')' : '' ) .
                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Comprimento: ' . number_format($v['comprimento'], 2, ',', '')
                    . ($v['sanfonaEsq'] != '' || $v['sanfonaDir'] != '' ?
                        ', Sanfona: ' . number_format($v['sanfonaEsq'], 2, ',', '') . ' - ' . number_format($v['sanfonaDir'], 2, ',', '') : '')
                    . ($v['solda'] != '' && $v['solda'] > 0 ? ', Solda: ' . $arrCorteQualidadeSolda[$v['solda']][2] : '')
                    . ', Sacaria: ' . $arrCorteQualidadeSacaria[$v['sacaria']][2]
                    . ', Código faca: ' . ($v['codigoFaca'] ? $v['codigoFaca'] : '-')
                    . ($v['impressao'] != '' && $v['impressao'] > 0 ? ', Impressão: ' . $arrCorteQualidadeImpressao[$v['impressao']][2] : '') .
                    ', Operador: ' . $v['operador'];

                $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $objTpl->assign("observacoes", substr($obs, 0, -2));
            }
            $objTpl->assign("icone", 'check-circle-o');

        } else if($v['tipo'] == 5) {
            if($v['semAnalise'] == '1'){
                $detalhe = 'Bobina ' . $v['numero'] . ' não analisada';
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .

                    ($v['reinspecao'] == 1 ?
                        ' <span style="font-size: 7pt">(R)</span>' : '') .

                    ($v['pista'] != '' ? ' (' . $arrCortePista[$v['pista']][2] . ')' : '') .
                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Comprimento: ' . number_format($v['comprimento'], 2, ',', '')
                    . ($v['sanfonaEsq'] != '' || $v['sanfonaDir'] != '' ?
                        ', Sanfona: ' . number_format($v['sanfonaEsq'], 2, ',', '') . ' - ' . number_format($v['sanfonaDir'], 2, ',', '') : '')
                    . ($v['solda'] != '' && $v['solda'] > 0 ? ', Solda: ' . $arrCorteQualidadeSolda[$v['solda']][2] : '')
                    . ', Sacaria: ' . $arrCorteQualidadeSacaria[$v['sacaria']][2]
                    . ', Código faca: ' . ($v['codigoFaca'] ? $v['codigoFaca'] : '-')
                    . ($v['impressao'] != '' && $v['impressao'] > 0 ? ', Impressão: ' . $arrCorteQualidadeImpressao[$v['impressao']][2] : '') .
                    ', Operador: ' . $v['operador'];
            }
            $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $objTpl->assign("observacoes", substr($obs, 0, -2));

            $objTpl->assign("icone", 'check-circle-o');

        } else if($v['tipo'] == 6) {

            if ($v['semVistoria'] == '1') {
                $detalhe = 'Liberação sem vistoria do analista';
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .

                    ($v['reinspecao'] == 1 ?
                        ' <span style="font-size: 7pt">(R)</span>' : '') .

                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Comprimento: ' . ($v['comprimento'] != '' ? number_format($v['comprimento'], 2, ',', '') : '')
                    . ($v['picote'] != '' && $v['picote'] > 0 ? ', Picote: ' . $arrRefileQualidadePicote[$v['picote']][2] : '')
                    . ', Nova bobina: ' . $arrRefileQualidadeNovaBobina[$v['novaBobina']][2]
                    . ($v['impressao'] != '' && $v['impressao'] > 0 ? ', Impressão: ' . $arrRefileQualidadeImpressao[$v['impressao']][2] : '') .
                    ', Operador: ' . $v['operador'];

                $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $objTpl->assign("observacoes", substr($obs, 0, -2));
            }
            $objTpl->assign("icone", 'check-circle-o');

        } else if($v['tipo'] == 7) {
            if($v['semAnalise'] == '1'){
                $detalhe = 'Bobina ' . $v['numero'] . ' não analisada';
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .

                    ($v['reinspecao'] == 1 ?
                        ' <span style="font-size: 7pt">(R)</span>' : '') .

                    ', Largura: ' . number_format($v['largura'], 2, ',', '') .
                    ', Comprimento: ' . ($v['comprimento'] != '' ? number_format($v['comprimento'], 2, ',', '') : '')
                    . ($v['picote'] != '' && $v['picote'] > 0 ? ', Picote: ' . $arrRefileQualidadePicote[$v['picote']][2] : '')
                    . ', Nova bobina: ' . $arrRefileQualidadeNovaBobina[$v['novaBobina']][2]
                    . ($v['impressao'] != '' && $v['impressao'] > 0 ? ', Impressão: ' . $arrRefileQualidadeImpressao[$v['impressao']][2] : '') .
                    ', Operador: ' . $v['operador'];
            }
            $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $objTpl->assign("observacoes", substr($obs, 0, -2));

            $objTpl->assign("icone", 'check-circle-o');
        } else if($v['tipo'] == 8) {

            if ($v['semVistoria'] == '1') {
                $detalhe = 'Liberação sem vistoria do analista';
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .

                    ($v['reinspecao'] == 1 ?
                        ' <span style="font-size: 7pt">(R)</span>' : '') .

                    ', Largura: ' . ($v['larguraEmbalagem'] != '' ? number_format($v['larguraEmbalagem'], 2, ',', '') : '') .
                    ', Passo: ' . ($v['passoEmbalagem'] != '' ? number_format($v['passoEmbalagem'], 2, ',', '') : '')
                    . ($v['analiseVisual'] != '' && $v['analiseVisual'] > 0 ? ', Visual: ' . $arrImpressaoAnaliseVisual[$v['analiseVisual']][2] : '')
                    . ($v['tonalidade'] != '' && $v['tonalidade'] > 0 ? ', Tonalidade: ' . $arrImpressaoTonalidade[$v['tonalidade']][2] : '')
                    . ($v['conferenciaArte'] != '' && $v['conferenciaArte'] > 0 ? ', Arte: ' . $arrImpressaoConferenciaArte[$v['conferenciaArte']][2] : '')
                    . ($v['ladoTratamento'] != '' && $v['ladoTratamento'] > 0 ? ', Lado: ' . $arrImpressaoLadoTratamento[$v['ladoTratamento']][2] : '')
                    . ($v['leituraCodigoBarras'] != '' && $v['leituraCodigoBarras'] > 0 ? ', Código barras: ' . $arrImpressaoLeituraCodigoBarras[$v['leituraCodigoBarras']][2] : '')
                    . ($v['testeAderenciaTinta'] != '' && $v['testeAderenciaTinta'] > 0 ? ', Tinta: ' . $arrImpressaoTesteAderenciaTinta[$v['testeAderenciaTinta']][2] : '')
                    . ($v['sentidoDesbobinamento'] != '' && $v['sentidoDesbobinamento'] > 0 ? ', Sentido: ' . $arrImpressaoSentidoDesbobinamento[$v['sentidoDesbobinamento']][2] : '')
                    . ', Operador: ' . $v['operador'];

                $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $objTpl->assign("observacoes", substr($obs, 0, -2));
            }
            $objTpl->assign("icone", 'check-circle-o');

        } else if($v['tipo'] == 9) {
            if($v['semAnalise'] == '1'){
                $detalhe = 'Bobina ' . $v['numero'] . ' não analisada';
            } else {
                $detalhe = 'Bobina ' . $v['numero'] .

                    ($v['reinspecao'] == 1 ?
                        ' <span style="font-size: 7pt">(R)</span>' : '') .

                    ', Largura: ' . ($v['larguraEmbalagem'] != '' ? number_format($v['larguraEmbalagem'], 2, ',', '') : '') .
                    ', Passo: ' . ($v['passoEmbalagem'] != '' ? number_format($v['passoEmbalagem'], 2, ',', '') : '')
                    . ($v['analiseVisual'] != '' && $v['analiseVisual'] > 0 ? ', Visual: ' . $arrImpressaoAnaliseVisual[$v['analiseVisual']][2] : '')
                    . ($v['tonalidade'] != '' && $v['tonalidade'] > 0 ? ', Tonalidade: ' . $arrImpressaoTonalidade[$v['tonalidade']][2] : '')
                    . ($v['conferenciaArte'] != '' && $v['conferenciaArte'] > 0 ? ', Arte: ' . $arrImpressaoConferenciaArte[$v['conferenciaArte']][2] : '')
                    . ($v['ladoTratamento'] != '' && $v['ladoTratamento'] > 0 ? ', Lado: ' . $arrImpressaoLadoTratamento[$v['ladoTratamento']][2] : '')
                    . ($v['leituraCodigoBarras'] != '' && $v['leituraCodigoBarras'] > 0 ? ', Código barras: ' . $arrImpressaoLeituraCodigoBarras[$v['leituraCodigoBarras']][2] : '')
                    . ($v['testeAderenciaTinta'] != '' && $v['testeAderenciaTinta'] > 0 ? ', Tinta: ' . $arrImpressaoTesteAderenciaTinta[$v['testeAderenciaTinta']][2] : '')
                    . ($v['sentidoDesbobinamento'] != '' && $v['sentidoDesbobinamento'] > 0 ? ', Sentido: ' . $arrImpressaoSentidoDesbobinamento[$v['sentidoDesbobinamento']][2] : '')
                    . ', Operador: ' . $v['operador'];
            }
            $obs = (trim($v['observacoes']) != '' || trim($v['obs']) != '' ? 'Obs: ' : '') . $v['observacoes'] . (trim($v['observacoes']) != '' && trim($v['obs']) != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $objTpl->assign("observacoes", substr($obs, 0, -2));

            $objTpl->assign("icone", 'check-circle-o');
        }

        $objTpl->assign("data", $v['data']);
        $objTpl->assign("hora", substr($v['hora'], 0, 5));
        $objTpl->assign("operador", $v['usuario']);

        $objTpl->assign("especificacao", $detalhe);
    }
} else {
    $objTpl->gotoBlock('_ROOT');
    $objTpl->assign("disabled", 'disabled="disabled"');
}

$objTpl->printToScreen(true);