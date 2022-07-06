<?php

class ExtrusaoPalavraChaveAlerta {

    public $idExtrusaoPalavraChaveAlerta;

    const tabela = 'extrusaoPalavraChaveAlerta';

    public function __construct($idExtrusaoPalavraChaveAlerta = NULL) {
        $this->idExtrusaoPalavraChaveAlerta = $idExtrusaoPalavraChaveAlerta;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idExtrusaoPalavraChaveAlerta']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idExtrusaoPalavraChaveAlerta = ' . $this->idExtrusaoPalavraChaveAlerta, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idExtrusaoPalavraChaveAlerta = ' . $this->idExtrusaoPalavraChaveAlerta);
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