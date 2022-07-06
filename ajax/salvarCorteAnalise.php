<?php
include( "../inc/load.php");

$r = getRequest($_POST);
$obj = new CorteAnalise();

if($r['idcorteAnalise'] != '') {

    $dados = array(
        'idoperador'            => (isset($r['operador']) ? (int)$r['operador'] : NULL),
        'idordemProducaoBobina' => (int)$r['bobina'],
        'pista'                 => (isset($r['pista']) ? (int)$r['pista'] : NULL),
        'largura'               => (isset($r['largura']) ? (float)str_replace(',', '.', $r['largura']) : NULL),
        'comprimento'           => (isset($r['comprimento']) ? (float)str_replace(',', '.', $r['comprimento']) : NULL),
        'sanfonaEsq'            => (isset($r['sanfonaEsq']) ? str_replace(',', '.', $r['sanfonaEsq']) : NULL),
        'sanfonaDir'            => (isset($r['sanfonaDir']) ? str_replace(',', '.', $r['sanfonaDir']) : NULL),
        'solda'                 => (isset($r['qualidadeSolda']) ? (int)$r['qualidadeSolda'] : NULL),
        'sacaria'               => (isset($r['qualidadeSacaria']) ? (int)$r['qualidadeSacaria'] : NULL),
        'idcodigoFaca'          => (isset($r['codigoFaca']) ? (int)$r['codigoFaca'] : NULL),
        'impressao'             => (isset($r['qualidadeImpressao']) ? (int)$r['qualidadeImpressao'] : NULL),
        'obs'                   => utf8_decode($r['observacaoTexto']),
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
        'pista'                 => (isset($r['pista']) ? (int)$r['pista'] : NULL),
        'largura'               => (isset($r['largura']) ? (float)str_replace(',', '.', $r['largura']) : NULL),
        'comprimento'           => (isset($r['comprimento']) ? (float)str_replace(',', '.', $r['comprimento']) : NULL),
        'sanfonaEsq'            => (isset($r['sanfonaEsq']) ? str_replace(',', '.', $r['sanfonaEsq']) : NULL),
        'sanfonaDir'            => (isset($r['sanfonaDir']) ? str_replace(',', '.', $r['sanfonaDir']) : NULL),
        'solda'                 => (isset($r['qualidadeSolda']) ? (int)$r['qualidadeSolda'] : NULL),
        'sacaria'               => (isset($r['qualidadeSacaria']) ? (int)$r['qualidadeSacaria'] : NULL),
        'idcodigoFaca'          => (isset($r['codigoFaca']) ? (int)$r['codigoFaca'] : NULL),
        'impressao'             => (isset($r['qualidadeImpressao']) ? (int)$r['qualidadeImpressao'] : NULL),
        'obs'                   => utf8_decode($r['observacaoTexto']),
        'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
        'dataCriacao'           => getData(),
        'semAnalise'            => ($r['semAnalise'] == '1' ? 1 : NULL),
        'reinspecao'            => ($r['reinspecao'] == '1' ? 1 : NULL)
    );
}

SqlServer::abrirTransacao();

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
        if((int)$r['idcorteAnalise'] > 0) {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx012e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx013e')));
        }
        die();
    }
}

if($r['idcorteAnalise'] != '') {
    $status = $obj->editar($dados, 'idcorteAnalise = ' . $r['idcorteAnalise']);
    $id = (int)$r['idcorteAnalise'];
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {

    $obs = $r['observacao'];

    $objObs = new CorteAnaliseObservacao();
    $status = $objObs->removerPor('idcorteAnalise = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($r['idcorteAnalise'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx002e')));
        }
        die();
    }

    if ($obs){
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idcorteAnalise' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs = array_map('utf8_decode', $dadosObs);

            $objObs = new CorteAnaliseObservacao();

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($r['idcorteAnalise'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx003e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($r['idcorteAnalise'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('CAx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('CAx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idcorteAnalise'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('CAx004e')));
    }
}