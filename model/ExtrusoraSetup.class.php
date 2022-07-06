<?php

class ExtrusoraSetup {

    public $idextrusoraSetup;

    const tabela = 'extrusoraSetup';

    public function __construct($idextrusoraSetup = NULL) {
        $this->idextrusoraSetup = $idextrusoraSetup;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idextrusoraSetup']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela . ' s LEFT JOIN operador o ON o.idoperador = s.idoperador LEFT JOIN usuario u ON u.idusuario = s.idusuario', 's.*, o.operador, u.usuario as analista, CONVERT(VARCHAR(10), s.dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), s.dataCriacao, 114) as dataCriacaoHora', 's.idextrusoraSetup = ' . $this->idextrusoraSetup, 's.idextrusoraSetup');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idextrusoraSetup = ' . $this->idextrusoraSetup);
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