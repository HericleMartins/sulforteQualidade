<?php

class CodigoFaca {

    public $idcodigoFaca;

    const tabela = 'codigoFaca';

    public function __construct($idcodigoFaca = NULL) {
        $this->idcodigoFaca = $idcodigoFaca;
    }

    public function cadastrar(array $dados) {
        global $sql;
        $status = $sql->cadastrar(true, self::tabela, $dados);
        return ($status ? $sql->lastID : false);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idcodigoFaca']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idcodigoFaca = ' . $this->idcodigoFaca, self::tabela);
        return $sql->arrayResult();
    }

    public function carregarPor($where) {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', $where, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idcodigoFaca = ' . $this->idcodigoFaca);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela, '*', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>

