<?php

class RefilePalavraChaveAlerta {

    public $idRefilePalavraChaveAlerta;

    const tabela = 'refilePalavraChaveAlerta';

    public function __construct($idRefilePalavraChaveAlerta = NULL) {
        $this->idRefilePalavraChaveAlerta = $idRefilePalavraChaveAlerta;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idRefilePalavraChaveAlerta']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idRefilePalavraChaveAlerta = ' . $this->idRefilePalavraChaveAlerta, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idRefilePalavraChaveAlerta = ' . $this->idRefilePalavraChaveAlerta);
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