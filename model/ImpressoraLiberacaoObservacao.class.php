<?php

class ImpressoraLiberacaoObservacao {

    public $idimpressoraLiberacaoObservacao;

    const tabela = 'impressoraLiberacaoObservacao';

    public function __construct($idimpressoraLiberacaoObservacao = NULL) {
        $this->idimpressoraLiberacaoObservacao = $idimpressoraLiberacaoObservacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idimpressoraLiberacaoObservacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idimpressoraLiberacaoObservacao = ' . $this->idimpressoraLiberacaoObservacao, 'idimpressoraLiberacaoObservacao');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idimpressoraLiberacaoObservacao = ' . $this->idimpressoraLiberacaoObservacao);
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