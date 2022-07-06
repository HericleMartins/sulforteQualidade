<?php

class ExtrusoraSetupObservacao {

    public $idextrusoraSetupObservacao;

    const tabela = 'extrusoraSetupObservacao';

    public function __construct($idextrusoraSetupObservacao = NULL) {
        $this->idextrusoraSetupObservacao = $idextrusoraSetupObservacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idextrusoraSetupObservacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idextrusoraSetupObservacao = ' . $this->idextrusoraSetupObservacao, 'idextrusoraSetupObservacao');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idextrusoraSetupObservacao = ' . $this->idextrusoraSetupObservacao);
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