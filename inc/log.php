<?php

/**
 * Classe responsavel pela inserção dos logs 
 */
class Log extends SqlServer {

    public function cadastrarLogAcesso() {
        $pagina = explode('/', $_SERVER['PHP_SELF']);
        $this->cadastrar(false, 'logAcesso', array('pagina' => end($pagina), 'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'], 'dataCadastro' => getData()));
    }

    public function cadastrarLogAcao($query) {
        if (strstr($query, 'INSERT')) {
            $tipoAcao = 1;
        } else if (strstr($query, 'SELECT')) {
            $tipoAcao = 2;
        } else if (strstr($query, 'DELETE')) {
            $tipoAcao = 4;
        } else if (strstr($query, 'UPDATE')) {
            $tipoAcao = 3;
        } else if (strstr($query, 'BEGIN')) {
            $tipoAcao = 5;
        } else if (strstr($query, 'COMMIT')) {
            $tipoAcao = 6;
        }
        $this->cadastrar(false, 'logAcao', array('query' => $query, 'tipoAcao' => $tipoAcao, 'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'], 'dataCadastro' => getData()));
    }

    public function cadastrarLogControle() {
        $this->cadastrar(false, 'logControle', array('idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'], 'ip' => $_SERVER['REMOTE_ADDR'], 'sistemaOperacional' => $_SERVER['HTTP_USER_AGENT'], 'dataCadastro' => getData()));
    }

}

$log = new Log();
$log->cadastrarLogAcesso();
?>
