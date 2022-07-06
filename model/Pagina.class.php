<?php

class Pagina {
    const tabela = 'pagina';

    public function listarPaginaFilho($where = false) {
        global $sql;
        $sql->selecionar(false, self::tabela . ' p INNER JOIN paginaGrupo pg ON p.idpagina=pg.idpagina', 'p.*, pg.idgrupo', ($where ? $where : ''), 'ordem ASC');
        return $sql->arrayResults();
    }

    public static function listar($where = false, $ordem = 'ordem ASC') {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', ($where ? $where : ''), $ordem);
        return $sql->arrayResults();
    }

    public static function listarPaginaGrupo($where) {
        global $sql;
        $sql->selecionar(false, self::tabela . 'Grupo', '*', ($where ? $where : ''), ' idpagina ASC');
        return $sql->arrayResults();
    }

    public function deletarPaginaGrupo($where) {
        global $sql;
        return $sql->remover(true, self::tabela . 'Grupo', $where);
    }

    public function cadastrarPaginaGrupo(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela. 'Grupo', $dados);
    }

}

?>
