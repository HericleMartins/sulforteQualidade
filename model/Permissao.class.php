<?php

/**
 * Description of Estado
 *
 * @author rmk90
 */

/**
 * Como funciona permiss�es:
 * Banco de dados cont�m tabelas
 * pagina -> link da pagina, ex: cadastrarUsuario.php
 * grupo -> nivel do usuario, ex: administrador, gerente..
 * paginaGrupo -> permissao que o usuario tem, ex: idpagina 1(cadastrarUsuario) idgrupo 1(administrador)
 * -> significa que o usu�rio tem permiss�o de acessar a pagina cadastrarUsuario
 *
 * na tabela paginaGrupo � onde fica as permiss�es dos usuarios
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
            echo 'Seu usu�rio n�o tem permiss�o para acessar est� p�gina.';
            die();
            header('Location: ../controller/login.php?erro=Seu usu�rio n�o tem permiss�o para acessar est� p�gina.');
            session_destroy();
            die();
        }
    }

    public static function verificarAcao($acao) {
        global $sql;

        if (defined($acao)) {
            echo 'Constante n�o existe declarada no sistema';
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

