<?php

class Observacao {

    public $idobservacao;

    const tabela = 'observacao';

    public function __construct($idobservacao = NULL) {
        $this->idobservacao = $idobservacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idobservacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idobservacao = ' . $this->idobservacao, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idobservacao = ' . $this->idobservacao);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . ' o INNER JOIN tipoMaquina t ON t.idtipoMaquina = o.idtipoMaquina', 'o.*, t.tipoMaquina', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>