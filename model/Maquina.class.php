<?php

class Maquina {

    public $idmaquina;

    const tabela = 'maquina';

    public function __construct($idmaquina = NULL) {
        $this->idmaquina = $idmaquina;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idmaquina']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idmaquina = ' . $this->idmaquina, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idmaquina = ' . $this->idmaquina);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . ' m INNER JOIN tipoMaquina t ON t.idtipoMaquina = m.idtipoMaquina LEFT JOIN viewTempoMaquina vtm ON vtm.idmaquina = m.idmaquina', 'm.*, t.tipoMaquina, vtm.delay', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>

