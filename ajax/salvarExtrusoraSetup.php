<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new ExtrusoraSetup();

if ((float)str_replace(',', '.', $r['espessuraMax']) < (float)str_replace(',', '.', $r['espessura'])) {
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx012e')));
    die();
}

SqlServer::abrirTransacao();

if($r['idextrusoraSetup'] != '') {

    //edi��o
    if($r['semVistoria'] == 1){
        $dados = array(
            'idoperador'            => NULL,
            'semVistoria'           => ($r['semVistoria']),
            'analise'               => 0,
            'largura'               => 0,
            'tipoMedida'            => NULL,
            'espessuraMin'          => NULL,
            'espessuraMax'          => NULL,
            'espessuraLargura'      => NULL,
            'espessuraComprimento'  => NULL,
            'espessuraPeso'         => NULL,
            'espessuraMedia'        => NULL,
            'sanfonaEsq'            => NULL,
            'sanfonaDir'            => NULL,
            'impressao'             => NULL,
            'faceamento'            => NULL,
            'obs'                   => ($r['observacaoTexto'])
        );
    } else {
        $dados = array(
            'idoperador'            => (int)$r['operador'],
            'semVistoria'           => $r['semVistoria'],
            'analise'               => ($r['analise']),
            'largura'               => (float)str_replace(',', '.', $r['largura']),
            'tipoMedida'            => (isset($r['tipoMedida']) ? $r['tipoMedida'] : NULL),
            'espessuraMin'          => (($r['espessura'] != '') ? str_replace(',', '.', $r['espessura']) : NULL),
            'espessuraMax'          => (($r['espessuraMax'] != '') ? str_replace(',', '.', $r['espessuraMax']) : NULL),
            'espessuraLargura'      => (($r['espessuraLargura'] != '') ? str_replace(',', '.', $r['espessuraLargura']) : NULL),
            'espessuraComprimento'  => (($r['espessuraComprimento'] != '') ? str_replace(',', '.', $r['espessuraComprimento']) : NULL),
            'espessuraPeso'         => (($r['espessuraPeso'] != '') ? str_replace(',', '.', $r['espessuraPeso']) : NULL),
            'espessuraMedia'        => (($r['espessuraMedia'] != '') ? str_replace(',', '.', $r['espessuraMedia']) : NULL),
            'obs'                   => ($r['observacaoTexto']),
            'impressao'             => (int)$r['impressao'],
            'faceamento'            => (($r['faceamento'] != '') ? (int)$r['faceamento'] : NULL)
        );
    }
    $dados = array_map('utf8_decode', $dados);
    //Verifica se sanfona � NULL
    $dados['sanfonaEsq'] = ($r['sanfonaEsq'] ? str_replace(',', '.', $r['sanfonaEsq']) : NULL);
    $dados['sanfonaDir'] = ($r['sanfonaDir'] ? str_replace(',', '.', $r['sanfonaDir']) : NULL);

    $status = $obj->editar($dados, 'idextrusoraSetup = ' . $r['idextrusoraSetup']);
    $id = (int)$r['idextrusoraSetup'];
} else {

    //cadastro
    if($r['semVistoria'] == 1){
        $dados = array(
            'idmaquina'             => (int)$r['idmaquina'],
            'idordemProducao'       => (int)$r['idordemProducao'],
            'idoperador'            => NULL,
            'semVistoria'           => $r['semVistoria'],
            'analise'               => 0,
            'largura'               => 0,
            'obs'                   => ($r['observacaoTexto']),
            'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
            'dataCriacao'           => getData()
        );
    } else {
        $dados = array(
            'idmaquina'             => (int)$r['idmaquina'],
            'idordemProducao'       => (int)$r['idordemProducao'],
            'idoperador'            => (int)$r['operador'],
            'semVistoria'           => $r['semVistoria'],
            'analise'               => ($r['analise']),
            'largura'               => (float)str_replace(',', '.', $r['largura']),
            'tipoMedida'            => (isset($r['tipoMedida']) ? $r['tipoMedida'] : NULL),
            'espessuraMin'          => (isset($r['espessura']) ? str_replace(',', '.', $r['espessura']) : NULL),
            'espessuraMax'          => (isset($r['espessuraMax']) ? str_replace(',', '.', $r['espessuraMax']) : NULL),
            'espessuraLargura'      => (isset($r['espessuraLargura']) ? str_replace(',', '.', $r['espessuraLargura']) : NULL),
            'espessuraComprimento'  => (isset($r['espessuraComprimento']) ? str_replace(',', '.', $r['espessuraComprimento']) : NULL),
            'espessuraPeso'         => (isset($r['espessuraPeso']) ? str_replace(',', '.', $r['espessuraPeso']) : NULL),
            'espessuraMedia'        => (isset($r['espessuraMedia']) ? str_replace(',', '.', $r['espessuraMedia']) : NULL),
            'obs'                   => ($r['observacaoTexto']),
            'impressao'             => (int)$r['impressao'],
            'faceamento'            => (isset($r['faceamento']) ? (int)$r['faceamento'] : NULL),
            'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
            'dataCriacao'           => getData()
        );
    }
    $dados = array_map('utf8_decode', $dados);
    //Verifica se sanfona � NULL
    $dados['sanfonaEsq'] = ($r['sanfonaEsq'] ? str_replace(',', '.', $r['sanfonaEsq']) : NULL);
    $dados['sanfonaDir'] = ($r['sanfonaDir'] ? str_replace(',', '.', $r['sanfonaDir']) : NULL);

    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new ExtrusoraSetupObservacao();
    $status = $objObs->removerPor('idextrusoraSetup = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idextrusoraSetup'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idextrusoraSetup' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs =  array_map('utf8_decode', $dadosObs);

            $objObs = new ExtrusoraSetupObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idextrusoraSetup'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idextrusoraSetup'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('ESx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('ESx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idextrusoraSetup'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ESx004e')));
    }
}