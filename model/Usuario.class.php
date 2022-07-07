<?php

class Usuario
{

    public $idusuario;

    const tabela = 'usuario';

    public function __construct($idusuario = NULL)
    {
        $this->idusuario = $idusuario;
    }

    public function cadastrar(array $dados)
    {
        global $sql;
        return $sql->cadastrar(true, self::tabela, $dados);
    }

    public function editar($dados, $where)
    {
        global $sql;
        unset($dados['idusuario']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar()
    {
        global $sql;
        $sql->selecionar(false, self::tabela . ' u INNER JOIN grupo g ON g.idgrupo=u.idgrupo', 'idusuario, usuario, email, status, u.idgrupo, grupo, telefone', 'idusuario = ' . $this->idusuario, self::tabela);
        return $sql->arrayResult();
    }

    public function remover()
    {
        global $sql;
        return $sql->remover(true, self::tabela, 'idusuario = ' . $this->idusuario);
    }

    public function verificarEmailUsuario($email)
    {
        global $sql;
        $sql->selecionar(true, self::tabela, 'idusuario, email, usuario, senha, idgrupo, status, numeroTentativa', ' email = "' . $email . '"');
        return $sql->arrayResult();
    }

    public static function listarGrupo($where = null)
    {
        global $sql;
        $sql->selecionar(false, 'grupo', 'idgrupo, grupo ', $where, ' idgrupo ASC ');
        return $sql->arrayResults();
    }

    public static function listarUsuario($where = null, $ordem = null, $pagina = null)
    {
        global $sql;

        $sql->selecionar(false, self::tabela . ' u INNER JOIN grupo g ON g.idgrupo=u.idgrupo', 'idusuario, usuario, email, status, u.idgrupo, grupo', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    
    public function carregarDadosSenha()
    {
        global $sql;
        $sql->selecionar(false, self::tabela . ' u INNER JOIN grupo g ON g.idgrupo=u.idgrupo', 'idusuario, usuario, email, status, u.idgrupo, grupo, telefone, senha', 'idusuario = ' . $this->idusuario, self::tabela);
        return $sql->arrayResult();
    }
    public static function carregarIndiceAnalista($idusuario,$dataInicial,$dataFinal)
    {
        global $sql;
        $sql->executar("SELECT COUNT ( idusuario ) as indiceExtrusao FROM viewAcompanhamentoExtrusao where ( CAST(dataCriacao AS DATE)  >= '$dataInicial' and CAST(dataCriacao AS DATE) <= '$dataFinal') and idusuario = $idusuario", false);
        $arrayExtrusao = $sql->ArrayResults();
        $sql->executar("SELECT COUNT ( idusuario ) as indiceImpressao FROM viewAcompanhamentoImpressora where ( CAST(dataCriacao AS DATE)  >= '$dataInicial' and CAST(dataCriacao AS DATE) <= '$dataFinal') and idusuario = $idusuario", false);
        $arrayImpressao = $sql->ArrayResults();
        $sql->executar("SELECT COUNT ( idusuario ) as indiceCorte FROM viewAcompanhamentoCorte where ( CAST(dataCriacao AS DATE)  >= '$dataInicial' and CAST(dataCriacao AS DATE) <= '$dataFinal') and idusuario = $idusuario", false);
        $arrayCorte = $sql->ArrayResults();
        $sql->executar("SELECT COUNT ( idusuario ) as indiceRefile FROM viewAcompanhamentoRefile where ( CAST(dataCriacao AS DATE)  >= '$dataInicial' and CAST(dataCriacao AS DATE) <= '$dataFinal') and idusuario = $idusuario", false);
        $arrayRefile = $sql->ArrayResults();
        $arrayIndices  = array(
            "Refile" => $arrayRefile[0]['indiceRefile'],
            "Extrusora" => $arrayExtrusao[0]['indiceExtrusao'],
            "Corte" => $arrayCorte[0]['indiceCorte'],
            "Impressora" => $arrayImpressao[0]['indiceImpressao'],
            "inicial" => $dataInicial,
            "final" => $dataFinal            
        );
        return $arrayIndices;
    }
    public static function carregarAnalisesExtrusao($idusuario,$dataInicial,$dataFinal)
    {
        global $sql;
        $sql->executar("SELECT v.numero,op.numero,v.dataCriacao,op.item,m.maquina,c.cliente,v.usuario FROM viewAcompanhamentoExtrusao v inner join ordemProducao op on op.idordemProducao = v.idOrdemProducao inner join maquina m on m.idmaquina = v.idmaquina inner join cliente c on c.idcliente = op.idcliente  where ( CAST(v.dataCriacao AS DATE)  >= '$dataInicial' and CAST(v.dataCriacao AS DATE) <= '$dataFinal') and v.idusuario = $idusuario order by dataCriacao desc;", false);
        $arrayExtrusao = $sql->ArrayResults();
        return $arrayExtrusao;
    }
    public static function carregarAnalisesRefile($idusuario,$dataInicial,$dataFinal)
    {
        global $sql;
        $sql->executar("SELECT v.numero,op.numero,v.dataCriacao,op.item,m.maquina,c.cliente,v.usuario FROM viewAcompanhamentoRefile v inner join ordemProducao op on op.idordemProducao = v.idOrdemProducao inner join maquina m on m.idmaquina = v.idmaquina inner join cliente c on c.idcliente = op.idcliente  where ( CAST(v.dataCriacao AS DATE)  >= '$dataInicial' and CAST(v.dataCriacao AS DATE) <= '$dataFinal') and v.idusuario = $idusuario order by dataCriacao desc;", false);
        $arrayExtrusao = $sql->ArrayResults();
        return $arrayExtrusao;
    }
    public static function carregarAnalisesImpressao($idusuario,$dataInicial,$dataFinal)
    {
        global $sql;
        $sql->executar("SELECT v.numero,op.numero,v.dataCriacao,op.item,m.maquina,c.cliente,v.usuario FROM viewAcompanhamentoImpressora v inner join ordemProducao op on op.idordemProducao = v.idOrdemProducao inner join maquina m on m.idmaquina = v.idmaquina inner join cliente c on c.idcliente = op.idcliente  where ( CAST(v.dataCriacao AS DATE)  >= '$dataInicial' and CAST(v.dataCriacao AS DATE) <= '$dataFinal') and v.idusuario = $idusuario order by dataCriacao desc;", false);
        $arrayExtrusao = $sql->ArrayResults();
        return $arrayExtrusao;
    }
    public static function carregarAnalisesCorte($idusuario,$dataInicial,$dataFinal)
    {
        global $sql;
        $sql->executar("SELECT v.numero,op.numero,v.dataCriacao,op.item,m.maquina,c.cliente,v.usuario FROM viewAcompanhamentoCorte v inner join ordemProducao op on op.idordemProducao = v.idOrdemProducao inner join maquina m on m.idmaquina = v.idmaquina inner join cliente c on c.idcliente = op.idcliente  where ( CAST(v.dataCriacao AS DATE)  >= '$dataInicial' and CAST(v.dataCriacao AS DATE) <= '$dataFinal') and v.idusuario = $idusuario order by dataCriacao desc;", false);
        $arrayExtrusao = $sql->ArrayResults();
        return $arrayExtrusao;
    }

}
