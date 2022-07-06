<?php

class RefileAnaliseObservacao {

    public $idrefileAnaliseObservacao;

    const tabela = 'refileAnaliseObservacao';

    public function __construct($idrefileAnaliseObservacao = NULL) {
        $this->idrefileAnaliseObservacao = $idrefileAnaliseObservacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idrefileAnaliseObservacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idrefileAnaliseObservacao = ' . $this->idrefileAnaliseObservacao, 'idrefileAnaliseObservacao');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idrefileAnaliseObservacao = ' . $this->idrefileAnaliseObservacao);
    }

    public function removerPor($where) {
        global $sql;
        return $sql->remover(true, self::tabela, $where);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . ' e INNER JOIN observacao o ON o.idobservacao = e.idobservacao', 'e.*, o.observacao', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>