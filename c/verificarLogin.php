<?php
include( "../inc/TemplatePower/class.TemplatePower.inc.php");
include( "../inc/sqlserver.php");
include( "../inc/funcoes.php");
include( "../inc/constantes.php");
include( "../inc/log.php");

// Pegar login e senha(cript)
$email = isset($_POST['email']) ? getRequest(trim(addslashes(str_replace(' ', '', $_POST['email'])))) : FALSE;
$senha = isset($_POST['senha']) ? trim(addslashes(str_replace(' ', '', $_POST['senha']))) : FALSE;
$pagina = getRequest($_POST['pagina']);

$erro = false;
// Se deixar em branco volta
if (!$email or !$senha) {
    $erro = true;
    $msg = 'Preencha todos os campos.';
} else {

    // consulta o bd e traz o numero de linhas
    $objUsuario = new Usuario();
    $usuario = $objUsuario->verificarEmailUsuario($email);

    if ($usuario) {
        if (!strcmp($usuario['senha'], sha1($senha))) {
            if ($usuario['status'] == 1) {
                $erro = true;
                $msg = 'Usuário bloqueado, entre em contato com o administrador';
            } else {
                $_SESSION[SESSAO_SISTEMA]['idusuario'] = $usuario['idusuario'];
                $_SESSION[SESSAO_SISTEMA]['usuario'] = $usuario['usuario'];
                $_SESSION[SESSAO_SISTEMA]['email'] = $usuario['email'];
                $_SESSION[SESSAO_SISTEMA]['idgrupo'] = $usuario['idgrupo'];
                $_SESSION[SESSAO_SISTEMA]['status'] = $usuario['status'];

                if ($pagina) {
                    $pagina = base64_decode($pagina);
                } else {
                    $pagina = 'plantaFabrica.php';
                }

                $log->cadastrarLogControle();
            }
        } else {
            $erro = true;
            $msg = 'Senha inválida';
        }
    } else {
        $erro = true;
        $msg = 'Usuário inválido';
    }
}
?>
<script language='JavaScript' type='text/javascript'>

<?php if ($erro) { ?>
        location.href='login.php?erro=<?php echo $msg ?>';
<?php } else { ?>
        location.href='<?php echo $pagina ?>';
<?php } ?>
</script>
