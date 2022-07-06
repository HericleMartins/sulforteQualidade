<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarUsuario.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar usuário");

$post = getRequest($_POST);

montarSelect(Usuario::listarGrupo(), 'listar-grupo', false,true);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-usuarioBuscar'] = $post['usuarioBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['usuarioBuscar'] != ''){
    $where .= "usuario like '%" . $post['usuarioBuscar'] . "%' AND ";
    $objTpl->assign("usuarioBuscar", $post['usuarioBuscar']);
}

//paginação-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaUsuario'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

Usuario::listarUsuario($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaUsuario'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaUsuario'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaUsuario']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginação-----------------------------------------------------------------------------------------------------------------

$objUsuario = new Usuario();
$arrayUsuario = $objUsuario->listarUsuario($where);
if ($arrayUsuario) {
    foreach ($arrayUsuario as $k => $v) {
        $objTpl->newBlock("listar-usuario");
        $objTpl->assign("idusuario", $v['idusuario']);
        $objTpl->assign("nome", $v['usuario']);
        $objTpl->assign("email", $v['email']);
        $objTpl->assign("grupo", $v['grupo']);
        $objTpl->assign("idgrupo", $v['idgrupo']);
        if($v['status'] == 1){
            $objTpl->assign("status", 'danger');
        } else {
            $objTpl->assign("status", 'success');
        }
    }
}

$objTpl->printToScreen(true);