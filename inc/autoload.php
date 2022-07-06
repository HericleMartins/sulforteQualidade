<?php

if ($_SESSION[SESSAO_SISTEMA]['status'] == 2 && $naoRedireciona !== true) {
    header('Location: editarUsuario.php?idusuario=' . $_SESSION[SESSAO_SISTEMA]['idusuario']);
}

if (!isset($_SESSION[SESSAO_SISTEMA]['idusuario'])) {
    header('Location: login.php?pagina='.  base64_encode(end(explode('/',$_SERVER['REQUEST_URI']))));
    session_destroy();
    die();
} else {
    $objPermissao = new Permissao();
    $objPermissao->verificarPermissao();
}
?>
