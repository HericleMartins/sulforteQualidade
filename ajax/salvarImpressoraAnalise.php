<?php
include( "../inc/load.php");

$r = getRequest($_POST);

SqlServer::abrirTransacao();

if($r['idimpressoraAnalise'] != '') {

    $dados = array(
        'idoperador'            => (isset($r['operador']) ? (int)$r['operador'] : NULL),
        'idordemProducaoBobina' => (int)$r['bobina'],

        'larguraEmbalagem'      => (isset($r['larguraEmbalagem']) ? (float)str_replace(',', '.', $r['larguraEmbalagem']) : NULL),
        'passoEmbalagem'        => ($r['passoEmbalagem'] != '' ? (float)str_replace(',', '.', $r['passoEmbalagem']) : NULL),

        'analiseVisual'         => ($r['analiseVisual'] != '' ? (int)$r['analiseVisual'] : NULL),
        'tonalidade'            => ($r['tonalidade'] != '' ? (int)$r['tonalidade'] : NULL),
        'conferenciaArte'       => ($r['conferenciaArte'] != '' ? (int)$r['conferenciaArte'] : NULL),
        'ladoTratamento'        => ($r['ladoTratamento'] != '' ? (int)$r['ladoTratamento'] : NULL),
        'leituraCodigoBarras'   => ($r['leituraCodigoBarras'] != '' ? (int)$r['leituraCodigoBarras'] : NULL),
        'testeAderenciaTinta'   => ($r['testeAderenciaTinta'] != '' ? (int)$r['testeAderenciaTinta'] : NULL),
        'sentidoDesbobinamento' => ($r['sentidoDesbobinamento'] != '' ? (int)$r['sentidoDesbobinamento'] : NULL),

        'obs'                   => $r['observacaoTexto'],
        'semAnalise'            => ($r['semAnalise'] == '1' ? 1 : NULL),
        'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
    );
} else {

    //cadastro
    $dados = array(
        'idmaquina'             => (int)$r['idmaquina'],
        'idordemProducao'       => (int)$r['idordemProducao'],
        'idoperador'            => (isset($r['operador']) ? (int)$r['operador'] : NULL),
        'idordemProducaoBobina' => (int)$r['bobina'],

        'larguraEmbalagem'      => (isset($r['larguraEmbalagem']) ? (float)str_replace(',', '.', $r['larguraEmbalagem']) : NULL),
        'passoEmbalagem'        => ($r['passoEmbalagem'] != '' ? (float)str_replace(',', '.', $r['passoEmbalagem']) : NULL),

        'analiseVisual'         => ($r['analiseVisual'] != '' ? (int)$r['analiseVisual'] : NULL),
        'tonalidade'            => ($r['tonalidade'] != '' ? (int)$r['tonalidade'] : NULL),
        'conferenciaArte'       => ($r['conferenciaArte'] != '' ? (int)$r['conferenciaArte'] : NULL),
        'ladoTratamento'        => ($r['ladoTratamento'] != '' ? (int)$r['ladoTratamento'] : NULL),
        'leituraCodigoBarras'   => ($r['leituraCodigoBarras'] != '' ? (int)$r['leituraCodigoBarras'] : NULL),
        'testeAderenciaTinta'   => ($r['testeAderenciaTinta'] != '' ? (int)$r['testeAderenciaTinta'] : NULL),
        'sentidoDesbobinamento' => ($r['sentidoDesbobinamento'] != '' ? (int)$r['sentidoDesbobinamento'] : NULL),

        'obs'                   => $r['observacaoTexto'],
        'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
        'dataCriacao'           => getData(),
        'semAnalise'            => ($r['semAnalise'] == '1' ? 1 : NULL),
        'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
    );
}

if ($r['bobinaValorOutro']){
    $obj = new OrdemProducaoBobina();
    $arrayBobina = $obj->carregarPor('idordemProducao = ' . $r['idordemProducao'] . ' AND numero = ' . (int)$r['bobinaValorOutro']);
    if ($arrayBobina) {
        $dados['idordemProducaoBobina'] = $arrayBobina['idordemProducaoBobina'];
    } else {
        $dadosOutraBobina = array(
            'idordemProducao' => (int)$r['idordemProducao'],
            'numero' => $r['bobinaValorOutro'],
            'peso' => 0,
            'idoperador' => 1, //criar operador "sistema" no BD
            'dataCriacao' => getData()
        );
        $dadosOutraBobina = $dadosOutraBobina;
        $objOutraBobina = new OrdemProducaoBobina();
        $statusOutraBobina = $objOutraBobina->cadastrar($dadosOutraBobina);
        $idoutraBobina = $sql->lastID;
        $dados['idordemProducaoBobina'] = $idoutraBobina;
        if (!$statusOutraBobina) {
            SqlServer::cancelarTransacao();
            if ((int)$r['idimpressoraAnalise'] > 0) {
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx012e')));
            } else {
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx013e')));
            }
            die();
        }
    }
}

$obj = new ImpressoraAnalise();

if($r['idimpressoraAnalise'] != '') {
    $status = $obj->editar($dados, 'idimpressoraAnalise = ' . $r['idimpressoraAnalise']);
    $id = (int)$r['idimpressoraAnalise'];
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new ImpressoraAnaliseObservacao();
    $status = $objObs->removerPor('idimpressoraAnalise = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idimpressoraAnalise'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idimpressoraAnalise' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs =  $dadosObs;

            $objObs = new ImpressoraAnaliseObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idimpressoraAnalise'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idimpressoraAnalise'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('IAx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('IAx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idimpressoraAnalise'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('IAx004e')));
    }
}