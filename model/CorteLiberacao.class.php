<?php

class CorteLiberacao {

    public $idcorteLiberacao;

    const tabela = 'corteLiberacao';

    public function __construct($idcorteLiberacao = NULL) {
        $this->idcorteLiberacao = $idcorteLiberacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idcorteLiberacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela . ' a LEFT JOIN operador o ON o.idoperador = a.idoperador LEFT JOIN ordemProducaoBobina b ON b.idordemProducaoBobina = a.idordemProducaoBobina LEFT JOIN usuario u ON u.idusuario = a.idusuario LEFT JOIN codigoFaca cf ON cf.idcodigoFaca = a.idcodigoFaca', 'a.*, o.operador, b.numero, u.usuario as analista, CONVERT(VARCHAR(10), a.dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), a.dataCriacao, 114) as dataCriacaoHora, cf.codigoFaca as codigoFacaTexto', 'a.idcorteLiberacao = ' . $this->idcorteLiberacao, 'a.idcorteLiberacao');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idcorteLiberacao = ' . $this->idcorteLiberacao);
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