<?php
include( "../inc/TemplatePower/class.TemplatePower.inc.php");
include( "../inc/sqlserver.php");
include( "../inc/funcoes.php");
include( "../inc/constantes.php");
include( "../inc/log.php");

//verifica se tem arquivo na pasta
$directory = '../ordemProducao/';
$extension = '[tT][xX][tT]';

if ( file_exists($directory) ) {
    foreach ( glob($directory . '*.' . $extension) as $file ) {
        $fileName = end(explode("/",strtolower($file)));
        $objOP = new OrdemProducao();
        //confirma se é um arquivo com o nome padrão de OP
        if ((substr($fileName, 0, 3) == 'pcp') || (substr($fileName, 0, 2) == 'mp')) {

            $handle = fopen($file, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    $objOP->tratarDadosImportados($line);
                }
                fclose($handle);
            }
            SqlServer::abrirTransacao();

            if(substr($fileName, 0, 3) == 'pcp') {

                $status = $objOP->importarDados();

            } else if(substr($fileName, 0, 2) == 'mp') {

                $status = $objOP->importarMateriaPrima();

            }

            $objLog = new LogImportacao();

            if($status){

                $statusCommit = SqlServer::confirmarTransacao();

                if($statusCommit) {
                    $arrayArquivo = explode('/', $file);
                    $arquivo = end($arrayArquivo);
                    if (copy($directory . $arquivo, $directory . 'importada/' . $arquivo)) {
                        unlink($directory . $arquivo);
                    }

                    SqlServer::abrirTransacao();
                    $objLog->salvarLog($arquivo, 1);
                    SqlServer::confirmarTransacao();
                }

            } else {
                SqlServer::cancelarTransacao();

                SqlServer::abrirTransacao();
                $objLog->salvarLog($arquivo, 2);
                SqlServer::confirmarTransacao();
            }
        }
    }
}
else {
    echo 'Diretório inválido';
}
