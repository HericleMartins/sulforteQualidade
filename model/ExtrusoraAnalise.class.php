<?php

class ExtrusoraAnalise {

    public $idextrusoraAnalise;

    const tabela = 'extrusoraAnalise';

    public function __construct($idextrusoraAnalise = NULL) {
        $this->idextrusoraAnalise = $idextrusoraAnalise;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idextrusoraAnalise']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela . ' a LEFT JOIN operador o ON o.idoperador = a.idoperador LEFT JOIN ordemProducaoBobina b ON b.idordemProducaoBobina = a.idordemProducaoBobina LEFT JOIN usuario u ON u.idusuario = a.idusuario', 'a.*, o.operador, b.numero, u.usuario as analista, CONVERT(VARCHAR(10), a.dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), a.dataCriacao, 114) as dataCriacaoHora', 'a.idextrusoraAnalise = ' . $this->idextrusoraAnalise, 'a.idextrusoraAnalise');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idextrusoraAnalise = ' . $this->idextrusoraAnalise);
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