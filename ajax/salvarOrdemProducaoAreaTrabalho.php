<?php
include( "../inc/load.php");

$post = getRequest($_POST);
$numero = (int)$post['numeroOrdemProducao'];
$idmaquina = (int)$post['idmaquina'];

if($numero > 0 && $idmaquina > 0){

    /* Verifica se existe a OP no BD */
    $arrayOrdem = OrdemProducao::carregarPor('numero = ' . $numero);

    if($arrayOrdem){

        /* Verifica se OP j� n�o est� inclusa na �rea de trabalho para a mesma m�quina */
        $arrayArea = AreaTrabalho::listar('idmaquina = ' . $idmaquina . ' AND idordemProducao = ' . $arrayOrdem['idordemProducao'], 'idareaTrabalho');

        if($arrayArea){
            /* J� inserida para a mesma m�quina */
            /*naoMostraPendencia = serve para evitar que esse erro apare�a quando a OP estiver sendo inserida pela func��o de pendencias. */
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
                /*flag = serve para identificar se a solicita��o de inclus�o da op foi feita pela modal de OP ou pela tela de pendencia.
                No caso de flag = true � pq foi feita atrav�s da lista de pendencia.
                */
                echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno((!$post['flag'] ? 'BBx003e' : 'BBx009e'))));
            }
        }
    } else {
        /* N�o existe no BD */
        echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno((!$post['flag'] ? 'BBx004e' : 'BBx010e'))));
    }
} else {
    /* retorna erro avisando que n�o chegou n�mero da OP*/
    echo retornarJsonEncode(array('status' => 0, 'msg' => montarMensagemRetorno((!$post['flag'] ? 'BBx005e' : 'BBx011e'))));
}
