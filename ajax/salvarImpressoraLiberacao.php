<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new ImpressoraLiberacao();

SqlServer::abrirTransacao();

if($r['idimpressoraLiberacao'] != '') {

    //edi��o
    if($r['semVistoria'] == 1){
        $dados = array(
            'idoperador'            => NULL,
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => NULL,

            'larguraEmbalagem'       => NULL,
            'passoEmbalagem'        => NULL,

            'analiseVisual'         => NULL,
            'tonalidade'            => NULL,
            'conferenciaArte'       => NULL,
            'ladoTratamento'        => NULL,
            'leituraCodigoBarras'   => NULL,
            'testeAderenciaTinta'   => NULL,
            'sentidoDesbobinamento' => NULL,

            'obs'                   => $r['observacaoTexto'],
            'reinspecao'            => NULL
        );
    } else {
        $dados = array(
            'idoperador'            => (int)$r['operador'],
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => (int)$r['bobina'],

            'larguraEmbalagem'      => (float)str_replace(',', '.', $r['larguraEmbalagem']),
            'passoEmbalagem'        => ($r['passoEmbalagem'] != '' ? (float)str_replace(',', '.', $r['passoEmbalagem']) : NULL),

            'analiseVisual'         => (int)$r['analiseVisual'],
            'tonalidade'            => (int)$r['tonalidade'],
            'conferenciaArte'       => (int)$r['conferenciaArte'],
            'ladoTratamento'        => (int)$r['ladoTratamento'],
            'leituraCodigoBarras'   => (int)$r['leituraCodigoBarras'],
            'testeAderenciaTinta'   => (int)$r['testeAderenciaTinta'],
            'sentidoDesbobinamento' => (int)$r['sentidoDesbobinamento'],

            'obs'                   => $r['observacaoTexto'],
            'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
        );
    }
} else {

    //cadastro
    if($r['semVistoria'] == 1){
        $dados = array(
            'idmaquina'             => (int)$r['idmaquina'],
            'idordemProducao'       => (int)$r['idordemProducao'],
            'idoperador'            => NULL,
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => NULL,

            'larguraEmbalagem'      => NULL,
            'passoEmbalagem'        => NULL,

            'analiseVisual'         => NULL,
            'tonalidade'            => NULL,
            'conferenciaArte'       => NULL,
            'ladoTratamento'        => NULL,
            'leituraCodigoBarras'   => NULL,
            'testeAderenciaTinta'   => NULL,
            'sentidoDesbobinamento' => NULL,

            'obs'                   => $r['observacaoTexto'],
            'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
            'dataCriacao'           => getData(),
            'reinspecao'            => NULL
        );
    } else {
        $dados = array(
            'idmaquina'             => (int)$r['idmaquina'],
            'idordemProducao'       => (int)$r['idordemProducao'],
            'idoperador'            => (int)$r['operador'],
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => (int)$r['bobina'],

            'larguraEmbalagem'      => (float)str_replace(',', '.', $r['larguraEmbalagem']),
            'passoEmbalagem'        => ($r['passoEmbalagem'] != '' ? (float)str_replace(',', '.', $r['passoEmbalagem']) : NULL),

            'analiseVisual'         => (int)$r['analiseVisual'],
            'tonalidade'            => (int)$r['tonalidade'],
            'conferenciaArte'       => (int)$r['conferenciaArte'],
            'ladoTratamento'        => (int)$r['ladoTratamento'],
            'leituraCodigoBarras'   => (int)$r['leituraCodigoBarras'],
            'testeAderenciaTinta'   => (int)$r['testeAderenciaTinta'],
            'sentidoDesbobinamento' => (int)$r['sentidoDesbobinamento'],

            'obs'                   => $r['observacaoTexto'],
            'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
            'dataCriacao'           => getData(),
            'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
        );
    }
}

if ($r['bobinaValorOutro']){
    $dadosOutraBobina = array(
        'idordemProducao' => (int)$r['idordemProducao'],
        'numero' => $r['bobinaValorOutro'],
        'peso' => 0,
        'idoperador' => 1, //criar operador "sistema" no BD
        'dataCriacao' => getData()
    );
    $dadosOutraBobina = array_map('utf8_decode', $dadosOutraBobina);
    $objOutraBobina = new OrdemProducaoBobina();
    $statusOutraBobina = $objOutraBobina->cadastrar($dadosOutraBobina);
    $idoutraBobina = $sql->lastID;
    $dados['idordemProducaoBobina'] = $idoutraBobina;
    if (!$statusOutraBobina) {
        SqlServer::cancelarTransacao();
        if((int)$r['idimpressoraLiberacao'] > 0) {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ILx012e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('ILx013e')));
        }
        die();
    }
}

if($r['idimpressoraLiberacao'] != '') {
    $status = $obj->editar($dados, 'idimpressoraLiberacao = ' . $r['idimpressoraLiberacao']);
    $id = (int)$r['idimpressoraLiberacao'];
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new ImpressoraLiberacaoObservacao();
    $status = $objObs->removerPor('idimpressoraLiberacao = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idimpressoraLiberacao'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idimpressoraLiberacao' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs = array_map('utf8_decode',  $dadosObs);

            $objObs = new ImpressoraLiberacaoObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idimpressoraLiberacao'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idimpressoraLiberacao'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RSx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RSx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idimpressoraLiberacao'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx004e')));
    }
}