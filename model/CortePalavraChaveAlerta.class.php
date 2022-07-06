<?php

class CortePalavraChaveAlerta {

    public $idCortePalavraChaveAlerta;

    const tabela = 'cortePalavraChaveAlerta';

    public function __construct($idCortePalavraChaveAlerta = NULL) {
        $this->idCortePalavraChaveAlerta = $idCortePalavraChaveAlerta;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idCortePalavraChaveAlerta']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idCortePalavraChaveAlerta = ' . $this->idCortePalavraChaveAlerta, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idCortePalavraChaveAlerta = ' . $this->idCortePalavraChaveAlerta);
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