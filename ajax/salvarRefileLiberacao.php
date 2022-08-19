<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new RefileLiberacao();

SqlServer::abrirTransacao();

if($r['idrefileLiberacao'] != '') {

    //edi��o
    if($r['semVistoria'] == 1){
        $dados = array(
            'idoperador'            => NULL,
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => NULL,
            'largura'               => NULL,
            'comprimento'           => NULL,
            'picote'                => NULL,
            'novaBobina'            => NULL,
            'impressao'             => NULL,
            'obs'                   => $r['observacaoTexto'],
            'reinspecao'            => NULL
        );
    } else {
        $dados = array(
            'idoperador'            => (int)$r['operador'],
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => (int)$r['bobina'],
            'largura'               => (float)str_replace(',', '.', $r['largura']),
            'comprimento'           => ($r['comprimento'] != '' ? (float)str_replace(',', '.', $r['comprimento']) : NULL),
            'picote'                => (int)$r['qualidadePicote'],
            'novaBobina'            => (int)$r['qualidadeNovaBobina'],
            'impressao'             => (int)$r['qualidadeImpressao'],
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
            'largura'               => NULL,
            'comprimento'           => NULL,
            'picote'                => NULL,
            'novaBobina'            => NULL,
            'impressao'             => NULL,
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
            'largura'               => (float)str_replace(',', '.', $r['largura']),
            'comprimento'           => ($r['comprimento'] != '' ? (float)str_replace(',', '.', $r['comprimento']) : NULL),
            'picote'                => (int)$r['qualidadePicote'],
            'novaBobina'            => (int)$r['qualidadeNovaBobina'],
            'impressao'             => (int)$r['qualidadeImpressao'],
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
    $dadosOutraBobina = array_map('utf8_decode',$dadosOutraBobina);
    $objOutraBobina = new OrdemProducaoBobina();
    $statusOutraBobina = $objOutraBobina->cadastrar($dadosOutraBobina);
    $idoutraBobina = $sql->lastID;
    $dados['idordemProducaoBobina'] = $idoutraBobina;
    if (!$statusOutraBobina) {
        SqlServer::cancelarTransacao();
        if((int)$r['idrefileLiberacao'] > 0) {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx012e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx013e')));
        }
        die();
    }
}

if($r['idrefileLiberacao'] != '') {
    $status = $obj->editar($dados, 'idrefileLiberacao = ' . $r['idrefileLiberacao']);
    $id = (int)$r['idrefileLiberacao'];
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new RefileLiberacaoObservacao();
    $status = $objObs->removerPor('idrefileLiberacao = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idrefileLiberacao'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idrefileLiberacao' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs = array_map('utf8_decode',$dadosObs);

            $objObs = new RefileLiberacaoObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idrefileLiberacao'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idrefileLiberacao'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RSx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('RSx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idrefileLiberacao'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('RSx004e')));
    }
}