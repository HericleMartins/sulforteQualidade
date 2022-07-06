<?php

class ExtrusoraPresetup {

    public $idextrusoraPresetup;

    const tabela = 'extrusoraPresetup';

    public function __construct($idextrusoraPresetup = NULL) {
        $this->idextrusoraPresetup = $idextrusoraPresetup;
    }

    public function cadastrar(array $dados) {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idextrusoraPresetup']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idextrusoraPresetup = ' . $this->idextrusoraPresetup, 'idextrusoraPresetup');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idextrusoraPresetup = ' . $this->idextrusoraPresetup);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, "(SELECT p.*, u.usuario, CONVERT(VARCHAR(10), CAST(p.dataCriacao AS DATE), 103) AS data, CAST(p.dataCriacao AS TIME) AS hora,

(CONVERT(VARCHAR(MAX), SUBSTRING((
                  SELECT ', ' + m.materiaPrima + ': ' + CAST(REPLACE(CAST(em.quantidade AS NUMERIC(10,2)), '.', ',') AS VARCHAR(20))
                  FROM extrusoraPresetupMateria em
                  INNER JOIN materiaPrima m ON m.idmateriaPrima = em.idmateriaPrima
                  WHERE em.idextrusoraPresetup = p.idextrusoraPresetup
                  FOR XML PATH('')
                  ), 3, 5000))
            ) AS detalhe


  FROM extrusoraPresetup p
  INNER JOIN usuario u ON u.idusuario = p.idusuario) z", '*', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

}
?>

