<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarPalavraChaveAlertaImpressao.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar palavra chave alerta Impressao");

$post = getRequest($_POST);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-impressaoPalavraChaveAlerta'] = $post['impressaoPalavraChaveAlertaBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['impressaoPalavraChaveAlertaBuscar'] != ''){
    $where .= "(impressaoPalavraChaveAlerta like '%" . $post['impressaoPalavraChaveAlertaBuscar'] . "%' OR mensagemx like '%" . $post['impressaoPalavraChaveAlertaBuscar'] . "%') AND ";
    $objTpl->assign("impressaoPalavraChaveAlertaBuscar", $post['impressaoPalavraChaveAlertaBuscar']);
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

ImpressaoPalavraChaveAlerta::listar($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaImpressaoPalavraChaveAlerta']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$objImpressaoPalavraChaveAlerta = new ImpressaoPalavraChaveAlerta();
$arrayImpressaoPalavraChaveAlerta = $objImpressaoPalavraChaveAlerta->listar($where);

if($arrayImpressaoPalavraChaveAlerta){
    foreach($arrayImpressaoPalavraChaveAlerta as $value){
        $objTpl->newBlock("palavrasChave");
        $objTpl->assign("impressaoPalavraChaveAlerta", $value['impressaoPalavraChaveAlerta']);
        $objTpl->assign("mensagem", $value['mensagem']);
        $objTpl->assign("idImpressaoPalavraChaveAlerta", $value['idImpressaoPalavraChaveAlerta']);
        if($value['situacao'] == '1'){
            $objTpl->assign("situacao", 'danger');
        } else {
            $objTpl->assign("situacao", 'success');
        }
    }
}

$objTpl->printToScreen(true);