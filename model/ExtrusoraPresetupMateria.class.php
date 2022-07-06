<?php

class ExtrusoraPresetupMateria {

    public $idextrusoraPresetupMateria;

    const tabela = 'extrusoraPresetupMateria';

    public function __construct($idextrusoraPresetupMateria = NULL) {
        $this->idextrusoraPresetupMateria = $idextrusoraPresetupMateria;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idextrusoraPresetupMateria']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idextrusoraPresetupMateria = ' . $this->idextrusoraPresetupMateria, 'idextrusoraPresetupMateria');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idextrusoraPresetupMateria = ' . $this->idextrusoraPresetupMateria);
    }

    public function removerPor($where) {
        global $sql;
        return $sql->remover(true, self::tabela, $where);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela, '*', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarCompleto($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . ' m INNER JOIN extrusoraPresetup s ON m.idextrusoraPresetup = s.idextrusoraPresetup INNER JOIN materiaPrima p ON p.idmateriaPrima = m.idmateriaPrima', 'm.*, s.idordemProducao, p.materiaPrima', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>