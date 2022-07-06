<?php

class AreaTrabalho {

    public $idareaTrabalho;

    const tabela = 'areaTrabalho';

    public function __construct($idareaTrabalho = NULL) {
        $this->idareaTrabalho = $idareaTrabalho;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idareaTrabalho']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idareaTrabalho = ' . $this->idareaTrabalho, self::tabela);
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idareaTrabalho = ' . $this->idareaTrabalho);
    }

    public static function listarView($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, 'viewAreaTrabalho', '*', $where, $ordem, $pagina);
        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, 'areaTrabalho', '*', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>

