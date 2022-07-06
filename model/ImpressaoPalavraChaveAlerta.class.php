<?php

class impressaoPalavraChaveAlerta {

    public $idimpressaoPalavraChaveAlerta;

    const tabela = 'impressaoPalavraChaveAlerta';

    public function __construct($idimpressaoPalavraChaveAlerta = NULL) {
        $this->idimpressaoPalavraChaveAlerta = $idimpressaoPalavraChaveAlerta;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idimpressaoPalavraChaveAlerta']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idimpressaoPalavraChaveAlerta = ' . $this->idimpressaoPalavraChaveAlerta, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idimpressaoPalavraChaveAlerta = ' . $this->idimpressaoPalavraChaveAlerta);
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