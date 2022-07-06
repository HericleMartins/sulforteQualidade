<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarOperador.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar operador");

$post = getRequest($_POST);

montarSelect(TipoMaquina::listar('idtipoMaquina IN (1,2,3,4)', 'idtipoMAquina'), 'ListaMaquina', false,true);

$where = '';

//gravando filtro em sessão--------------------------------------------------------------------------------------------
$_SESSION[SESSAO_SISTEMA]['filtro-operadorBuscar'] = $post['operadorBuscar'];

//tratando filtro-----------------------------------------------------------------------------------------------------------------

if($post['operadorBuscar'] != ''){
    $where .= "operador like '%" . $post['operadorBuscar'] . "%' AND ";
    $objTpl->assign("operadorBuscar", $post['operadorBuscar']);
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$_SESSION[SESSAO_SISTEMA]['paginaOperador'] = ($post['pagina'] != '' ? $post['pagina'] : '1');

Operador::listar($where);
$iTotalPaginas = ceil($sql->numRows() / REGISTRO_POR_PAGINA);

$objTpl->gotoBlock('_ROOT');

if($_SESSION[SESSAO_SISTEMA]['paginaOperador'] == '1'){
    $objTpl->assign("disablePrevious",'disabled');
} else {
    $objTpl->assign("linkPrevious","paginar($('#paginacao .active a').html(), '-')");
}

if($_SESSION[SESSAO_SISTEMA]['paginaOperador'] == $iTotalPaginas){
    $objTpl->assign("disableNext",'disabled');
} else {
    $objTpl->assign("linkNext","paginar($('#paginacao .active a').html(), '+')");
}

if ($_SESSION[SESSAO_SISTEMA]['paginaOperador'] > $iTotalPaginas) {
    $_SESSION[SESSAO_SISTEMA]['paginaOperador'] = 1;
}

if($iTotalPaginas > 1) {
    $paginaInicial = (($_SESSION[SESSAO_SISTEMA]['paginaOperador'] - 5) < 1 ? 1 : ($_SESSION[SESSAO_SISTEMA]['paginaOperador'] - 5));
    $paginaFinal = (($_SESSION[SESSAO_SISTEMA]['paginaOperador'] + 5) > $iTotalPaginas ? $iTotalPaginas : ($_SESSION[SESSAO_SISTEMA]['paginaOperador'] + 5));
    for ($i = $paginaInicial; $i <= $paginaFinal; $i++) {

        $objTpl->newBlock("Paginacao");
        $objTpl->assign("numero", $i);
        if($i == $_SESSION[SESSAO_SISTEMA]['paginaOperador']){
            $objTpl->assign("active", 'active');
        }
    }
} else {
    $objTpl->newBlock("Paginacao");
    $objTpl->assign("numero", '1');
    $objTpl->assign("active", 'active');
}

//paginaçãoo-----------------------------------------------------------------------------------------------------------------

$objOperador = new Operador();
$order = 'situacao,tipoMaquina';
$arrayOperador = $objOperador->listar($where,$order);

if($arrayOperador){
    foreach($arrayOperador as $value){
        $objTpl->newBlock("operadores");
        $objTpl->assign("operador", $value['operador']);
        $objTpl->assign("tipoMaquina", $value['tipoMaquina']);
        $objTpl->assign("idtipoMaquina", $value['idtipoMaquina']);
        $objTpl->assign("idoperador", $value['idoperador']);
        $objTpl->assign("codigo", ($value['codigo'] != 0 ? $value['codigo'] : '-'));
        //$value['idtipoMaquina'] == 1
        if($value['naoMostrar'] == 1 || $value['idtipoMaquina'] == 100){
            $objTpl->assign("disabled", 'disabled');
        }
        if($value['situacao'] == '1'){
            $objTpl->assign("situacao", 'danger');
        } else {
            $objTpl->assign("situacao", 'success');
        }
    }
}


$objTpl->printToScreen(true);