<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarPalavraChaveAlertaExtrusao.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar palavra chave alerta Extrusao");

$post = getRequest($_POST);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-extrusaoPalavraChaveAlerta'] = $post['extrusaoPalavraChaveAlertaBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['extrusaoPalavraChaveAlertaBuscar'] != ''){
    $where .= "(extrusaoPalavraChaveAlerta like '%" . $post['extrusaoPalavraChaveAlertaBuscar'] . "%' OR mensagemx like '%" . $post['extrusaoPalavraChaveAlertaBuscar'] . "%') AND ";
    $objTpl->assign("extrusaoPalavraChaveAlertaBuscar", $post['extrusaoPalavraChaveAlertaBuscar']);
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

ExtrusaoPalavraChaveAlerta::listar($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaExtrusaoPalavraChaveAlerta']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$objExtrusaoPalavraChaveAlerta = new ExtrusaoPalavraChaveAlerta();
$arrayExtrusaoPalavraChaveAlerta = $objExtrusaoPalavraChaveAlerta->listar($where);

if($arrayExtrusaoPalavraChaveAlerta){
    foreach($arrayExtrusaoPalavraChaveAlerta as $value){
        $objTpl->newBlock("palavrasChave");
        $objTpl->assign("extrusaoPalavraChaveAlerta", $value['extrusaoPalavraChaveAlerta']);
        $objTpl->assign("mensagem", $value['mensagem']);
        $objTpl->assign("idExtrusaoPalavraChaveAlerta", $value['idExtrusaoPalavraChaveAlerta']);
        if($value['situacao'] == '1'){
            $objTpl->assign("situacao", 'danger');
        } else {
            $objTpl->assign("situacao", 'success');
        }
    }
}

$objTpl->printToScreen(true);