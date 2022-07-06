<?php
function getRequest(&$valor, $tipoCodificacao = 'decode', $antiInjection = true)
{
    if (is_array($valor)) {
        foreach ($valor as $key => $value) {
            if (is_array($value)) {
                $valor[$key] = getRequest($value, $tipoCodificacao, $antiInjection);
            } else {
                // Codificar strings para não dar erro no retorno com acento
                if (codificacao($value) == 'UTF-8' && $tipoCodificacao == 'decode') {
                    utf8_decode($value);
                } else if ($tipoCodificacao == 'encode') {
                    utf8_encode($value);
                }

                // Como o retorno do json tb utiliza essa função aqui deve ser o retorno da data, onde de sql(XXXX-XX-XX) vai para BR(XX/XX/XXXX)
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                    $value = formatarData($value, '/');
                }

                // Em nos retornos do ajax, nao deve haver verificações e coisas do genero
                if ($antiInjection) {
                    if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $value)) {
                        $value = formatarData($value, '-');
                    }
                    $valor[$key] = strip_tags($value);
                    $valor[$key] = preg_replace(sql_regcase('/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/'), '', $value);
                } else {
                    $valor[$key] = $value;
                }
            }
        }
    } else {
        if ($antiInjection) {
            if (codificacao($valor) == 'UTF-8') {
                utf8_decode($valor);
            }
            $valor = strip_tags($valor);
            $valor = preg_replace(sql_regcase('/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/'), '', $valor);
        } else {
            $valor = $valor;
        }
    }
    return $valor;
}

/**
 * Função usada para retornar o dia com a hora atual ou sem a hora atual
 * @param type $hora boolean informado se deseja que retorne a hora, ex: false
 * @param type $formato string informando ql o tipo de retorno da data, ex: -
 * @return type retorna apenas a data no formato sql
 * 2012-06-31
 */
function getData($hora = true, $formato = '-')
{
    if ($formato == '-') {
        return date('Y-m-d' . ($hora ? ' H:i:s' : ''));
    } else {
        return date('d/m/Y' . ($hora ? ' H:i:s' : ''));
    }
}

/**
 * FUNÇÃO IMPORTANTE
 *  Função resposanvel por sempre que o sistema executar um new(ex: $objExemplo = new Exemplo(); )
 *  a função verifica se existe um arquivo chamado model/Exemplo.class.php, caso existe ele
 *  faz o include automaticamente
 * @param type $class
 */
function __autoload($class)
{

    $diretorios = array('model/', 'rn/');

    foreach ($diretorios as $valor) {
        $file = $valor . $class . '.class.php';

        if (file_exists($file)) {
            include $file;
        } else if (file_exists('../' . $file)) {
            include '../' . $file;
        }
    }
}

function montarMenu($where, $submenu = 0)
{
    $arrayMenu = '';
    if (!isset($_SESSION[SESSAO_SISTEMA]['idgrupo'])) {
        $_SESSION[SESSAO_SISTEMA]['idgrupo'] = 12;
    }
    $objPagina = new Pagina();
    $arrayPagina = $objPagina->listarPaginaFilho('idgrupo = ' . $_SESSION[SESSAO_SISTEMA]['idgrupo'] . ' AND ' . $where);
    foreach ($arrayPagina as $pagina) {
        $arrayPaginaFilho = $objPagina->listarPaginaFilho('idgrupo = ' . $_SESSION[SESSAO_SISTEMA]['idgrupo'] . ' AND pagina_idpagina = ' . $pagina['idpagina']);
        if ($arrayPaginaFilho) {
            $arrayMenu .= '<li class="' . ($submenu ? 'dropdown-submenu' : 'dropdown') . '">';
            $arrayMenu .= '<a class="dropdown-toggle" data-toggle="dropdown" href="javascript:;;">' . utf8_encode($pagina['pagina']) . ($submenu ? '' : ' <b class="caret"></b>') . '</a>';
            $arrayMenu .= '<ul class="dropdown-menu">';
            $arrayMenu .= montarMenu('pagina_idpagina = ' . $pagina['idpagina'], 1);
            $arrayMenu .= '</ul>';
        } else {
            $arrayMenu .= '<li>';
            if ($pagina['target'] == 1) {
                $target = ' target="_blank" ';
            } else {
                $target = '';
            }
            $arrayMenu .= '<a ' . $target . ' href="' . $pagina['link'] . '">' . utf8_encode($pagina['pagina']) . '</a>';
        }
        $arrayMenu .= '</li>';
    }

    return $arrayMenu;
}

/**
 * Funcao faz o json_encode de uma array e transforma em utf
 * FUNÇÃO deve ser usada no retorno do ajax para nao ter q ficar colocando
 * utf8_encode em cada string
 * @param type $json array com os valores
 * @param type $utf boolean se a array deve passar pelo utf8_encode
 * @return type
 */
function retornarJsonEncode($json, $utf = true)
{
    if ($utf) {
        return json_encode(getRequest($json, 'encode', false));
    } else {
        return json_encode($json);
    }
}

/**
 * Função que verifica qual a codificação da string
 * @param type $string texto qualquer
 * @return type retorna qual o tipo da string
 */
function codificacao($string)
{
    return mb_detect_encoding($string . 'x', 'UTF-8, ISO-8859-1');
}

/**
 *
 * @param $cod constante da mensagem
 */
function montarMensagemRetorno($cod)
{
    if (constant($cod)) {
        $tipo = substr($cod, -1, 1);
        if ($tipo == 's') {
            //sucesso
            $erro = constant($cod);
        } else if ($tipo = 'e') {
            //erro
            $erro = "<p>" . constant($cod) . "</p><p>Código de erro <b>" . $cod . "</b></p>";
        } else {
            $erro = constant($cod);
        }
    } else {
        $erro = "<p>Ops, ocorreu um erro</p><p>Código de erro <b>RRx001e</b></p>";
    }
    return $erro;
}

/**
 * Função que reduz uma string colocando ... no final
 * @param type $string texto qualquer
 * @param type $int tamanho max da string
 */
function truncateString($string, $tamanho)
{
    $stringFormatada = substr($string, 0, $tamanho);
    if (strlen($string) > $tamanho) {
        $stringFormatada = $stringFormatada . '...';
    }
    return $stringFormatada;
}

/**
 * Função que localiza uma palavra em uma string e faz o replace na ultima ocorrencia encontrada
 * @param type $search -> string, procura por, ex: 'label'
 * @param type $replace -> palavra a ser substituida, ex: 'exemplo'
 * @param type $subject -> texto que sera tratado, ex: '<label> Exemplo da função </label>'
 * @return type retorna uma string, acompanhamento os exemplos acima, ex: '<label> Exemplo da função </exemplo>'
 */
function removerUltimaLocalizacaoString($sSearch, $sReplace, $sSubject)
{
    return preg_replace('~(.*)' . preg_quote($sSearch, '~') . '~', '$1' . $sReplace, $sSubject, 1);
}

function formatarData($data, $formato = '/')
{

    if ($formato == '-' && preg_match('^([0-3]{1}[0-9]{1})/([0-1]{1}[0-9]{1})/([1-2]{1}[0-9]{3})$^', $data, $arrayData)) {
        return $arrayData[3] . '-' . verificarTamanhoString($arrayData[2], 2) . '-' . verificarTamanhoString($arrayData[1], 2);
    } else if ($formato == '/' && preg_match('^([1-2]{1}[0-9]{3})-([0-1]{1}[0-9]{1})-([0-3]{1}[0-9]{1})$^', $data, $arrayData)) {
        return verificarTamanhoString($arrayData[3], 2) . '/' . verificarTamanhoString($arrayData[2], 2) . '/' . $arrayData[1];
    } else if ($formato == '-' && preg_match('^([0-1]{1}[0-9]{1})/([1-2]{1}[0-9]{3})$^', $data, $arrayData)) {
        return $arrayData[2] . '-' . verificarTamanhoString($arrayData[1], 1);
    } else {
        return '-';
    }
}

/**
 * Funcao para verificar o tamanho da string
 * caso nao tenha o tamanho soliciatado a função preencha com o valor informado no segundo parametro
 * @param string $string
 * @param type $deveTer
 * @param type $preencher
 * @return string
 */
function verificarTamanhoString($string, $deveTer, $preencher = '0')
{
    if (strlen($string) < $deveTer) {
        $string = $preencher . $string;
    }
    return $string;
}

/**
 *  responsavel por montar um select no TemplatePower
 * @global type $objTpl variavel global do template
 * @param type $array array a ser mostrada no select
 * @param type $select nome do bloco onde vai ser montado o select
 * @param type $idselecionado caso tenha um id selecionado
 * @param type $selecione se quiser mostrar a informação "Selecione..."
 * @param type $tipoSelecao posição do array $arrayTipoSelecao que contém outros tipo de informação ex. "Selecione um item..."
 */
function montarSelect($array, $select, $idselecionado = NULL, $selecione = true, $tipoSelecao = 0, $label = false)
{
    global $objTpl, $arrayTipoSelecao;

    if ($selecione) {
        $objTpl->newBlock($select);
        $objTpl->assign('valor', '');
        $objTpl->assign('nome', 'Selecione..');
    }

    if (is_array($array)) {
        foreach ($array as $r) {
            $id = next($r);
            $nome = next($r);

            $objTpl->newBlock($select);
            $objTpl->assign('valor', $id);
            $objTpl->assign('nome', $nome);
            if ($label) {
                $labelimpressao = next($r);
                $objTpl->assign('label', $labelimpressao);
            }
            $objTpl->assign('checked', ($id == $idselecionado ? 'selected' : ''));
        }
        $objTpl->gotoBlock('_ROOT');
    }
}

function verificarPalavraChaveAlertaCS($descricaoItem)
{
    $r = array();
    $mensagemPalavraChave = false;
    $palavraChave = false;
    $arrayPalavraChave = array();
    $objCortePalavraChaveAlerta = new CortePalavraChaveAlerta();
    $arrayCortePalavraChaveAlerta = $objCortePalavraChaveAlerta->listar('situacao IS NULL');
    if ($arrayCortePalavraChaveAlerta) {
        foreach ($arrayCortePalavraChaveAlerta as $value) {
            //array_push($arrayPalavraChave, $value['cortePalavraChaveAlerta']);
            $r['status'] = preg_match('/(' . $value['cortePalavraChaveAlerta'] . ')+/i', $descricaoItem);
            if ($r['status']) {
                $r['mensagemPalavraChave'] =  $value['mensagem'];
                break;
            }
        }
        //$palavraChave = implode($arrayPalavraChave, "|");
        //$r = preg_match('/(' . $palavraChave . ')+/i', $descricaoItem);
    }
    return $r;
}
function verificarPalavraChaveAlertaExtrusao($descricaoItem)
{
    $r = array();
    $mensagemPalavraChave = false;
    $palavraChave = false;
    $arrayPalavraChave = array();
    $objExtrusaoPalavraChaveAlerta = new ExtrusaoPalavraChaveAlerta();
    $arrayExtrusaoPalavraChaveAlerta = $objExtrusaoPalavraChaveAlerta->listar('situacao IS NULL');
    if ($arrayExtrusaoPalavraChaveAlerta) {
        foreach ($arrayExtrusaoPalavraChaveAlerta as $value) {
            //array_push($arrayPalavraChave, $value['cortePalavraChaveAlerta']);
            $r['status'] = preg_match('/(' . $value['extrusaoPalavraChaveAlerta'] . ')+/i', $descricaoItem);
            if ($r['status']) {
                $r['mensagemPalavraChave'] =  $value['mensagem'];
                break;
            }
        }
        //$palavraChave = implode($arrayPalavraChave, "|");
        //$r = preg_match('/(' . $palavraChave . ')+/i', $descricaoItem);
    }
                
    return $r;
}
function verificarPalavraChaveAlertaImpressao($descricaoItem)
{
    $r = array();
    $mensagemPalavraChave = false;
    $palavraChave = false;
    $arrayPalavraChave = array();
    $objImpressaoPalavraChaveAlerta = new ImpressaoPalavraChaveAlerta();
    $arrayImpressaoPalavraChaveAlerta = $objImpressaoPalavraChaveAlerta->listar('situacao IS NULL');
    if ($arrayImpressaoPalavraChaveAlerta) {
        foreach ($arrayImpressaoPalavraChaveAlerta as $value) {
            //array_push($arrayPalavraChave, $value['cortePalavraChaveAlerta']);
            $r['status'] = preg_match('/(' . $value['impressaoPalavraChaveAlerta'] . ')+/i', $descricaoItem);
            if ($r['status']) {
                $r['mensagemPalavraChave'] =  $value['mensagem'];
                break;
            }
        }
        //$palavraChave = implode($arrayPalavraChave, "|");
        //$r = preg_match('/(' . $palavraChave . ')+/i', $descricaoItem);
    }
    return $r;
}
function verificarPalavraChaveAlertaRefile($descricaoItem)
{
    $r = array();
    $mensagemPalavraChave = false;
    $palavraChave = false;
    $arrayPalavraChave = array();
    $objRefilePalavraChaveAlerta = new RefilePalavraChaveAlerta();
    $arrayRefilePalavraChaveAlerta = $objRefilePalavraChaveAlerta->listar('situacao IS NULL');
    if ($arrayRefilePalavraChaveAlerta) {
        foreach ($arrayRefilePalavraChaveAlerta as $value) {
            //array_push($arrayPalavraChave, $value['cortePalavraChaveAlerta']);
            $r['status'] = preg_match('/(' . $value['refilePalavraChaveAlerta'] . ')+/i', $descricaoItem);
            if ($r['status']) {
                $r['mensagemPalavraChave'] =  $value['mensagem'];
                break;
            }
        }
        //$palavraChave = implode($arrayPalavraChave, "|");
        //$r = preg_match('/(' . $palavraChave . ')+/i', $descricaoItem);
    }
    return $r;
}
