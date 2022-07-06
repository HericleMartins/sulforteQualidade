<?php
include( "../inc/TemplatePower/class.TemplatePower.inc.php");
include( "../inc/sqlserver.php");
include( "../inc/funcoes.php");
include( "../inc/constantes.php");
include( "../inc/log.php");

$tpl = new TemplatePower("../view/login.html");
$tpl->prepare();

if (isset($_GET['erro'])) {
    $tpl->assign('statusLogin', true);
    $tpl->assign('texto', $_GET['erro']);
}

if (isset($_GET['pagina'])) {
    $tpl->assign('pagina', $_GET['pagina']);
}

$tpl->printToScreen(false);