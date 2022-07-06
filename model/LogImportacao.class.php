<?php

class LogImportacao {

    public $idlogImportacao;

    const tabela = 'logImportacao';

    public function __construct($idlogImportacao = NULL) {
        $this->idlogImportacao = $idlogImportacao;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idlogImportacao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idlogImportacao = ' . $this->idlogImportacao, 'arquivo');
        return $sql->arrayResult();
    }

    public function salvarLog($arquivo, $status) {
        $arrayLog = array(
            'arquivo' => $arquivo,
            'status' => $status,
            'dataCriacao' => getData(true, '-')
        );
        $this->cadastrar($arrayLog);
    }
}
?>

