<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarPalavraChaveAlertaRefile.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar palavra chave alerta Refile");

$post = getRequest($_POST);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-refilePalavraChaveAlerta'] = $post['refilePalavraChaveAlertaBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['refilePalavraChaveAlertaBuscar'] != ''){
    $where .= "(refilePalavraChaveAlerta like '%" . $post['refilePalavraChaveAlertaBuscar'] . "%' OR mensagemx like '%" . $post['refilePalavraChaveAlertaBuscar'] . "%') AND ";
    $objTpl->assign("refilePalavraChaveAlertaBuscar", $post['refilePalavraChaveAlertaBuscar']);
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

RefilePalavraChaveAlerta::listar($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaRefilePalavraChaveAlerta']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$objRefilePalavraChaveAlerta = new RefilePalavraChaveAlerta();
$arrayRefilePalavraChaveAlerta = $objRefilePalavraChaveAlerta->listar($where);

if($arrayRefilePalavraChaveAlerta){
    foreach($arrayRefilePalavraChaveAlerta as $value){
        $objTpl->newBlock("palavrasChave");
        $objTpl->assign("refilePalavraChaveAlerta", $value['refilePalavraChaveAlerta']);
        $objTpl->assign("mensagem", $value['mensagem']);
        $objTpl->assign("idRefilePalavraChaveAlerta", $value['idRefilePalavraChaveAlerta']);
        if($value['situacao'] == '1'){
            $objTpl->assign("situacao", 'danger');
        } else {
            $objTpl->assign("situacao", 'success');
        }
    }
}

$objTpl->printToScreen(true);