<?php

/**
 * Description of Estado
 *
 * @author rmk90
 */

/**
 * Como funciona permissões:
 * Banco de dados contém tabelas
 * pagina -> link da pagina, ex: cadastrarUsuario.php
 * grupo -> nivel do usuario, ex: administrador, gerente..
 * paginaGrupo -> permissao que o usuario tem, ex: idpagina 1(cadastrarUsuario) idgrupo 1(administrador)
 * -> significa que o usuário tem permissão de acessar a pagina cadastrarUsuario
 *
 * na tabela paginaGrupo é onde fica as permissões dos usuarios
 */
class Permissao {

    public $idgrupo;
    public $idpagina;
    public $titulo;

    const tabela = 'paginaGrupo';

    public function verificarPermissao() {
        global $sql;

        $array = explode('/', $_SERVER['PHP_SELF']);

        $sql->selecionar(false, self::tabela . ' pg INNER JOIN pagina p ON p.idpagina=pg.idpagina', 'pg.idgrupo, p.*', 'idgrupo = ' . $_SESSION[SESSAO_SISTEMA]['idgrupo'] . ' AND link = "' . end($array) . '"', 'p.idpagina');

        if ($sql->numRows() > 0) {
            $arrayPagina = $sql->arrayResult();
            $obj = new Permissao();
            $this->titulo = $arrayPagina['titulo'];
        } else {
            echo 'Seu usuário não tem permissão para acessar está página.';
            die();
            header('Location: ../controller/login.php?erro=Seu usuário não tem permissão para acessar está página.');
            session_destroy();
            die();
        }
    }

    public static function verificarAcao($acao) {
        global $sql;

        if (defined($acao)) {
            echo 'Constante não existe declarada no sistema';
            die();
        } else {
            $arrayGrupos = explode(',', trim($acao));

            if(in_array($_SESSION[SESSAO_SISTEMA]['idgrupo'], $arrayGrupos)){
                return true;
            }else{
                return false;
            }
        }
    }

    public function cadastrarPaginaGrupo(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function cadastrarPagina(array $dados) {
        global $sql;
        return $sql->cadastrar(true, 'pagina', $dados);
    }

    public function verificarPermissaoEditar(array $dados){
        if ($dados['semVistoria'] == '1' || ($_SESSION[SESSAO_SISTEMA]['idgrupo'] != USUARIO_ADMINISTRADOR && $_SESSION[SESSAO_SISTEMA]['idusuario'] != $dados['idusuario'])){
            return 'disabled';
        }
    }

    public function verificarPermissaoRemover(array $dados){
        if ($_SESSION[SESSAO_SISTEMA]['idgrupo'] != USUARIO_ADMINISTRADOR && $_SESSION[SESSAO_SISTEMA]['idusuario'] != $dados['idusuario']){
            return 'disabled';
        }
    }

}
?>

