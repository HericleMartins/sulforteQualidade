<?php
include( "../inc/load.php");
$tpl = new TemplatePower("../view/default.html");
$tpl->assignInclude("conteudo", "../view/visualizarOrdemProducao.html");

$request = getRequest($_REQUEST);

if ($request['maquina'] == 1) {

    $tpl->assignInclude("bloco-acompanhamento", "../view/visualizarAcompanhamentoExtrusora.html");
    $tpl->assignInclude("bloco-presetup", "../view/visualizarPresetupExtrusora.html");

    $tpl->assignInclude("cadastrarExtrusoraPresetup", "../view/cadastrarExtrusoraPresetup.html");
    $tpl->assignInclude("cadastrarExtrusoraSetup", "../view/cadastrarExtrusoraSetup.html");
    $tpl->assignInclude("cadastrarExtrusoraAnalise", "../view/cadastrarExtrusoraAnalise.html");

    $tpl->assignInclude("visualizarExtrusoraSetup", "../view/visualizarExtrusoraSetup.html");
    $tpl->assignInclude("visualizarExtrusoraAnalise", "../view/visualizarExtrusoraAnalise.html");
} else if ($request['maquina'] == 2) {

    $tpl->assignInclude("bloco-acompanhamento", "../view/visualizarAcompanhamentoCorte.html");

    $tpl->assignInclude("cadastrarCorteLiberacao", "../view/cadastrarCorteSetup.html");
    $tpl->assignInclude("cadastrarCorteAnalise", "../view/cadastrarCorteAnalise.html");

    $tpl->assignInclude("visualizarCorteLiberacao", "../view/visualizarCorteSetup.html");
    $tpl->assignInclude("visualizarCorteAnalise", "../view/visualizarCorteAnalise.html");
} else if ($request['maquina'] == 3) {

    $tpl->assignInclude("bloco-acompanhamento", "../view/visualizarAcompanhamentoRefile.html");

    $tpl->assignInclude("cadastrarRefileLiberacao", "../view/cadastrarRefileLiberacao.html");
    $tpl->assignInclude("cadastrarRefileAnalise", "../view/cadastrarRefileAnalise.html");

    $tpl->assignInclude("visualizarRefileLiberacao", "../view/visualizarRefileLiberacao.html");
    $tpl->assignInclude("visualizarRefileAnalise", "../view/visualizarRefileAnalise.html");

} else if ($request['maquina'] == 4) {

    $tpl->assignInclude("bloco-acompanhamento", "../view/visualizarAcompanhamentoImpressora.html");

    $tpl->assignInclude("cadastrarImpressoraLiberacao", "../view/cadastrarImpressoraLiberacao.html");
    $tpl->assignInclude("cadastrarImpressoraAnalise", "../view/cadastrarImpressoraAnalise.html");

    $tpl->assignInclude("visualizarImpressoraLiberacao", "../view/visualizarImpressoraLiberacao.html");
    $tpl->assignInclude("visualizarImpressoraAnalise", "../view/visualizarImpressoraAnalise.html");
}


$tpl->prepare();

$tpl->assign("titulo-pagina", "Visualização");

$tpl->assign("tipoMaquina", $arrMaquina[$request['maquina']]);
$tpl->assign("idtipoMaquina", $request['maquina']);
$tpl->assign("idmaquina", $request['idmaquina']);
$tpl->assign("classe-maquina", $request['maquina']);

$objMaquina = new Maquina($request['idmaquina']);
$arrayMaquina = $objMaquina->carregar();

$tpl->assign("maquina", str_pad($arrayMaquina['maquina'], 2, 0, STR_PAD_LEFT));

$objOrdemProducao = new OrdemProducao($request['idordemProducao']);
$arrayOrdemProducao = $objOrdemProducao->carregar();

$tpl->assign("numero", $arrayOrdemProducao['numero']);
$tpl->assign("nome-cliente", truncateString($arrayOrdemProducao['cliente'], 26));

$tpl->assign("item", $arrayOrdemProducao['item']);
$tpl->assign("medidas", $arrayOrdemProducao['medidas']);
$tpl->assign("larguraBalao", number_format($arrayOrdemProducao['larguraBalao'], 2, ',', ''));
$tpl->assign("pesoExtrusao", number_format($arrayOrdemProducao['pesoExtrusao'], 2, ',', ''));

$tpl->assign("termoencolhivel", str_replace('%','', $arrayOrdemProducao['termoencolhivel']));
$tpl->assign("impresso", str_replace('%','', $arrayOrdemProducao['impresso']));
$tpl->assign("soldaLateral", str_replace('%','', $arrayOrdemProducao['soldaLateral']));
$tpl->assign("toleranciaQualidade", str_replace('%','', $arrayOrdemProducao['toleranciaQualidade']));

$tpl->assign("sanfona", (str_replace(' ', '' , $arrayOrdemProducao['sanfona']) != '0,00/0,00' ? $arrayOrdemProducao['sanfona'] : '-'));

$tpl->assign("gofrado", ($arrayOrdemProducao['gofrado'] == 'S' ? 'Sim' : 'Não'));

//carrega as matéria-prima da OP
$objMateriaPrima = new OrdemProducaoMateria();
$arrayMateriaPrima = $objMateriaPrima->listar('o.idordemProducao = ' . $request['idordemProducao'], 'm.codigo');

if($arrayMateriaPrima) {
    foreach ($arrayMateriaPrima as $k => $v) {
        $tpl->newBlock("lista-materia-prima");
        $tpl->assign("materiaPrima", $v['materiaPrima']);
        $tpl->assign("quantidade", number_format($v['quantidade'], 2, ',', ''));
    }
}

//carrega as bobinas já pesadas da OP
$objBobina = new OrdemProducaoBobina();
$arrayBobina = $objBobina->listar('b.idordemProducao = ' . $request['idordemProducao'], 'b.numero');

if($arrayBobina) {
    foreach ($arrayBobina as $k => $v) {
        $tpl->newBlock("lista-bobinas-pesadas");
        $tpl->assign("numero-bobina", $v['numero']);
        $tpl->assign("peso", number_format($v['peso'], 2, ',', ''));
        $tpl->assign("operador", $v['operador']);
        $tpl->assign("data", $v['dataFormatada']);
        $tpl->assign("hora", $v['hora']);
        $tpl->assign("extrusora", $v['maquina']);
    }
}

if ($request['maquina'] == 1) {
    //carrega as bobinas já pesadas da OP
    $objBobina = new OrdemProducaoBobina();
    $arrayBobina = $objBobina->listarGeral('idmaquina = ' . $request['idmaquina'] . ' AND idordemProducao = ' . $request['idordemProducao'], 'dataCriacao DESC');

    if($arrayBobina) {
        foreach ($arrayBobina as $k => $v) {
            $tpl->newBlock("lista-bobina");

            if($v['tipo'] == 1){
                $corBobina = 'info';
            } else {
                $corBobina = 'warning';
            }

            if($v['semVistoria'] != 1) {
                if($v['semAnalise'] == '1') {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . '</td><td colspan="5"> Bobina não analisada</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                } else {
                    if ($v['tipoMedida'] == 1) {
                        $espessura = number_format($v['espessuraMin'], 2, ',', '') . ' - ' . number_format($v['espessuraMax'], 2, ',', '');
                    } else {
                        $espessura = number_format($v['espessuraMedia'], 2, ',', '') . ' <i class="fa fa-info-circle text-info" aria-hidden="true" data-toggle="tooltip" title="Espessura calculada"></i>';
                    }

                    $sanfona = array();

                    if ($v['sanfonaEsq']) {
                        array_push($sanfona, number_format($v['sanfonaEsq'], 2, ',', ''));
                    }
                    if ($v['sanfonaDir']) {
                        array_push($sanfona, number_format($v['sanfonaDir'], 2, ',', ''));
                    }

                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . '</td><td>' . number_format($v['largura'], 2, ',', '') .
                        '</td><td>' . $espessura .
                        '</td><td>' . ($sanfona ? implode(' - ', $sanfona) : '-') .
                        '</td><td>' . ($v['faceamento'] != '' ? $arrExtrusaoFaceamento[$v['faceamento']][2] : '-') .
                        '</td><td>' . ($v['impressao'] != '' ? $arrExtrusaoTratamentoImpressao[$v['impressao']][2] : '-') .
                        '</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                }
            } else {
                $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> </td><td colspan="5"> Setup sem vistoria do analista</td><td>' . $v['usuario'] . '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
            }

            $tpl->assign("linha", $linha);
            $tpl->assign("id", $v['id']);
            $tpl->assign("tipo", $v['tipo']);
            $tpl->assign("idmaquina", $v['idmaquina']);
            $tpl->assign("idordemProducao", $v['idordemProducao']);

            $tpl->assign("permissao-editar", Permissao::verificarPermissaoEditar($v));
            $tpl->assign("permissao-remover", Permissao::verificarPermissaoRemover($v));

        }
    }

    //carrega as presetup
    $objPresetup = new ExtrusoraPresetup();
    $arrayPresetup  = $objPresetup->listar('idmaquina = ' . $request['idmaquina'] . ' AND idordemProducao = ' . $request['idordemProducao'], 'dataCriacao DESC');

    if($arrayPresetup) {
        foreach ($arrayPresetup as $k => $v) {
            $tpl->newBlock("lista-presetup");
            $tpl->assign("data", $v['data']);
            $tpl->assign("hora", substr($v['hora'], 0, 5));
            $tpl->assign("observacao", $v['observacao']);
            $tpl->assign("analista", $v['usuario']);
            $tpl->assign("detalhe", ($v['semVistoria'] == '1' ? 'Pré-setup sem vistoria do analista' : $v['detalhe']));
            $tpl->assign("id", $v['idextrusoraPresetup']);
            $tpl->assign("tipo", 3);
            $tpl->assign("idmaquina", $v['idmaquina']);
            $tpl->assign("idordemProducao", $v['idordemProducao']);

            $tpl->assign("permissao-editar", Permissao::verificarPermissaoEditar($v));
            $tpl->assign("permissao-remover", Permissao::verificarPermissaoRemover($v));

        }
    }
}

if ($request['maquina'] == 2) {

    $objBobina = new OrdemProducaoBobina();
    $arrayBobina = $objBobina->listarGeralCorte('idmaquina = ' . $request['idmaquina'] . ' AND idordemProducao = ' . $request['idordemProducao'], 'dataCriacao DESC');

    if($arrayBobina) {
        foreach ($arrayBobina as $k => $v) {
            $tpl->newBlock("lista-bobina");

            if($v['tipo'] == 4){
                $corBobina = 'info';
            } else {
                $corBobina = 'warning';
            }

            $sanfona = array();

            if ($v['sanfonaEsq']) {
                array_push($sanfona, number_format($v['sanfonaEsq'], 2, ',', ''));
            }
            if ($v['sanfonaDir']) {
                array_push($sanfona, number_format($v['sanfonaDir'], 2, ',', ''));
            }

            if($v['semVistoria'] != 1){
                if($v['semAnalise'] == '1') {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . '</td><td colspan="7"> Bobina não analisada</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                } else {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . ($v['reinspecao'] == 1 ? '<span style="font-size: 7pt; cursor: context-menu" title="Reinspeçõo"> (R)</span>' : '') . '</td><td>' . ($v['pista'] ? $v['pista'] : '-') . '</td><td>' . number_format($v['largura'], 2, ',', '') .
                        '</td><td>' . number_format($v['comprimento'], 2, ',', '') .
                        '</td><td>' . ($sanfona ? implode(' - ', $sanfona) : '-') .
                        '</td><td>' . $v['codigoFaca'] .
                        '</td><td>' . $arrCorteQualidadeSolda[$v['solda']][2] .
                        '</td><td>' . $arrCorteQualidadeSacaria[$v['sacaria']][2] .

                        '</td><td>' . $arrCorteQualidadeImpressao[$v['impressao']][2] .
                        '</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                }
            } else {
                $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] .  '</td><td colspan="7">Liberação sem vistoria do analista</td><td>' . $v['usuario'] . '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora']. '</td>';
            }

            $tpl->assign("linha", $linha);
            $tpl->assign("id", $v['id']);
            $tpl->assign("tipo", $v['tipo']);
            $tpl->assign("idmaquina", $v['idmaquina']);
            $tpl->assign("idordemProducao", $v['idordemProducao']);

            $tpl->assign("permissao-editar", Permissao::verificarPermissaoEditar($v));
            $tpl->assign("permissao-remover", Permissao::verificarPermissaoRemover($v));

        }
    }
}

if ($request['maquina'] == 3) {

    $objBobina = new OrdemProducaoBobina();
    $arrayBobina = $objBobina->listarGeralRefile('idmaquina = ' . $request['idmaquina'] . ' AND idordemProducao = ' . $request['idordemProducao'], 'dataCriacao DESC');

    if($arrayBobina) {
        foreach ($arrayBobina as $k => $v) {
            $tpl->newBlock("lista-bobina");

            if($v['tipo'] == 6){
                $corBobina = 'info';
            } else {
                $corBobina = 'warning';
            }

            if($v['semVistoria'] != 1){
                if($v['semAnalise'] == '1') {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . '</td><td colspan="5"> Bobina não analisada</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                } else {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . ($v['reinspecao'] == 1 ? '<span style="font-size: 7pt; cursor: context-menu" title="Reinspeção"> (R)</span>' : '') . '</td><td>' . number_format($v['largura'], 2, ',', '') .
                        '</td><td>' . ($v['comprimento'] != '' ? number_format($v['comprimento'], 2, ',', '') : '') .
                        '</td><td>' . $arrRefileQualidadePicote[$v['picote']][2] .
                        '</td><td>' . $arrRefileQualidadeNovaBobina[$v['novaBobina']][2] .
                        '</td><td>' . $arrRefileQualidadeImpressao[$v['impressao']][2] .
                        '</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                }
            } else {
                $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] .  '</td><td colspan="5">Liberação sem vistoria do analista</td><td>' . $v['usuario'] . '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora']. '</td>';
            }

            $tpl->assign("linha", $linha);
            $tpl->assign("id", $v['id']);
            $tpl->assign("tipo", $v['tipo']);
            $tpl->assign("idmaquina", $v['idmaquina']);
            $tpl->assign("idordemProducao", $v['idordemProducao']);

            $tpl->assign("permissao-editar", Permissao::verificarPermissaoEditar($v));
            $tpl->assign("permissao-remover", Permissao::verificarPermissaoRemover($v));

        }
    }
}

if ($request['maquina'] == 4) {

    $objBobina = new OrdemProducaoBobina();
    $arrayBobina = $objBobina->listarGeralImpressora('idmaquina = ' . $request['idmaquina'] . ' AND idordemProducao = ' . $request['idordemProducao'], 'dataCriacao DESC');

    if($arrayBobina) {
        foreach ($arrayBobina as $k => $v) {
            $tpl->newBlock("lista-bobina");

            if($v['tipo'] == 8){
                $corBobina = 'info';
            } else {
                $corBobina = 'warning';
            }

            if($v['semVistoria'] != 1){
                if($v['semAnalise'] == '1') {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . '</td><td colspan="9"> Bobina não analisada</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                } else {
                    $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] . ($v['reinspecao'] == 1 ? '<span style="font-size: 7pt; cursor: context-menu" title="Reinspeção"> (R)</span>' : '') .
                        '</td><td>' . ($v['larguraEmbalagem'] != '' ? number_format($v['larguraEmbalagem'], 2, ',', '') : '') .
                        '</td><td>' . ($v['passoEmbalagem'] != '' ? number_format($v['passoEmbalagem'], 2, ',', '') : '') .
                        '</td><td>' . $arrImpressaoAnaliseVisual[$v['analiseVisual']][2] .
                        '</td><td>' . $arrImpressaoTonalidade[$v['tonalidade']][2] .
                        '</td><td>' . $arrImpressaoConferenciaArte[$v['conferenciaArte']][2] .
                        '</td><td>' . $arrImpressaoLadoTratamento[$v['ladoTratamento']][2] .
                        '</td><td>' . $arrImpressaoLeituraCodigoBarras[$v['leituraCodigoBarras']][2] .
                        '</td><td>' . $arrImpressaoTesteAderenciaTinta[$v['testeAderenciaTinta']][2] .
                        '</td><td>' . $arrImpressaoSentidoDesbobinamento[$v['sentidoDesbobinamento']][2] .
                        '</td><td>' . $v['usuario'] .
                        '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora'] . '</td>';
                }
            } else {
                $linha = '<td><i class="fa fa-circle text-' . $corBobina . '" aria-hidden="true"></i> ' . $v['numero'] .  '</td><td colspan="9">Liberação sem vistoria do analista</td><td>' . $v['usuario'] . '</td><td>' . $v['dataCriacaoData'] . ' - ' . $v['dataCriacaoHora']. '</td>';
            }

            $tpl->assign("linha", $linha);
            $tpl->assign("id", $v['id']);
            $tpl->assign("tipo", $v['tipo']);
            $tpl->assign("idmaquina", $v['idmaquina']);
            $tpl->assign("idordemProducao", $v['idordemProducao']);

            $tpl->assign("permissao-editar", Permissao::verificarPermissaoEditar($v));
            $tpl->assign("permissao-remover", Permissao::verificarPermissaoRemover($v));

        }
    }
}

$tpl->printToScreen(true);