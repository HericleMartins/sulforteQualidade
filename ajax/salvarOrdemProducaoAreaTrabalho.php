<?php
include( "../inc/load.php");

$post = getRequest($_POST);
$numero = (int)$post['numeroOrdemProducao'];
$idmaquina = (int)$post['idmaquina'];

if($numero > 0 && $idmaquina > 0){

    /* Verifica se existe a OP no BD */
    $arrayOrdem = OrdemProducao::carregarPor('numero = ' . $numero);

    if($arrayOrdem){

        /* Verifica se OP já não está inclusa na área de trabalho para a mesma máquina */
        $arrayArea = AreaTrabalho::listar('idmaquina = ' . $idmaquina . ' AND idordemProducao = ' . $arrayOrdem['idordemProducao'], 'idareaTrabalho');

        if($arrayArea){
            /* Já inserida para a mesma máquina */
            /*naoMostraPendencia = serve para evitar que esse erro apareça quando a OP estiver sendo inserida pela funcção de pendencias. */
            echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno('BBx002e'), 'naoMostraPendencia' => 1));
        } else {
            $dados = array(
                'idmaquina' => $idmaquina,
                'idordemProducao' => $arrayOrdem['idordemProducao'],
                'idusuario' => $_SESSION[SESSAO_SISTEMA]['idusuario'],
                'dataCriacao' => getData()
            );

            SqlServer::abrirTransacao();

            $objArea = new AreaTrabalho();
            $status = $objArea->cadastrar($dados);

            if ($status) {
                SqlServer::confirmarTransacao();
                echo retornarJsonEncode(array('status' => 1, 'msg' => montarMensagemRetorno('BBx001s')));
            } else {
                SqlServer::cancelarTransacao();
                /*flag = serve para identificar se a solicitação de inclusão da op foi feita pela modal de OP ou pela tela de pendencia.
                No caso de flag = true é pq foi feita através da lista de pendencia.
                */
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno((!$post['flag'] ? 'BBx003e' : 'BBx009e'))));
            }
        }
    } else {
        /* Não existe no BD */
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno((!$post['flag'] ? 'BBx004e' : 'BBx010e'))));
    }
} else {
    /* retorna erro avisando que não chegou número da OP*/
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno((!$post['flag'] ? 'BBx005e' : 'BBx011e'))));
}
