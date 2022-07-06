<?php
include("../inc/load.php");

$request = getRequest($_REQUEST);

$tpl = new TemplatePower("../view/default.html");
$tpl->assignInclude("conteudo", "../view/areaTrabalho.html");
$tpl->assignInclude("cadastrar-OP", "../view/CadastrarOrdemProducao.html");
$tpl->assignInclude("cadastrarExtrusoraPresetup", "../view/cadastrarExtrusoraPresetup.html");
$tpl->assignInclude("cadastrarExtrusoraSetup", "../view/cadastrarExtrusoraSetup.html");
$tpl->assignInclude("cadastrarExtrusoraAnalise", "../view/cadastrarExtrusoraAnalise.html");
$tpl->assignInclude("cadastrarCorteSetup", "../view/cadastrarCorteSetup.html");
$tpl->assignInclude("cadastrarCorteAnalise", "../view/cadastrarCorteAnalise.html");
$tpl->assignInclude("cadastrarRefileLiberacao", "../view/cadastrarRefileLiberacao.html");
$tpl->assignInclude("cadastrarRefileAnalise", "../view/cadastrarRefileAnalise.html");
$tpl->assignInclude("cadastrarImpressoraLiberacao", "../view/cadastrarImpressoraLiberacao.html");
$tpl->assignInclude("cadastrarImpressoraAnalise", "../view/cadastrarImpressoraAnalise.html");

if ($request['maquina'] == 1) {
    $tpl->assignInclude("listar-ordem-producao", "../view/listarOrdemProducaoExtrusora.html");
} else if ($request['maquina'] == 2) {
    $tpl->assignInclude("listar-ordem-producao", "../view/listarOrdemProducaoCorte.html");
} else if ($request['maquina'] == 3) {
    $tpl->assignInclude("listar-ordem-producao", "../view/listarOrdemProducaoRefile.html");
} else if ($request['maquina'] == 4) {
    $tpl->assignInclude("listar-ordem-producao", "../view/listarOrdemProducaoImpressora.html");
} else {
    $tpl->newBlock("lista-op-nenhum");
}

$tpl->prepare();

#$tpl->assign("titulo-pagina", "área de trabalho");
$tpl->assign("idmaquina", $request['idmaquina']);
$tpl->assign("idtipoMaquina", $request['maquina']);

$arrayArea = AreaTrabalho::listarView('idmaquina = ' . $request['idmaquina'], 'numero');

/* Valida informação do request */
if ($arrayArea) {
    /* Popula informaçães gerais da máquina */
    $tpl->assign("tipoMaquina", $arrayArea[0]['tipoMaquina']);
    $tpl->assign("maquina", str_pad($arrayArea[0]['maquina'], 2, 0, STR_PAD_LEFT));
    $tpl->assign("classe-maquina", $arrayArea[0]['idtipoMaquina']);

    /* Verifica se existe alguma OP na área de trabalho */
    if ($arrayArea[0]['numero'] && $arrayArea[0]['cliente']) {
        foreach ($arrayArea as $k => $v) {
            $tpl->newBlock("lista-op");

            if ($request['maquina'] == 1) {
                $rPalavaChave = verificarPalavraChaveAlertaExtrusao($v['item']);
                if ($rPalavaChave['status']) {
                    $tpl->assign("cabecalhoAvisoOp", "cabecalhoAvisoOp");
                    $tpl->newBlock("lista-op-aviso-msg");
                    $tpl->assign("cabecalhoAvisoOpTexto", ($rPalavaChave['mensagemPalavraChave'] ? $rPalavaChave['mensagemPalavraChave'] : TEXTO_ALERTA_AVISO_EXTRUSAO));
                    $tpl->gotoBlock("lista-op");
                }

            } else if ($request['maquina'] == 2){
                $rPalavaChave = verificarPalavraChaveAlertaCS($v['item']);
                if ($rPalavaChave['status']) {
                    $tpl->assign("cabecalhoAvisoOp", "cabecalhoAvisoOp");
                    $tpl->newBlock("lista-op-aviso-msg");
                    $tpl->assign("cabecalhoAvisoOpTexto", ($rPalavaChave['mensagemPalavraChave'] ? $rPalavaChave['mensagemPalavraChave'] : TEXTO_ALERTA_AVISO_CS));
                    $tpl->gotoBlock("lista-op");
                }
            }else if ($request['maquina'] == 3){
                $rPalavaChave = verificarPalavraChaveAlertaRefile($v['item']);
                if ($rPalavaChave['status']) {
                    $tpl->assign("cabecalhoAvisoOp", "cabecalhoAvisoOp");
                    $tpl->newBlock("lista-op-aviso-msg");
                    $tpl->assign("cabecalhoAvisoOpTexto", ($rPalavaChave['mensagemPalavraChave'] ? $rPalavaChave['mensagemPalavraChave'] : TEXTO_ALERTA_AVISO_REFILE));
                    $tpl->gotoBlock("lista-op");
                }
            }else if ($request['maquina'] == 4){
                $rPalavaChave = verificarPalavraChaveAlertaImpressao($v['item']);
                if ($rPalavaChave['status']) {
                    $tpl->assign("cabecalhoAvisoOp", "cabecalhoAvisoOp");
                    $tpl->newBlock("lista-op-aviso-msg");
                    $tpl->assign("cabecalhoAvisoOpTexto", ($rPalavaChave['mensagemPalavraChave'] ? $rPalavaChave['mensagemPalavraChave'] : TEXTO_ALERTA_AVISO_IMPRESSAO));
                    $tpl->gotoBlock("lista-op");
                }
            }
            
            $tpl->assign("numero-op", $v['numero']);
            $tpl->assign("nome-cliente", truncateString($v['cliente'], 21));
            $tpl->assign("idareaTrabalho", $v['idareaTrabalho']);
            $tpl->assign("idordemProducao", $v['idordemProducao']);
            $tpl->assign("idmaquina", $v['idmaquina']);
            $tpl->assign("maquina", $request['maquina']);
            $tpl->assign("quantPresetup", ($v['quantPresetup'] ? "<span class='badge badge-quantidade'>" . $v['quantPresetup'] . "</span>" : ""));
            $tpl->assign("quantSetup", ($v['quantSetup'] ? "<span class='badge badge-quantidade'>" . $v['quantSetup'] . "</span>" : ""));
            $tpl->assign("quantAnalise", ($v['quantAnalise'] ? "<span class='badge badge-quantidade'>" . $v['quantAnalise'] . "</span>" : ""));
            $tpl->assign("quantCLiberacao", ($v['quantCLiberacao'] ? "<span class='badge badge-quantidade'>" . $v['quantCLiberacao'] . "</span>" : ""));
            $tpl->assign("quantCAnalise", ($v['quantCAnalise'] ? "<span class='badge badge-quantidade'>" . $v['quantCAnalise'] . "</span>" : ""));
            $tpl->assign("quantRLiberacao", ($v['quantRLiberacao'] ? "<span class='badge badge-quantidade'>" . $v['quantRLiberacao'] . "</span>" : ""));
            $tpl->assign("quantRAnalise", ($v['quantRAnalise'] ? "<span class='badge badge-quantidade'>" . $v['quantRAnalise'] . "</span>" : ""));
            $tpl->assign("quantILiberacao", ($v['quantILiberacao'] ? "<span class='badge badge-quantidade'>" . $v['quantILiberacao'] . "</span>" : ""));
            $tpl->assign("quantIAnalise", ($v['quantIAnalise'] ? "<span class='badge badge-quantidade'>" . $v['quantIAnalise'] . "</span>" : ""));

            if ($request['maquina'] == 1) {
                //só mostra na extrusora
                if ($v['quantBobinaPesada'] == 1) {
                    $tpl->assign("quantidadeBobinasPesadas", $v['quantBobinaPesada'] . ' bobina pesada');
                } else if ($v['quantBobinaPesada'] > 0) {
                    $tpl->assign("quantidadeBobinasPesadas", $v['quantBobinaPesada'] . ' bobinas pesadas');
                } else {
                    $tpl->assign("quantidadeBobinasPesadas", 'Nenhuma bobina pesada');
                }
            }
        }
    } else {
        $tpl->newBlock("lista-op-nenhum");
    }
} else {
    header('location: plantaFabrica.php');
}

/* código para montar a relação de máquinas por tipo - para modal de atalho */
$arrayMaquina = Maquina::listar(null, 't.ordem ASC, m.maquina ASC');
if ($arrayMaquina) {
    foreach ($arrayMaquina as $k => $v) {
        if (in_array($v['idtipoMaquina'], $arrayEtapaAtiva)) {
            if ($v['idtipoMaquina'] != $arrayMaquina[$k - 1]['idtipoMaquina']) {
                $tpl->newBlock("listar-maquina-atalho");
                $tpl->assign("maquina-atalho", $v['tipoMaquina']);
            }
            $tpl->newBlock("listar-maquina-atalho-nome");
            $tpl->assign("maquina-atalho", str_pad($v['maquina'], 2, 0, STR_PAD_LEFT));
            $tpl->assign("idmaquina-atalho", $v['idmaquina']);
            $tpl->assign("tipoMaquina-atalho", $v['tipoMaquina']);
            $tpl->assign("idtipoMaquina-atalho", $v['idtipoMaquina']);
            $tpl->assign("classe-maquina-atalho", $v['idtipoMaquina']);
        }
        if ($v['delay'] >= DELAY_MAQUINA || is_null($v['delay'])) {
            $tpl->assign("iconeDelay", "fa fa-exclamation-triangle");
            $tpl->assign("classeDelay", "maquina-delay");
            $tpl->assign("mensagemDelay", MENSAGEM_MAQUINA_OCIOSA);
        } else {
            $tpl->assign("iconeDelay", "");
            $tpl->assign("classeDelay", "");
            $tpl->assign("mensagemDelay", "");
        }
    }
}

$tpl->printToScreen(true);
