<?php
include( "../inc/load.php");

$tpl = new TemplatePower("../view/default.html");
$tpl->assignInclude("conteudo", "../view/plantaFabrica.html");
$tpl->prepare();
$tpl->assign("titulo-pagina", "Planta da fábrica");

/* código para montar o submenu de tipo de máquina */
$arrayTipoMaquina = TipoMaquina::listar(null, 'ordem ASC');
$arrayAnalistas = Usuario::listarUsuario('u.status = null');
if ($arrayAnalistas){
    foreach ($arrayAnalistas as $k => $v) {
        $select = $v['idusuario'] == $_SESSION[SESSAO_SISTEMA]['idusuario'] ? 'selected' : '';
        $tpl->newBlock("lista-analistas");
        $tpl->assign("idusuario", $v['idusuario']);
        $tpl->assign("analista", $v['usuario']);        
        $tpl->assign("selected", $select);
    }
}
if ($arrayTipoMaquina) {
    foreach ($arrayTipoMaquina as $k => $v) {
        if (!in_array($v['idtipoMaquina'], $arrayEtapaAtiva)){
            $tpl->newBlock("lista-tipo-maquina");
            $tpl->assign("tipoMaquina", $v['tipoMaquina']);
            $tpl->assign("menuDesativado", 'disabled');
        } else {
            $tpl->newBlock("lista-tipo-maquina");
            $tpl->assign("tipoMaquina", $v['tipoMaquina']);
            $tpl->assign("tipoMaquinaAnchor", str_replace('/', '_', $v['tipoMaquina']));
            $tpl->assign("active", ($k == 0 ? 'active' : ''));
            $tpl->assign("toggle", 'pill');
        }
        $tpl->assign("indice", $arrayIndices[$v['tipoMaquina']]['usuario']);
        $tpl->assign("carregarPendencia", ($v['idtipoMaquina'] == 1 ? true : false));
    }
}

/* código para montar a relação de máquinas por tipo */

$arrayMaquina = Maquina::listar(null, 'm.idtipoMaquina ASC, m.maquina ASC');
if ($arrayMaquina) {
    foreach ($arrayMaquina as $k => $v) {
        if ($v['idtipoMaquina'] != $arrayMaquina[$k - 1]['idtipoMaquina']) {
            $tpl->newBlock("lista-bloco-maquina");
            $tpl->assign("tipoMaquina", $v['tipoMaquina']);
            $tpl->assign("tipoMaquinaAnchor", str_replace('/', '_', $v['tipoMaquina']));
            $tpl->assign("active", ($k == 0 ? 'active' : ''));
        }

        $tpl->newBlock("lista-maquina");
        $tpl->assign("maquina", str_pad($v['maquina'], 2, 0, STR_PAD_LEFT));
        $tpl->assign("idmaquina", $v['idmaquina']);
        $tpl->assign("tipoMaquina", $v['tipoMaquina']);
        $tpl->assign("idtipoMaquina", $v['idtipoMaquina']);
        $tpl->assign("classeMaquina", $v['idtipoMaquina']);
        if ($v['delay'] >= DELAY_MAQUINA || is_null($v['delay'])){
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