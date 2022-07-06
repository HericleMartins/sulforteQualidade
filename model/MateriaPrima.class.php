<?php

class MateriaPrima {

    public $idmateriaPrima;

    const tabela = 'materiaPrima';

    public function __construct($idmateriaPrima = NULL) {
        $this->idmateriaPrima = $idmateriaPrima;
    }

    public function cadastrar(array $dados) {
        global $sql;
        $status = $sql->cadastrar(true, self::tabela, $dados);
        return ($status ? $sql->lastID : false);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idmateriaPrima']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idmateriaPrima = ' . $this->idmateriaPrima, self::tabela);
        return $sql->arrayResult();
    }

    public function carregarPor($where) {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', $where, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idmateriaPrima = ' . $this->idmateriaPrima);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela, '*', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarSalientando($idordemProducao = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, '(SELECT m.*, (SELECT COUNT(*) FROM ordemProducaoMateria o WHERE o.idmateriaPrima = m.idmateriaPrima AND o.idordemProducao = ' . $idordemProducao . ') as qtd FROM materiaPrima m
          ) z', '*', '', 'qtd DESC, materiaPrima', $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>

