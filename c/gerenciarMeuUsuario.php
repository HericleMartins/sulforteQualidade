<?php
include( "../inc/load.php");

$objTpl = new TemplatePower("../view/default.html");
$objTpl->assignInclude("conteudo", "../view/gerenciarMeuUsuario.html");
$objTpl->prepare();
$objTpl->assign("titulo-pagina", "Gerenciar meu usuário");

$r = getRequest($_REQUEST);

$objUsuario = new Usuario($_SESSION[SESSAO_SISTEMA]['idusuario']);
$arrayUsuario = $objUsuario->carregar();

$objTpl->assign("usuario", $arrayUsuario['usuario']);
$objTpl->assign("email", $arrayUsuario['email']);

$objTpl->printToScreen();