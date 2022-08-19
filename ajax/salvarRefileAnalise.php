<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new RefileAnalise();

SqlServer::abrirTransacao();

if($r['idrefileAnalise'] != '') {

    $dados = array(
        'idoperador'            => (isset($r['operador']) ? (int)$r['operador'] : NULL),
        'idordemProducaoBobina' => (int)$r['bobina'],
        'largura'               => (isset($r['largura']) ? (float)str_replace(',', '.', $r['largura']) : NULL),
        'comprimento'           => ($r['comprimento'] != '' ? (float)str_replace(',', '.', $r['comprimento']) : NULL),
        'picote'                => ($r['qualidadePicote'] != '' ? (int)$r['qualidadePicote'] : NULL),
        'novaBobina'            => ($r['qualidadeNovaBobina'] != '' ? (int)$r['qualidadeNovaBobina'] : NULL),
        'impressao'             => ($r['qualidadeImpressao'] != '' ? (int)$r['qualidadeImpressao'] : NULL),
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
        'largura'               => (isset($r['largura']) ? (float)str_replace(',', '.', $r['largura']) : NULL),
        'comprimento'           => ($r['comprimento'] != '' ? (float)str_replace(',', '.', $r['comprimento']) : NULL),
        'picote'                => ($r['qualidadePicote'] != '' ? (int)$r['qualidadePicote'] : NULL),
        'novaBobina'            => ($r['qualidadeNovaBobina'] != '' ? (int)$r['qualidadeNovaBobina'] : NULL),
        'impressao'             => ($r['qualidadeImpressao'] != '' ? (int)$r['qualidadeImpressao'] : NULL),
        'obs'                   => $r['observacaoTexto'],
        'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
        'dataCriacao'           => getData(),
        'semAnalise'            => ($r['semAnalise'] == '1' ? 1 : NULL),
        'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
    );
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
        if((int)$r['idrefileAnalise'] > 0) {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx012e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx013e')));
        }
        die();
    }
}

if($r['idrefileAnalise'] != '') {
    $status = $obj->editar($dados, 'idrefileAnalise = ' . $r['idrefileAnalise']);
    $id = (int)$r['idrefileAnalise'];
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new RefileAnaliseObservacao();
    $status = $objObs->removerPor('idrefileAnalise = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idrefileAnalise'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idrefileAnalise' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs =  array_map('utf8_decode', $dadosObs);

            $objObs = new RefileAnaliseObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idrefileAnalise'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idrefileAnalise'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RAx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RAx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idrefileAnalise'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RAx004e')));
    }
}