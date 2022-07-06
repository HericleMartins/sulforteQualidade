<?php
include( "../inc/load.php");

$r = getRequest($_POST);

if ((float)str_replace(',', '.', $r['espessuraMax']) < (float)str_replace(',', '.', $r['espessura'])) {
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx013e')));
    die();
}

$idextrusoraAnalise = (int)$r['idextrusoraAnalise'];

$dados = array(
    'idmaquina'             => (int)$r['idmaquina'],
    'idordemProducao'       => (int)$r['idordemProducao'],
    'idordemProducaoBobina' => (int)$r['bobina'],
    'largura'               => (isset($r['largura']) ? str_replace(',', '.', $r['largura']) : NULL),
    'tipoMedida'            => (isset($r['tipoMedida']) ? $r['tipoMedida'] : NULL),
    'espessuraMin'          => (($r['espessura'] != '') ? str_replace(',', '.', $r['espessura']) : NULL),
    'espessuraMax'          => (($r['espessuraMax'] != '') ? str_replace(',', '.', $r['espessuraMax']) : NULL),
    'espessuraLargura'      => (($r['espessuraLargura'] != '') ? str_replace(',', '.', $r['espessuraLargura']) : NULL),
    'espessuraComprimento'  => (($r['espessuraComprimento'] != '') ? str_replace(',', '.', $r['espessuraComprimento']) : NULL),
    'espessuraPeso'         => (($r['espessuraPeso'] != '') ? str_replace(',', '.', $r['espessuraPeso']) : NULL),
    'espessuraMedia'        => (($r['espessuraMedia'] != '') ? str_replace(',', '.', $r['espessuraMedia']) : NULL),
    'idoperador'            => (isset($r['operador']) ? (int)$r['operador'] : NULL),
    'obs'                   => utf8_decode($r['observacaoTexto']),
    'impressao'             => (isset($r['impressao']) ? (int)$r['impressao'] : NULL),
    'sanfonaEsq'            => (isset($r['sanfonaEsq']) ? str_replace(',', '.', $r['sanfonaEsq']) : NULL),
    'sanfonaDir'            => (isset($r['sanfonaDir']) ? str_replace(',', '.', $r['sanfonaDir']) : NULL),
    'idusuario'             => $_SESSION[SESSAO_SISTEMA]['idusuario'],
    'dataCriacao'           => getData(),
    'semAnalise'            => ($r['semAnalise'] == '1' ? 1 : NULL),
    'faceamento'            => (($r['faceamento'] != '') ? (int)$r['faceamento'] : NULL)
);

if($idextrusoraAnalise > 0){

    //edição
    unset($dados['idmaquina']);
    unset($dados['idordemProducao']);
    unset($dados['idusuario']);
    unset($dados['dataCriacao']);

}

SqlServer::abrirTransacao();

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
        $dadosOutraBobina = array_map('utf8_decode', $dadosOutraBobina);
        $objOutraBobina = new OrdemProducaoBobina();
        $statusOutraBobina = $objOutraBobina->cadastrar($dadosOutraBobina);
        $idoutraBobina = $sql->lastID;
        $dados['idordemProducaoBobina'] = $idoutraBobina;
        if (!$statusOutraBobina) {
            SqlServer::cancelarTransacao();
            if($idextrusoraAnalise > 0) {
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx008e')));
            } else {
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx002e')));
            }
            die();
        }
    }
}

$obj = new ExtrusoraAnalise();

if($idextrusoraAnalise > 0){
    $status = $obj->editar($dados, 'idextrusoraAnalise = ' . $idextrusoraAnalise);
    $id = $idextrusoraAnalise;
} else {
    $status = $obj->cadastrar($dados);
    $id = $sql->lastID;
}

if ($status) {
    $obs = $r['observacao'];

    $objObs = new ExtrusoraAnaliseObservacao();
    $status = $objObs->removerPor('idextrusoraAnalise = ' . $id);

    if (!$status) {
        SqlServer::cancelarTransacao();
        if($idextrusoraAnalise > 0) {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx009e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx003e')));
        }
        die();
    }

    if ($obs) {
        foreach ($obs as $k => $v) {
            $dadosObs = array(
                'idextrusoraAnalise' => $id,
                'idobservacao' => $v,
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );
            $dadosObs = array_map('utf8_decode', $dadosObs);

            $statusObs = $objObs->cadastrar($dadosObs);

            if (!$statusObs) {
                SqlServer::cancelarTransacao();
                if($idextrusoraAnalise > 0) {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx010e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx004e')));
                }
                die();
            }
        }
    }

    SqlServer::confirmarTransacao();
    if($idextrusoraAnalise > 0) {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('EAx011s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('EAx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($idextrusoraAnalise > 0) {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx012e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EAx005e')));
    }
}