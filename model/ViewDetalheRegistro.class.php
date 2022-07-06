<?php

class ViewDetalheRegistro {

    const tabela = 'viewDetalheRegistro';

    public function __construct() {

    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela, '*, CONVERT(VARCHAR(10), CAST(dataCriacao AS DATE), 103) AS data, CAST(dataCriacao AS TIME) AS hora', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
/*
    public static function listarLimite($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . 'Limite', '*, CONVERT(VARCHAR(10), CAST(dataCriacao AS DATE), 103) AS data, CAST(dataCriacao AS TIME) AS hora', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
*/
    public static function listarPaginacao($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->ExecuteSQL("SELECT * FROM " . self::tabela . " WHERE " . $where . " 1=1", false);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
}
?>

