<?php

class ViewObservacaoBobina {

    const tabela = 'ViewObservacaoBobina';

    public function __construct() {

    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(
            false,
            self::tabela,
            '*, CONVERT(VARCHAR(10), CAST(dataCriacao AS DATE), 103) AS data, CAST(dataCriacao AS TIME) AS hora',
            $where,
            $ordem,
            $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>

