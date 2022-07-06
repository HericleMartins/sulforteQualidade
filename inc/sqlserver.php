<?php

ini_set('default_charset', 'UTF-8'); // Para o charset das páginas
ini_set('mssql.charset', 'UTF-8');

include('constantes.php');

class SqlServer {

    // Variaveis
    var $sLastError;    // msg ultimo erro
    var $sLastQuery;    // ultima query executado
    var $aResult;     // resultado
    var $iRecords;     // total de linhas
    var $iAffected;     // numero de linhas q foram afetadas
    var $aRawResults;    // coolca array
    var $aArrayedResult;   // array com apenas um registro
    var $aArrayedResults;   // n registro
    var $sHostname = 'VM-2008R2\SQLEXPRESS'; // MySQL Hostname
    var $sUsername = 'sulforte'; // MySQL Username
    var $sPassword = 'ah5bdgrYgaR'; // MySQL Password
    var $sDatabase = 'sulforte'; // MySQL Database
    var $sDBLink;   // date base
    var $lastID;


    // construtor para conectar

    public function SqlServer() {
        $this->Connect();
    }

    /**
     * Funcao para se conectar no bd
     * @return boolean -> true conectou, false nao conectou
     */
    public function connect($bPersistant = false) {
        if ($this->sDBLink) {
            mssql_close($this->sDBLink);
        }

        if ($bPersistant) {
            $this->sDBLink = mssql_connect($this->sHostname, $this->sUsername, $this->sPassword);
        } else {
            $this->sDBLink = mssql_connect($this->sHostname, $this->sUsername, $this->sPassword);
        }

        if (!$this->sDBLink) {
            $this->sLastError = 'Could not connect to server: ' . mssql_get_last_message($this->sDBLink);
            return false;
        }

        if (!$this->UseDB()) {
            $this->sLastError = 'Could not connect to database: ' . mssql_get_last_message($this->sDBLink);
            return false;
        }
        return true;
    }

    /**
     * funcao para executar no bd X
     * @return boolean
     */
    public function useDB() {
        if (!mssql_select_db($this->sDatabase, $this->sDBLink)) {
            $this->sLastError = 'Cannot select database: ' . mssql_get_last_message($this->sDBLink);
            return false;
        } else {
            return true;
        }
    }

    /**
     * Funcao para executar a query q foi passada como parametro
     * @param type $sSQLQuery -> query
     * @return boolean -> retorna se foi executada com sucesso
     */
    public function ExecuteSQL($sSQLQuery, $statusLog = false) {
        global $log;
        $this->sLastQuery = $sSQLQuery;
        if ($statusLog) {
            $log->cadastrarLogAcao($sSQLQuery);
        }
        if ($this->aResult = @mssql_query($sSQLQuery, $this->sDBLink)) {
            $this->iRecords = @mssql_num_rows($this->aResult);
            $this->iAffected = @mssql_rows_affected($this->sDBLink);
            return true;
        } else {
            $this->sLastError = @mssql_get_last_message($this->sDBLink);
            return false;
        }

        //Descomente a linha abaixo para rastrear o erro do sql, o arquivo e linha de origem | Comente os retornos acima Linha: 85 e 88.
        //$this->_traceSQL($this);
    }

    /**
     * Funcao para insercao de dados
     * @param type $log boolean -> true/false nele tu diz se a ação vai ter um log cadastrado ou não
     * @param type -> array('nomeDaColuna' => 'valor a ser inserido')
     * @param type $sTable -> tabela a ser inserida
     * @param array $aExclude
     * @return boolean -> retorna o status da insercao
     */
    public function cadastrar($log, $sTable, $aVars, $aExclude = '') {
        // Catch Exceptions
        if ($aExclude == '') {
            $aExclude = array();
        }

        array_push($aExclude, 'MAX_FILE_SIZE');

        // Prepare Variables
        $aVars = $this->SecureData($aVars);

        $sSQLQuery = 'INSERT INTO ' . $sTable;
        foreach ($aVars as $iKey => $sValue) {
            if (in_array($iKey, $aExclude)) {
                continue;
            }
            if (($sValue != '' && !is_array($sValue)) || $sValue === NULL) {
                $campos .= $iKey . ',';
                if (is_string($sValue)) {
                    $valores .= "'" . $sValue . "',";
                } else if ($sValue === NULL) {
                    $valores .= "NULL,";
                } else {
                    $valores .= $sValue . ",";
                }
            }
        }

        $sSQLQuery .= " (" . substr($campos, 0, -1) . ")";
        $sSQLQuery .= " VALUES (" . substr($valores, 0, -1) . ")";

        
        if ($this->ExecuteSQL($sSQLQuery, $log)) {
            $this->lastID = $this->Insert_ID($sTable);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Abre trasação com o banco para iniciar uma instrução segura com sql
     */
    public static function abrirTransacao() {
        global $sql;
        $sql->ExecuteSQL('BEGIN TRANSACTION',true);
    }

    /**
     * Confirma transação com o banco, comitando todo o script sql
     */
    public static function confirmarTransacao() {
        global $sql;
        $status = $sql->ExecuteSQL('COMMIT TRANSACTION', true);
        return $status;
    }

    /**
     * Cancela a transação com o banco, revertendo todo o script sql anteriormente iniciado.
     */
    public static function cancelarTransacao() {
        global $sql;
        $sql->ExecuteSQL('ROLLBACK TRANSACTION',false);
    }

    /**
     * Funcao para exclusao de dados
     * @param type $log boolean -> true/false nele tu diz se a ação vai ter um log cadastrado ou não
     * @param type $sTable -> tabela onde vai ter a exclusao
     * @param type $aWhere -> condicao da exclusao
     * @param type $sLimit -> limit de dados a serem excluido
     * @return boolean
     */
    public function remover($log, $sTable, $aWhere = '', $sLimit = '') {
        //$sSQLQuery = 'UPDATE ' . $sTable . ' SET deletado = 1 WHERE ';
        $sSQLQuery = 'DELETE FROM ' . $sTable . ' WHERE ';
        if ($aWhere != '') {
            // anti inject
            $aWhere = $this->SecureData($aWhere);
            $sSQLQuery .= $aWhere;
        }

        if ($sLimit != '') {
            $sSQLQuery .= ' LIMIT ' . $sLimit;
        }
        //echo $sSQLQuery;

        if ($this->ExecuteSQL($sSQLQuery, $log)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Funcao para exclusao de registro do BANCO DE DADOS
     * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     * :::::::::::::::::::::: ESTÁ FUNÇÃO REMOVE O REGISTRO PERMANENTE DO BANCO DE DADOS ::::::::::::::::::::::::::
     * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     * @param type $log boolean -> true/false nele tu diz se a ação vai ter um log cadastrado ou não
     * @param type $sTable -> tabela onde vai ter a exclusao
     * @param type $aWhere -> condicao da exclusao
     * @param type $sLimit -> limit de dados a serem excluido
     * @return boolean
     */
    public function removerRegistro($log, $sTable, $aWhere = '', $sLimit = '') {
        $sSQLQuery = 'DELETE FROM ' . $sTable . ' WHERE ';
        if ($aWhere != '') {
            // anti inject
            $aWhere = $this->SecureData($aWhere);
            $sSQLQuery .= $aWhere;
        }

        if ($sLimit != '') {
            $sSQLQuery .= ' LIMIT ' . $sLimit;
        }

        if ($this->ExecuteSQL($sSQLQuery, $log)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Funcao para buscar no banco de dados
     * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     * :::: CASO O SELECIONAR TENHA UM INNER JOIN É NECESSARIO PASSAR UM PARAMETRO PARA A VARIAVEL DE sOrderBy ::::
     * ::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::
     * @param type $log boolean -> true/false nele tu diz se a ação vai ter um log cadastrado ou não
     * @param type $sFrom -> tabela onde vai ocorrer a busca
     * @param type $campos -> campos que vao ser selecionados
     * @param type $aWhere -> condicao do select
     * @param type $sOrderBy -> parametro de ordenacao do select
     * @param type $sLimit -> caso tenho paginacao, passar o limit
     * @param type $sGroupBy -> caso tenha group by
     * @return boolean retorna o status da consulta
     */
    public function selecionar($log, $sFrom, $campos = '*', $aWhere = '', $sOrderBy = '', $sLimit = 'false', $sGroupBy = '', $orderPai = false) {
        // Catch Exceptions
        if (trim($sFrom) == '') {
            return false;
        }
       
        

        $sSQLQuery = "SELECT ROW_NUMBER() OVER (ORDER BY " . ($sOrderBy != "" ? $sOrderBy : current(explode(' ', $sFrom)) . " ASC") . ") AS RowNumber,
                       {$campos}
                        FROM {$sFrom}";

        if ($aWhere != '') {
            // anti inject
            if (strstr('AND', substr(trim($aWhere), -3, 3))) {
                $aWhere = removerUltimaLocalizacaoString('AND', '', $aWhere);
                // Caso o jaguara tenha colocado duas vezes AND no final
                if (strstr('AND', substr(trim($aWhere), -3, 3))) {
                    $aWhere = removerUltimaLocalizacaoString('AND', '', $aWhere);
                }
            }
            $aWhere = " WHERE " . $aWhere;
            $sSQLQuery .= $aWhere;
        }

        if ($sLimit !== 'false' && $sLimit !== null) {
            $paginacao = $sLimit - 1;
            $paginacao = $paginacao * REGISTRO_POR_PAGINA;
            $sSQLQuery = "SELECT TOP " . REGISTRO_POR_PAGINA . " * FROM (" . $sSQLQuery;
            $sSQLQuery = $sSQLQuery . ") resultado
                        WHERE RowNumber > " . $paginacao . " ORDER BY RowNumber " . ($orderPai ? ', ' . $orderPai : '') . " ASC";
        }
        if ($this->ExecuteSQL($sSQLQuery, $log)) {
            if ($this->iRecords > 0) {
                return $this->ArrayResults();
            }
            return true;
        } else {
            return false;
        }
    }
    public function executar($query,$log) {
        if ($this->ExecuteSQL($query, $log)) {
            if ($this->iRecords > 0) {
                return $this->ArrayResults();
            }
            return true;
        } else {
            return false;
        }
    }


    /**
     * Funcao para editar um registro
     * @param type $log boolean -> true/false nele tu diz se a ação vai ter um log cadastrado ou não
     * @param type $sTable -> tabela onde vai aconteder o update
     * @param type $aSet -> mesma coisa q os campos da insercao
     * @param type $aWhere -> condicao
     * @param array $aExclude
     * @return boolean
     */
    public function editar($log, $sTable, $aSet, $aWhere, $aExclude = '') {
        // Catch Exceptions
        if (trim($sTable) == '' || !is_array($aSet) || $aWhere == '') {
            return false;
        }
        if ($aExclude == '') {
            $aExclude = array();
        }

        array_push($aExclude, 'MAX_FILE_SIZE');


        // SET

        $sSQLQuery = 'UPDATE ' . $sTable . ' SET ';
        foreach ($aSet as $iKey => $sValue) {
            if (in_array($iKey, $aExclude)) {
                continue;
            }
            if (($sValue != '' && !is_array($sValue)) || $sValue === NULL || is_numeric($sValue)) {
                if ($sValue === NULL) {
                    $sSQLQuery .= $iKey . " = NULL,";
                } else if (is_string($sValue)) {
                    $sSQLQuery .= $iKey . " = '" . $sValue . "',";
                } else {
                    $sSQLQuery .= $iKey . " = " . $sValue . ",";
                }
            }
        }

        $sSQLQuery = substr($sSQLQuery, 0, -1);

        // WHERE

        $sSQLQuery .= ' WHERE ';
        if (!is_array($aWhere)) {
            $aWhere = $this->SecureData($aWhere);
            $sSQLQuery .= $aWhere;
        } else {
            foreach ($aWhere as $iKey => $sValue) {
                $sSQLQuery .= '`' . $iKey . '` = "' . $sValue . '" AND ';
            }
            $sSQLQuery = substr($sSQLQuery, 0, -5);
        }
        if ($this->ExecuteSQL($sSQLQuery, $log)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Funcao q retorna o resultado solo de um registro
     * @return type
     */
    public function arrayResult() {
        $this->ExecuteSQL($this->sLastQuery);
        $this->aArrayedResult = mssql_fetch_assoc($this->aResult);
        if (is_array($this->aArrayedResult)) {
            return $this->aArrayedResult;
        } else {
            return false;
        }
    }

    /**
     * Funcao q retorna uma array contendo todos os registro selecionados
     * @return type
     */
    public function arrayResults() {
        $this->ExecuteSQL($this->sLastQuery);
        $this->aArrayedResults = array();
        while ($aData = mssql_fetch_assoc($this->aResult)) {
            $this->aArrayedResults[] = $aData;
        }
        return $this->aArrayedResults;
    }

    /**
     * Funcao q retorna uma array com o indice a escolher, mt util
     * @param type $sKey
     * @return type
     */
    public function arrayResultsWithKey($sKey = 'id') {
        $this->ExecuteSQL($this->sLastQuery);
        if (isset($this->aArrayedResults)) {
            unset($this->aArrayedResults);
        }
        $this->aArrayedResults = array();
        while ($aRow = mssql_fetch_assoc($this->aResult)) {
            foreach ($aRow as $sTheKey => $sTheValue) {
                $this->aArrayedResults[$aRow[$sKey]][$sTheKey] = $sTheValue;
            }
        }
        return $this->aArrayedResults;
    }

    /**
     * Funcao para anti-injection
     * @param type $aData
     * @return type
     */
    public function secureData($aData) {
        if (is_array($aData)) {
            foreach ($aData as $iKey => $sVal) {
                if (!is_array($aData[$iKey])) {
                    $aData[$iKey] = $this->ms_escape_string($aData[$iKey]);
                }
            }
        } else {
            $aData = $this->ms_escape_string($aData);
        }
        return $aData;
    }

    /**
     * Funcao serve para retorna a funcao q foi executada no servidor
     * @return <String> retorna o query q foi executda
     */
    public function getQuery() {
        return $this->sLastQuery;
    }

    /**
     * Funcao que retorna o id do ultima registro inserido
     * @return <int> Retorna o id da ultima insercao
     */
    public function Insert_ID($sTable) {
        $this->ExecuteSQL("SELECT IDENT_CURRENT('" . $sTable . "')");
        $id = mssql_fetch_assoc($this->aResult);
        return $id['computed'];
    }

    /**
     * Funcao para tirar funcao do mysql(evitar SQL INFECTION)
     * @param <type> $param_name
     * @param <type> $escape
     * @return <type> retorna string sem mysql
     */
    public function getparam($param_name, $escape = true) {
        $valor = $param_name;
        if (is_array($valor))
            return $valor;
        if ($valor == '') {
            return $valor;
        }
        if (!is_numeric($valor)) {
            if ($escape) {
                $valor = strip_tags($valor);
                //$valor = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|--|\\\\)/"), "",  $valor);
                $valor = trim($valor);
                if (!get_magic_quotes_gpc()) {
                    $valor = str_replace("'", "\'", $valor);
                }
            }
        }
        return $valor;
    }

    function ms_escape_string($data) {
        if (is_numeric($data))
            return $data;
        if (!isset($data) or empty($data))
            return '';
        $non_displayables = array(
            '/%0[0-8bcef]/', // url encoded 00-08, 11, 12, 14, 15
            '/%1[0-9a-f]/', // url encoded 16-31
            '/[\x00-\x08]/', // 00-08
            '/\x0b/', // 11
            '/\x0c/', // 12
            '/[\x0e-\x1f]/'             // 14-31
        );
        foreach ($non_displayables as $regex)
            $data = preg_replace($regex, '', $data);
        $data = str_replace("'", "''", $data);
        return $data;
    }

    /**
     * Retorna o numero total de linhas do select
     * @return type
     */
    public function numRows() {

        $rows = 0;

        if (isset($this->aResult)) {
            $rows = mssql_num_rows($this->aResult);
        }

        return $rows;
    }

    /*
     * fUNÇÃO privada para mostrar as rotas dos SQL que tiveram algum erro
     * funciona em conjunto com a _dbg() em funcoes.inc.php
     * @param array $obj
     * @param int $trace
     * @param bool $bolExit
     * @author André Brandão <andrebrandao27@gmail.com>
     */
    private function _traceSQL($obj, $trace = 2, $bolExit = false) {

        if ($obj->sLastError != '') {
            $array = array();
            $array['ROTA'] = get_debug_print_backtrace($trace) . '<br />';
            $array['ERRO'] = $obj->sLastError;
            $array['SQL'] = $obj->sLastQuery;
            $array['ARRAY_RESULT'] = $obj->aArrayedResult;
            $array['ARRAY_RESULTS'] = $obj->aArrayedResults;
            _dbg($array, $bolExit);
        }
    }


}

$sql = new SqlServer();

session_start();
