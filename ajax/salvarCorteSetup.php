<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new CorteLiberacao();

SqlServer::abrirTransacao();

if($r['idcorteLiberacao'] != '') {

    //edi��o
    if($r['semVistoria'] == 1){
        $dados = array(
            'idoperador'            => NULL,
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => NULL,
            'pista'                 => NULL,
            'largura'               => NULL,
            'comprimento'           => NULL,
            'sanfonaEsq'            => NULL,
            'sanfonaDir'            => NULL,
            'solda'                 => NULL,
            'sacaria'               => NULL,
            'idcodigoFaca'          => NULL,
            'impressao'             => NULL,
            'obs'                   => $r['observacaoTexto'],
            'reinspecao'            => NULL
        );
    } else {
        $dados = array(
            'idoperador'            => (int)$r['operador'],
            'semVistoria'           => $r['semVistoria'],
            'idordemProducaoBobina' => (int)$r['bobina'],
            'pista'                 => (int)$r['pista'],
            'largura'               => (float)str_replace(',', '.', $r['largura']),
            'comprimento'           => (float)str_replace(',', '.', $r['comprimento']),
            'sanfonaEsq'            => ($r['sanfonaEsq'] ? str_replace(',', '.', $r['sanfonaEsq']) : NULL),
            'sanfonaDir'            => ($r['sanfonaDir'] ? str_replace(',', '.', $r['sanfonaDir']) : NULL),
            'solda'                 => (int)$r['qualidadeSolda'],
            'sacaria'               => (int)$r['qualidadeSacaria'],
            'idcodigoFaca'          => (int)$r['codigoFaca'],
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
            'pista'                 => NULL,
            'largura'               => NULL,
            'comprimento'           => NULL,
            'solda'                 => NULL,
            'sacaria'               => NULL,
            'idcodigoFaca'          => NULL,
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
            'pista'                 => (int)$r['pista'],
            'largura'               => (float)str_replace(',', '.', $r['largura']),
            'comprimento'           => (float)str_replace(',', '.', $r['comprimento']),
            'solda'                 => (int)$r['qualidadeSolda'],
            'sacaria'               => (int)$r['qualidadeSacaria'],
            'idcodigoFaca'          => (int)$r['codigoFaca'],
            'impressao'             => (int)$r['qualidadeImpressao'],
            'obs'                   => $r['observacaoTexto'],
            'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
            'dataCriacao'           => getData(),
            'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
        );
    }

    $dados['sanfonaEsq'] = ($r['sanfonaEsq'] ? str_replace(',', '.', $r['sanfonaEsq']) : NULL);
    $dados['sanfonaDir'] = ($r['sanfonaDir'] ? str_replace(',', '.', $r['sanfonaDir']) : NULL);
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
        if((int)$r['idcorteLiberacao'] > 0) {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx012e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx013e')));
        }
        die();
    }
}

if($r['idcorteLiberacao'] != '') {
    $status = $obj->editar($dados, 'idcorteLiberacao = ' . $r['idcorteLiberacao']);
    $id = (int)$r['idcorteLiberacao'];
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new CorteLiberacaoObservacao();
    $status = $objObs->removerPor('idcorteLiberacao = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idcorteLiberacao'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idcorteLiberacao' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs = array_map('utf8_decode', $dadosObs);

            $objObs = new CorteLiberacaoObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idcorteLiberacao'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idcorteLiberacao'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('CSx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('CSx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idcorteLiberacao'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CSx004e')));
    }
}