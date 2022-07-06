<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarObservacao.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar observação");

$post = getRequest($_POST);

montarSelect(TipoMaquina::listar('', 'idtipoMAquina'), 'ListaMaquina', false,true);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-observacaoBuscar'] = $post['observacaoBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['observacaoBuscar'] != ''){
    $where .= "observacao like '%" . $post['observacaoBuscar'] . "%' AND ";
    $objTpl->assign("observacaoBuscar", $post['observacaoBuscar']);
}

//paginação-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaObservacao'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

Observacao::listar($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaObservacao'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaObservacao'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaObservacao']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginação-----------------------------------------------------------------------------------------------------------------

$objObservacao = new Observacao();
$arrayObservacao = $objObservacao->listar($where);

if($arrayObservacao){
    foreach($arrayObservacao as $value){
        $objTpl->newBlock("observacoes");
        $objTpl->assign("observacao", $value['observacao']);
        $objTpl->assign("tipoMaquina", $value['tipoMaquina']);
        $objTpl->assign("idtipoMaquina", $value['idtipoMaquina']);
        $objTpl->assign("idobservacao", $value['idobservacao']);
        if($value['situacao'] == '1'){
            $objTpl->assign("situacao", 'danger');
        } else {
            $objTpl->assign("situacao", 'success');
        }
    }
}

$objTpl->printToScreen(true);