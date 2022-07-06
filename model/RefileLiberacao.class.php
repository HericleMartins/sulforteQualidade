<?php

class RefileLiberacao {

    public $idrefileLiberacao;

    const tabela = 'refileLiberacao';

    public function __construct($idrefileLiberacao = NULL) {
        $this->idrefileLiberacao = $idrefileLiberacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idrefileLiberacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela . ' a LEFT JOIN operador o ON o.idoperador = a.idoperador LEFT JOIN ordemProducaoBobina b ON b.idordemProducaoBobina = a.idordemProducaoBobina LEFT JOIN usuario u ON u.idusuario = a.idusuario', 'a.*, o.operador, b.numero, u.usuario as analista, CONVERT(VARCHAR(10), a.dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), a.dataCriacao, 114) as dataCriacaoHora', 'a.idrefileLiberacao = ' . $this->idrefileLiberacao, 'a.idrefileLiberacao');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idrefileLiberacao = ' . $this->idrefileLiberacao);
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