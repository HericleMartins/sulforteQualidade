<?php
ini_set('max_execution_time', 1000);
ini_set('memory_limit', '1000M');

include( "../inc/load.php");
include( "../inc/PHPExcel/PHPExcel.php");

$where = '';

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($_SESSION[SESSAO_SISTEMA]['filtro-numeroOP'] != ''){
    $where .= 'numeroOp = ' . $_SESSION[SESSAO_SISTEMA]['filtro-numeroOP'] . ' AND ';
}

if($_SESSION[SESSAO_SISTEMA]['filtro-periodoInicial'] != ''){
    $where .= ' CAST(dataCriacao AS DATE) >= "' . $_SESSION[SESSAO_SISTEMA]['filtro-periodoInicial'] . '" AND ';
}

if($_SESSION[SESSAO_SISTEMA]['filtro-periodoFinal'] != ''){
    $where .= ' CAST(dataCriacao AS DATE) <= "' . $_SESSION[SESSAO_SISTEMA]['filtro-periodoFinal'] . '" AND ';
}

if($_SESSION[SESSAO_SISTEMA]['filtro-tipo'] != ''){
    $where .= ' tipo = "' . $_SESSION[SESSAO_SISTEMA]['filtro-tipo'] . '" AND ';
}

if($_SESSION[SESSAO_SISTEMA]['filtro-idtipoMaquina'] != ''){
    $where .= ' idtipoMaquina = "' . $_SESSION[SESSAO_SISTEMA]['filtro-idtipoMaquina'] . '" AND ';
}

$arrayRegistros = ViewDetalheRegistro::listar($where, 'CAST(numeroOP AS INT) DESC, dataCriacao DESC', 'false');

ob_end_clean();
ob_start();

$objPHPExcel = new PHPExcel();

$cabecalho = array('OP', 'Cliente', 'M�quina', 'Etapa', 'An�lise/Bobina', 'Peso Bobina', 'Largura', 'Comprimento', 'Espessura M�n.',  'Espessura M�x.', 'Espessura M�dia', 'Amostra (Larg. X Compr. X Peso)', 'Sanfona Esq.', 'Sanfona Dir.', 'Faceamento', 'Tratamento de impress�o', 'Reinspe��o', 'C�digo faca', 'Solda', 'Sacaria', 'Impress�o', 'Picote', 'Nova Bobina', 'Largura Embalagem', 'Passo Embalagem', 'An�lise Visual', 'Tonalidade', 'Confer�ncia Arte', 'Lado Tratamento', 'Leitura C�digo Barras', 'Teste Ader�ncia Tinta', 'Sentido Desbobinamento', 'Operador', 'Detalhe/Observa��o', 'Analista', 'Data', 'Hora');

$coluna = 'A';
foreach($cabecalho as $value){
    $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna . '1', utf8_encode($value));
    $objPHPExcel->getActiveSheet()->getColumnDimension($coluna . '1')->setAutoSize(true);
    $coluna++;
}

$linha = 2;
if(is_array($arrayRegistros)) {


    foreach ($arrayRegistros as $key => $v) {
        $coluna = 'A';

        $peso = ($v['peso'] != '' ? number_format($v['peso'], 2, ',', '') : '');
        $largura = ($v['largura'] != '' ? number_format($v['largura'], 2, ',', '') : '');
        $espessuraMin = ($v['espessuraMin'] != '' ? number_format($v['espessuraMin'], 2, ',', '') : '');
        $espessuraMax = ($v['espessuraMax'] != '' ? number_format($v['espessuraMax'], 2, ',', '') : '');
        $espessuraLargura = ($v['espessuraLargura'] != '' ? number_format($v['espessuraLargura'], 2, ',', '') : '');
        $espessuraComprimento = ($v['espessuraComprimento'] != '' ? number_format($v['espessuraComprimento'], 2, ',', '') : '');
        $espessuraPeso = ($v['espessuraPeso'] != '' ? number_format($v['espessuraPeso'], 2, ',', '') : '');
        $espessuraMedia = ($v['espessuraMedia'] != '' ? number_format($v['espessuraMedia'], 2, ',', '') : '');
        $sanfonaEsq = ($v['sanfonaEsq'] != '' ? number_format($v['sanfonaEsq'], 2, ',', '') : '');
        $sanfonaDir = ($v['sanfonaDir'] != '' ? number_format($v['sanfonaDir'], 2, ',', '') : '');
        $comprimento = ($v['comprimento'] != '' ? number_format($v['comprimento'], 2, ',', '') : '');

        $larguraEmbalagem = ($v['larguraEmbalagem'] != '' ? number_format($v['larguraEmbalagem'], 2, ',', '') : '');
        $passoEmbalagem = ($v['passoEmbalagem'] != '' ? number_format($v['passoEmbalagem'], 2, ',', '') : '');

        $dadosMedia = '';
        if ($espessuraLargura != '' || $espessuraComprimento != '' || $espessuraPeso) {
            $dadosMedia = $espessuraLargura . ' X ' . $espessuraComprimento . ' X ' . $espessuraPeso;
        }

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($v['numeroOP']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($v['cliente']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($v['tipoMaquina'] . " " . str_pad($v['maquina'], 2, 0, STR_PAD_LEFT)));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrEtapaMaquina[$v['tipo']]));

        $bobina = '';
        if ($v['tipo'] == 1 || $v['tipo'] == 2) {
            $bobina = $v['numero'];
        } else {
            if ($v['numero'] != '') {
                $bobina = 'Bobina ' . $v['numero'] . ($v['pista'] != '' ? ' (' . $arrCortePista[$v['pista']][2] . ')' : '');
            }
        }

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($bobina));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $peso);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $largura);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $comprimento);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $espessuraMin);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $espessuraMax);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $espessuraMedia);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $dadosMedia);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $sanfonaEsq);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $sanfonaDir);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrExtrusaoFaceamento[$v['faceamento']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $arrExtrusaoTratamentoImpressao[$v['tratImpressao']][2]);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode(($v['reinspecao'] == 1 ? 'Sim': 'N�o')));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $v['codigoFaca']);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $arrCorteQualidadeSolda[$v['solda']][2]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $arrCorteQualidadeSacaria[$v['sacaria']][2]);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $arrCorteQualidadeImpressao[$v['impressao']][2]);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $arrRefileQualidadePicote[$v['picote']][2]);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $arrRefileQualidadeNovaBobina[$v['novaBobina']][2]);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $larguraEmbalagem);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, $passoEmbalagem);

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoAnaliseVisual[$v['analiseVisual']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoTonalidade[$v['tonalidade']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoConferenciaArte[$v['conferenciaArte']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoLadoTratamento[$v['ladoTratamento']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoLeituraCodigoBarras[$v['leituraCodigoBarras']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoTesteAderenciaTinta[$v['testeAderenciaTinta']][2]));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($arrImpressaoSentidoDesbobinamento[$v['sentidoDesbobinamento']][2]));

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($v['operador']));

        $detalhe = '';
        if ($v['tipo'] == 1) {

            if ($v['semVistoria'] == '1') {
                $detalhe = 'Pr�-setup sem vistoria do analista';
            } else {
                $detalhe = $v['detalhe'];
            }
        } else if ($v['tipo'] == 2) {

            if ($v['semVistoria'] == '1') {
                $detalhe = 'Setup sem vistoria do analista';
            } else {
                $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $detalhe = substr($obs, 0, -2);
            }
        } else if ($v['tipo'] == 3) {
            if ($v['semAnalise'] == '1') {
                $detalhe = 'Bobina n�o analisada';
            }
            $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $detalhe = ($detalhe != '' ? $detalhe . '. ' . substr($obs, 0, -2) : substr($obs, 0, -2));

        } else if ($v['tipo'] == 4) {

            if ($v['semVistoria'] == '1') {
                $detalhe = 'Libera��o sem vistoria do analista';
            } else {
                $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $detalhe = substr($obs, 0, -2);
            }
        } else if ($v['tipo'] == 5) {
            if ($v['semAnalise'] == '1') {
                $detalhe = 'Bobina n�o analisada';
            }
            $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $detalhe = ($detalhe != '' ? $detalhe . '. ' . substr($obs, 0, -2) : substr($obs, 0, -2));

        } else if ($v['tipo'] == 6) {
            if ($v['semVistoria'] == '1') {
                $detalhe = 'Libera��o sem vistoria do analista';
            } else {
                $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $detalhe = substr($obs, 0, -2);
            }
        } else if ($v['tipo'] == 7) {
            if ($v['semAnalise'] == '1') {
                $detalhe = 'Bobina n�o analisada';
            }
            $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $detalhe = ($detalhe != '' ? $detalhe . '. ' . substr($obs, 0, -2) : substr($obs, 0, -2));

        } else if ($v['tipo'] == 8) {
            if ($v['semVistoria'] == '1') {
                $detalhe = 'Libera��o sem vistoria do analista';
            } else {
                $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
                $detalhe = substr($obs, 0, -2);
            }
        } else if ($v['tipo'] == 9) {
            if ($v['semAnalise'] == '1') {
                $detalhe = 'Bobina n�o analisada';
            }
            $obs = ($v['observacoes'] != '' || $v['obs'] != '' ? 'Obs: ' : '') . $v['observacoes'] . ($v['observacoes'] != '' ? ', ' . $v['obs'] . ', ' : $v['obs'] . ', ');
            $detalhe = ($detalhe != '' ? $detalhe . '. ' . substr($obs, 0, -2) : substr($obs, 0, -2));
        }


        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($detalhe));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($v['usuario']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode($v['data']));
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue($coluna++ . $linha, utf8_encode(substr($v['hora'], 0, 5)));

        $linha++;
    }

    $coluna = 'A';
    foreach ($cabecalho as $value) {
        $objPHPExcel->getActiveSheet()->getStyle($coluna . '1')->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension($coluna)->setAutoSize(true);

        $coluna++;
    }

    $lastrow = $objPHPExcel->getActiveSheet()->getHighestRow();

    $objPHPExcel->getActiveSheet()->getStyle('E1:E' . $lastrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
    //$objPHPExcel->getActiveSheet()->getStyle('F1:L' . $lastrow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
    $objPHPExcel->getActiveSheet()->getStyle('A1:AK1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
    header('Content-Disposition: attachment;filename="registros-qualidade.xls"');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

    ob_end_flush();

    $objWriter->save('php://output');
    $objPHPExcel->disconnectWorksheets();

    exit();
} else {
    print_r('N�o h� registros para exporta��o');
    exit();
}