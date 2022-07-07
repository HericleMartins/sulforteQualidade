<?php

class OrdemProducaoBobina {

    public $idordemProducaoBobina;

    const tabela = 'ordemProducaoBobina';

    public function __construct($idordemProducaoBobina = NULL) {
        $this->idordemProducaoBobina = $idordemProducaoBobina;
    }

    public function cadastrar(array $dados) {
        global $sql;
        $status = $sql->cadastrar(true, self::tabela, $dados);
        return ($status ? $sql->lastID : false);
    }

    public function editar($dados, $where) {
        global $sql;
        unset($dados['idordemProducaoBobina']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar() {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', 'idordemProducaoBobina = ' . $this->idordemProducaoBobina, 'numero');
        return $sql->arrayResult();
    }

    public function carregarPor($where) {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', $where, 'numero');
        return $sql->arrayResult();
    }

    public function remover() {
        global $sql;
        return $sql->remover(true, self::tabela, 'idordemProducaoBobina = ' . $this->idordemProducaoBobina);
    }

    public static function listar($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, self::tabela . ' b INNER JOIN operador o ON b.idoperador = o.idoperador', 'b.*, o.operador, CONVERT(VARCHAR(10), b.data, 103) AS dataFormatada', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarGeral($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, ' viewAcompanhamentoExtrusao v', 'v.*, CONVERT(VARCHAR(10), dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), dataCriacao, 114) as dataCriacaoHora', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarGeralCorte($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, ' viewAcompanhamentoCorte v', 'v.*, CONVERT(VARCHAR(10), dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), dataCriacao, 114) as dataCriacaoHora', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarGeralRefile($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, ' viewAcompanhamentoRefile v', 'v.*, CONVERT(VARCHAR(10), dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), dataCriacao, 114) as dataCriacaoHora', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarGeralImpressora($where = null, $ordem = null, $pagina = null) {
        global $sql;

        $sql->selecionar(false, ' viewAcompanhamentoImpressora v', 'v.*, CONVERT(VARCHAR(10), dataCriacao, 103) as dataCriacaoData, CONVERT(VARCHAR(5), dataCriacao, 114) as dataCriacaoHora', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    public static function quantidadeBobinasNaoAnalisadas($dataInicial,$dataFinal) {
        global $sql;
        $data = Date('Y-m-d');
        $sql->ExecuteSQL("Select (SUM(quantBobinaPesada) - sum(quantRegistro)) as quantidadeBobinasNaoAnalisadas from(
            SELECT
              idordemProducao,
              numero,
              maquina,
              idmaquina,
              quantBobinaPesada,
              (SELECT
                COUNT(id) AS id
              FROM (SELECT
                idextrusoraAnalise AS id,
                idordemProducao AS idop
              FROM sulforte.dbo.extrusoraAnalise
              ) AS A
              WHERE (idop = C.idordemProducao))
              AS quantRegistro
            FROM (SELECT
              O.idordemProducao,
              O.numero,
              B.maquina,
              M.idmaquina,
              COUNT(B.idordemProducaoBobina) AS quantBobinaPesada
            FROM sulforte.dbo.ordemProducao AS O
            INNER JOIN sulforte.dbo.ordemProducaoBobina AS B
              ON B.idordemProducao = O.idordemProducao
            LEFT OUTER JOIN sulforte.dbo.maquina AS M
              ON M.maquina = B.maquina
              AND M.idtipoMaquina = 1
            WHERE o.idordemProducao IN (
                    SELECT opb.IDORDEMPRODUCAO FROM sulforte.DBO.ordemProducaoBobina opb WHERE CAST(opb.dataCriacao AS DATE) >= '$dataInicial' AND  CAST(opb.dataCriacao AS DATE) <= '$dataFinal'
                    ) or O.idordemProducao in ( SELECT vAT.IDORDEMPRODUCAO FROM SULFORTE.DBO.viewareaTrabalho vAT where vat.idtipoMaquina = 1)
            GROUP BY O.idordemProducao,
                     O.numero,
                     B.maquina,
                     M.idmaquina ) AS C) as G WHERE G.quantBobinaPesada - G.quantRegistro > 0");
        
        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    public static function bobinasNaoAnalisadas($idUsuario,$dataInicial,$dataFinal) {
        global $sql;
        $sql->ExecuteSQL("SELECT * from(
            SELECT
              numero,
              maquina,
              quantBobinaPesada,
              item,
              cliente,
              bobina,
              dataCriacao,
              (SELECT
                COUNT(id) AS id
              FROM (SELECT
                idextrusoraAnalise AS id,
                idordemProducao AS idop
              FROM sulforte.dbo.extrusoraAnalise
              ) AS A
              WHERE (idop = C.idordemProducao))
              AS quantRegistro
            FROM (SELECT
              O.idordemProducao,
              O.numero,
              O.item,
              c.cliente,
              B.maquina,
              M.idmaquina,
              B.numero as bobina,
              B.dataCriacao,
              COUNT(B.idordemProducaoBobina) AS quantBobinaPesada
            FROM sulforte.dbo.ordemProducao AS O
            INNER JOIN sulforte.dbo.ordemProducaoBobina AS B
              ON B.idordemProducao = O.idordemProducao inner join
               sulforte.dbo.cliente c on c.idcliente = O.idcliente
            LEFT OUTER JOIN sulforte.dbo.maquina AS M
              ON M.maquina = B.maquina
              AND M.idtipoMaquina = 1
            WHERE o.idordemProducao IN (
                    SELECT opb.IDORDEMPRODUCAO FROM sulforte.DBO.ordemProducaoBobina opb WHERE CAST(opb.dataCriacao AS DATE) >= '$dataInicial' AND  CAST(opb.dataCriacao AS DATE) <= '$dataFinal'
                    ) or O.idordemProducao in ( SELECT vAT.IDORDEMPRODUCAO FROM SULFORTE.DBO.viewareaTrabalho vAT where vat.idtipoMaquina = 1)
            GROUP BY O.idordemProducao,
                     O.numero,
                     B.maquina,
                     M.idmaquina,
                     O.item,
                     C.cliente,
                     B.numero,
                     B.dataCriacao ) AS C) as G where G.quantBobinaPesada - G.quantRegistro > 0");
        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    
}
