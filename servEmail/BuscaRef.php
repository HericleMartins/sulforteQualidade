<?php
//url do site
$url = 'https://www.sefaz.rs.gov.br/sat/REF-CON.aspx';

$dadosSite=file_get_contents($url);
//todo o conteudo
//print $dadosSite;


//$var1 = explode('<div id="painelConteudo">',$dadosSite);
//pegando o conteudo do sitecom a delimitação div id="painelConteudo
//var1 array 0 é a parte de cima var1 array 1 é a parte de baixo

//$var2 = explode('<div id="barraInferior">',$var1[1]);
//estou pegando a da var1 e delimitando ela e gravando 
//pegando somente a tabela

//$texto = $var2[0];
//gravando a parte que eu quero em uma variavel
$texto = $dadosSite;

//$nomeArquivo = date( "y-m-d" );
     
$nomeArquivo = '\\server1\sistema\wvenda'.date("Ymd").'.txt';

print $texto;
//mostrando a variavel para ver se funcionou
file_put_contents($nomeArquivo, $texto);
?>