<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarPalavraChaveAlertaCS.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar palavra chave alerta CS");

$post = getRequest($_POST);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-cortePalavraChaveAlerta'] = $post['cortePalavraChaveAlertaBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['cortePalavraChaveAlertaBuscar'] != ''){
    $where .= "(cortePalavraChaveAlerta like '%" . $post['cortePalavraChaveAlertaBuscar'] . "%' OR mensagemx like '%" . $post['cortePalavraChaveAlertaBuscar'] . "%') AND ";
    $objTpl->assign("cortePalavraChaveAlertaBuscar", $post['cortePalavraChaveAlertaBuscar']);
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

CortePalavraChaveAlerta::listar($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaCortePalavraChaveAlerta']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$objCortePalavraChaveAlerta = new CortePalavraChaveAlerta();
$arrayCortePalavraChaveAlerta = $objCortePalavraChaveAlerta->listar($where);

if($arrayCortePalavraChaveAlerta){
    foreach($arrayCortePalavraChaveAlerta as $value){
        $objTpl->newBlock("palavrasChave");
        $objTpl->assign("cortePalavraChaveAlerta", $value['cortePalavraChaveAlerta']);
        $objTpl->assign("mensagem", $value['mensagem']);
        $objTpl->assign("idCortePalavraChaveAlerta", $value['idCortePalavraChaveAlerta']);
        if($value['situacao'] == '1'){
            $objTpl->assign("situacao", 'danger');
        } else {
            $objTpl->assign("situacao", 'success');
        }
    }
}

$objTpl->printToScreen(true);