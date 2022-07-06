<?php

class OrdemProducaoMateria {

    public $idordemProducaoMateria;

    const tabela = 'ordemProducaoMateria';

    public function __construct($idordemProducaoMateria = NULL) {
        $this->idordemProducaoMateria = $idordemProducaoMateria;
    }

    public function cadastrar(array $dados) {
        global $sql;
        $status = $sql->cadastrar(true, self::tabela, $dados);
        return ($status ? $sql->lastID : false);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idordemProducaoMateria']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idordemProducaoMateria = ' . $this->idordemProducaoMateria, 'idmateriaPrima');
        return $sql->arrayResult();
    }

    public function carregarPor($where) {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', $where, 'idmateriaPrima');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idordemProducaoMateria = ' . $this->idordemProducaoMateria);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . ' o INNER JOIN materiaPrima m ON m.idmateriaPrima = o.idmateriaPrima', 'o.*, m.materiaPrima', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
}
?>

