<?php

class OrdemProducao
{

    public $idordemProducao;

    const tabela = 'ordemProducao';

    public $arrayDados = array();
    public $arrayBobinas = array();
    public $arrayMaterias = array();

    public $arrayCampos = array(
        'pcpCod' => 'numero',
        'cliCod' => 'idcliente',
        'cliNom' => 'cliente',
        'iteCod' => 'codigoitem',
        'iteNom' => 'item',
        'extMet' => 'metragemTotal',
        'extMbo' => 'metragemBobina',
        'extPeb' => 'pesoBobina',
        'extPet' => 'pesoExtrusao',
        'iteMed' => 'medidas',
        'extBal' => 'larguraBalao',
        'extMap' => 'pesoMateriaPrima',
        'extSai' => 'tipoItem',
        'extTer' => 'termoencolhivel',
        'extImp' => 'impresso',
        'extLar' => 'produtoLargura',
        'extCom' => 'produtoComprimento',
        'extEpp' => 'produtoEspessura',
        'extPex' => 'pesoBobinaCliente',
        'extTub' => 'tubete',
        'extSaf' => 'sanfona',
        'extGof' => 'gofrado',
        'extFur' => 'furos',
        'extPig' => 'pigmentado',
        'ctsTip' => 'soldaLateral',
        'embTol' => 'toleranciaQualidade',
        'MP'     => 'MP',
        'BB'     => 'BB',
        'nroBob' => 'numeroBobinas',
        'extMaq' => 'extrusora',
        'extSta' => 'situacaoExtrusora'
    );

    public function formatarDados($dado, $valor)
    {
        if ($dado == 'extPet') {
            $valorFormatado = str_replace("kg", "", $valor);
            $valorFormatado = str_replace(",", ".", $valorFormatado);
            $valorFormatado = str_replace("_", "", $valorFormatado);
        } else if ($dado == 'extMet') {
            $valorFormatado = str_replace(",", ".", $valor);
            $valorFormatado = ($valorFormatado == 'N�o Calc' ? NULL : $valorFormatado);
        } else if ($dado == 'extMbo') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extPeb') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extBal') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extMap') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extLar') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extCom') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extEpp') {
            $valorFormatado = str_replace(",", ".", $valor);
        } else if ($dado == 'extPex') {
            $valorFormatado = str_replace(",", ".", $valor);
            $valorFormatado = trim($valorFormatado);
        } else {
            $valorFormatado = $valor;
        }

        if (trim($valorFormatado) == '') {
            $valorFormatado = NULL;
        }

        return $valorFormatado;
    }

    public function tratarDadosImportados($linha)
    {

        //remove quebra de linha e tab
        $linha = str_replace("\n", "", $linha);
        $linha = str_replace("\r", "", $linha);

        $valores = explode('=', $linha);

        //verifica se � um campo v�lido
        if ($this->arrayCampos[$valores[0]] != null) {

            if ($valores[0] == 'BB') {

                //bobina
                $this->arrayBobinas[$valores[1]]['numero'] = $valores[1];
                $this->arrayBobinas[$valores[1]]['peso'] = str_replace(',', '.', $valores[2]);
                $this->arrayBobinas[$valores[1]]['idoperador'] = $valores[3];
                $this->arrayBobinas[$valores[1]]['operador'] = $valores[4];

                $dataBobina = explode('/', $valores[5]);

                $this->arrayBobinas[$valores[1]]['data'] = $dataBobina[2] . '-' . str_pad($dataBobina[1], 2, '0', STR_PAD_LEFT) .  '-' . str_pad($dataBobina[0], 2, '0', STR_PAD_LEFT);
                $this->arrayBobinas[$valores[1]]['hora'] = $valores[6];
                $this->arrayBobinas[$valores[1]]['maquina'] = $valores[7];
            } else if ($valores[0] == 'MP') {

                //mat�ria-prima
                $this->arrayMaterias[$valores[1]]['codigo'] = $valores[1];
                $this->arrayMaterias[$valores[1]]['materia'] = $valores[2];
                $this->arrayMaterias[$valores[1]]['quantidade'] = str_replace(',', '.', $valores[3]);
                $this->arrayMaterias[$valores[1]]['possuiLote'] = $valores[4];
            } else {

                //demais
                $this->arrayDados[$this->arrayCampos[$valores[0]]] = $this->formatarDados($valores[0], $valores[1]);
            }
        } else {
            return false;
        }
    }

    public function salvarCliente()
    {

        //salva ou atualiza dbo.cliente
        $arrayCliente = Cliente::carregarPor('codigo = ' . $this->arrayDados['idcliente']);
        $objCliente = new Cliente();

        if ($arrayCliente) {
            $status = $objCliente->editar(array('cliente' => $this->arrayDados['cliente']), 'idcliente = ' . $arrayCliente['idcliente']);
            $this->arrayDados['idcliente'] = $arrayCliente['idcliente'];
        } else {
            $arrayCliente = array(
                'codigo' => $this->arrayDados['idcliente'],
                'cliente' => $this->arrayDados['cliente'],
                'dataCriacao' => getData(true, '-')
            );
            $status = $objCliente->cadastrar($arrayCliente);

            if ($status) {
                $this->arrayDados['idcliente'] = $status;
            }
        }

        if ($status) {
            unset($this->arrayDados['cliente']);
        }

        return $status;
    }

    public function salvarOP()
    {

        $arrayOP = OrdemProducao::carregarPor('numero = ' . $this->arrayDados['numero']);

        if ($arrayOP) {

            //edita dbo.ordemProducao
            $status = OrdemProducao::editar($this->arrayDados, 'idordemProducao = ' . $arrayOP['idordemProducao']);
            $this->arrayDados['idordemProducao'] = $arrayOP['idordemProducao'];
        } else {

            //insere em dbo.ordemProducao
            $this->arrayDados['dataCriacao'] = getData(true, '-');
            $status = OrdemProducao::cadastrar($this->arrayDados);
            $this->arrayDados['idordemProducao'] = $status;
        }

        return $status;
    }

    public function salvarMaterias()
    {
        $status = true;
        if (count($this->arrayMaterias) > 0) {
            foreach ($this->arrayMaterias as $key => $value) {

                $objMateriaPrima = new MateriaPrima();

                $arrayMateriaPrima = $objMateriaPrima->carregarPor('codigo = ' . $value['codigo']);

                if ($arrayMateriaPrima) {
                    $status = $objMateriaPrima->editar(array('materiaPrima' => $value['materia'], 'possuiLote' => $value['possuiLote']), 'codigo = ' . $value['codigo']);
                    $idmateriaPrima = $arrayMateriaPrima['idmateriaPrima'];
                } else {
                    $arrayMateriaPrima = array(
                        'codigo' => $value['codigo'],
                        'materiaPrima' => $value['materia'],
                        'possuiLote' => $value['possuiLote'],
                        'dataCriacao' => getData(true, '-')
                    );
                    $status = $objMateriaPrima->cadastrar($arrayMateriaPrima);
                    $idmateriaPrima = $status;
                }

                if ($status && isset($this->arrayDados['idordemProducao'])) {
                    $value['idmateriaPrima'] = $idmateriaPrima;
                    unset($value['materia']);
                    unset($value['possuiLote']);
                    unset($value['codigo']);

                    $objMateria = new OrdemProducaoMateria();

                    $arrayMateria = $objMateria->carregarPor('idmateriaPrima = ' . $idmateriaPrima . ' AND idordemProducao = ' . $this->arrayDados['idordemProducao']);

                    if ($arrayMateria) {
                        $status = $objMateria->editar($value, 'idmateriaPrima = ' . $idmateriaPrima . ' AND idordemProducao = ' . $this->arrayDados['idordemProducao']);
                    } else {
                        $value['idordemProducao'] = $this->arrayDados['idordemProducao'];
                        $value['dataCriacao'] = getData(true, '-');
                        $status = $objMateria->cadastrar($value);
                    }

                    if (!$status) {
                        break 1;
                    }
                }
            }
        }
        return $status;
    }

    public function salvarBobinas()
    {
        $status = true;
        if (count($this->arrayBobinas) > 0) {
            foreach ($this->arrayBobinas as $key => $value) {
                $objOperador = new Operador();
                $arrayOperador = $objOperador->carregarPor('codigo = ' . $value['idoperador']);
                if ($arrayOperador) {
                    $status = $objOperador->editar(array('operador' => $value['operador']), 'codigo = ' . $value['idoperador']);
                    $idoperador = $arrayOperador['idoperador'];
                } else {
                    $arrayOperador = array(
                        'codigo' => $value['idoperador'],
                        'operador' => $value['operador'],
                        'naoMostrar' => '0',
                        'idtipoMaquina' => 1,
                        'dataCriacao' => getData(true, '-')
                    );
                    $status = $objOperador->cadastrar($arrayOperador);
                    $idoperador = $status;
                }

                if ($status) {
                    $value['idoperador'] = $idoperador;
                    unset($value['operador']);

                    $objBobina = new OrdemProducaoBobina();

                    $arrayBobina = $objBobina->carregarPor('idordemProducao = ' . $this->arrayDados['idordemProducao'] . ' AND numero = ' . $value['numero']);

                    if ($arrayBobina) {
                        $status = $objBobina->editar($value, 'idordemProducao = ' . $this->arrayDados['idordemProducao'] . ' AND numero = ' . $value['numero']);
                    } else {
                        $value['idordemProducao'] = $this->arrayDados['idordemProducao'];
                        $value['dataCriacao'] = getData(true, '-');
                        $status = $objBobina->cadastrar($value);
                    }

                    if (!$status) {
                        break 1;
                    }
                }
            }
        }
        return $status;
    }

    public function importarDados()
    {

        $status = $this->salvarCliente();

        if ($status) {

            $status = $this->salvarOP();

            if ($status) {

                $status = $this->salvarMaterias();

                if ($status) {

                    $status = $this->salvarBobinas();
                }
            }
        }

        return $status;
    }

    public function importarMateriaPrima()
    {

        $status = $this->salvarMaterias();

        return $status;
    }

    public function __construct($idordemProducao = NULL)
    {
        $this->idordemProducao = $idordemProducao;
    }

    public function cadastrar(array $dados)
    {
        global $sql;
        $status = $sql->cadastrar(true, self::tabela, $dados);
        return ($status ? $sql->lastID : false);
    }

    public function editar($dados, $where)
    {
        global $sql;
        unset($dados['idordemProducao']);
        return $sql->editar(true, self::tabela, $dados, $where);
    }

    public function carregar()
    {
        global $sql;
        $sql->selecionar(false, self::tabela . ' o INNER JOIN cliente c ON c.idcliente = o.idcliente', 'o.*, c.cliente, (CONVERT(VARCHAR(MAX), SUBSTRING((
                  SELECT ", " + m.materiaPrima + ": " + CAST(REPLACE(CAST(em.quantidade AS NUMERIC(10,2)), ".", ",") AS VARCHAR(20))
                  FROM ordemProducaoMateria em
                  INNER JOIN materiaPrima m ON m.idmateriaPrima = em.idmateriaPrima
                  WHERE em.idordemProducao = o.idordemProducao
                  FOR XML PATH("")
                  ), 3, 5000))
            ) AS detalhe', 'o.idordemProducao = ' . $this->idordemProducao, 'o.numero');
        return $sql->arrayResult();
    }

    public static function carregarPor($where = null)
    {
        global $sql;
        $sql->selecionar(false, self::tabela, '*', $where, 'idordemProducao');
        return $sql->arrayResult();
    }

    public function remover()
    {
        global $sql;
        return $sql->remover(true, self::tabela, 'idordemProducao = ' . $this->idordemProducao);
    }

    public static function listar($where = null, $ordem = null, $pagina = null)
    {
        global $sql;

        $sql->selecionar(false, self::tabela, '*', $where, $ordem, $pagina);

        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }

    public static function listarPendencia($where = null, $ordem = null, $pagina = null)
    {
        global $sql;
        $sql->selecionar(false, ' viewPendencia', '*', $where, $ordem, $pagina);
        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    public static function ordensNaoIniciadas($idUsuario, $dataInicial, $dataFinal)
    {
        global $sql;
        $sql->selecionar(false, ' viewPendencia', '*', "quantRegistro = 0 ", 'numero DESC, maquina ASC');
        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    public static function quantidadeSemPreSetup($idUsuario, $dataInicial, $dataFinal)
    {
        global $sql;
        $sql->ExecuteSQL("SELECT COUNT(op.idordemProducao) as quantidadeSemPreSetup FROM ordemProducao op 
        where 
              ( op.idordemProducao not in ( SELECT eps.idordemProducao FROM extrusoraPresetup eps) and
                op.idordemProducao in (SELECT vae.idordemProducao FROM viewAcompanhamentoExtrusao vae where vae.idusuario = $idUsuario and (vae.numero = '1' or vae.numero = 'A1' or vae.numero = '999' ) and cast(vae.dataCriacao as date) >= '$dataInicial' and cast(vae.dataCriacao as date) <= '$dataFinal'))");
        if ($sql->numRows() > 0) {
            return $sql->arrayResults();
        }
        return false;
    }
    public static function semPreSetup($idUsuario, $dataInicial, $dataFinal)
    {
        global $sql;
        $sql->ExecuteSQL("SELECT    c.cliente,
		                            numero,
		                            numeroBobinas,
                                    item,
                                    (SELECT CAST(min(vae.dataCriacao) AS DATE) FROM viewAcompanhamentoExtrusao vae where vae.idOrdemProducao = op.idordemProducao) as dataCriacao,
                                    (SELECT vae.idmaquina FROM viewAcompanhamentoExtrusao vae inner join maquina m on m.idmaquina = vae.idmaquina where vae.idOrdemProducao = op.idordemProducao group by vae.idmaquina) as maquina
        FROM ordemProducao op 
        inner join cliente c on c.idcliente = op.idcliente
        where 
              ( op.idordemProducao not in ( SELECT eps.idordemProducao FROM extrusoraPresetup eps) and
                op.idordemProducao in (SELECT vae.idordemProducao FROM viewAcompanhamentoExtrusao vae where vae.idusuario = $idUsuario and (vae.numero = '1' or vae.numero = 'A1' or vae.numero = '999' ) and cast(vae.dataCriacao as date) >= '$dataInicial' and cast(vae.dataCriacao as date) <= '$dataFinal'))");
        if ($sql->numRows() > 0) {
            return $sql->ArrayResults();
        }
        return false;
    }
}