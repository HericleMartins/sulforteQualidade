<?php
include( "../inc/load.php");

SqlServer::abrirTransacao();

$r = getRequest($_POST);
$obj = new ExtrusoraPresetup();

if($r['idextrusoraPresetup'] != ''){
    //edição
    $id = (int)$r['idextrusoraPresetup'];
    $status = $obj->editar(array('semVistoria' => $r['semVistoria'],'observacao' => ($r['observacao'] ? utf8_decode($r['observacao']) : NULL)), 'idextrusoraPresetup = ' . $r['idextrusoraPresetup']);
} else {
    //cadastro
    $dados = array(
        'idmaquina'       => (int)$r['idmaquina'],
        'idordemProducao' => (int)$r['idordemProducao'],
        'observacao'      => utf8_decode($r['observacao']),
        'semVistoria'     => $r['semVistoria'],
        'idusuario'       => $_SESSION[SESSAO_SISTEMA]['idusuario'],
        'dataCriacao'     => getData()
    );

    $status = $obj->cadastrar($dados);

    $id = $sql->lastID;
}

//verifica se deu tudo certo no cadastro/edição do pai
if ($status) {

    $objMateria = new ExtrusoraPresetupMateria();

    //remove todas as matérias
    $status = $objMateria->removerPor('idextrusoraPresetup = ' . $id);

    if(!$status){
        SqlServer::cancelarTransacao();
        if($r['idextrusoraPresetup'] != '') {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx008e')));
        } else {
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx004e')));
        }
        die();
    }

    if ($r['semVistoria'] == "0") {

        $arrayMp = json_decode($r['materiaPrima'], true);
        foreach ($arrayMp as $k => $v) {
            $mp = explode(" | ", $v['materia']);
            $dadosMateria = array(
                'idextrusoraPresetup' => $id,
                'idmateriaPrima' => $v['idmateria'],
                'lote' => utf8_decode($mp[1]),
                'quantidade' => (float)str_replace(',', '.', $mp[2]),
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );

            if ((float)str_replace(',', '.', $mp[2]) == 0) {
                SqlServer::cancelarTransacao();
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx012e')));
                die();
            }

            //salva todas como novas

            $statusMateria = $objMateria->cadastrar($dadosMateria);

            if (!$statusMateria) {
                SqlServer::cancelarTransacao();
                if($r['idextrusoraPresetup'] != '') {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx009e')));
                } else {
                    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx002e')));
                }
                die();
            }
        }
    }
    SqlServer::confirmarTransacao();
    if($r['idextrusoraPresetup'] != '') {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('EPx010s')));
    } else {
        echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('EPx001s')));
    }
} else {
    SqlServer::cancelarTransacao();
    if($r['idextrusoraPresetup'] != '') {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx011e')));
    } else {
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('EPx003e')));
    }
}