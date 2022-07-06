<?php

//Define variaveis de sessão e nome do projeto
define('SESSAO_SISTEMA', 'sulforte');
define('NOME_SISTEMA', 'sulforte');

//Define variaveis de apresentações
define('TITULO_SISTEMA', 'Sulforte');
define('LOGO_SISTEMA', 'logo.png');
define('LOGO_SISTEMA_INTERNO', 'logo-interno.png');

//Monta o caminho do projeto
$caminho = explode('www/',$_SERVER["SCRIPT_FILENAME"]);
$link = explode('/', $caminho[1]);
$urlSistema = 'http://' . $_SERVER['SERVER_NAME'] . '/' . $link[0];

//Define o caminho absoluto do sistema
define('URL_SISTEMA', $urlSistema.'/'.NOME_SISTEMA);

//Define se envia e-mail e para quais endereços em caso de teste
define('AMBIENTE_TESTE', true);
define('EMAIL_DESTINATARIO_TESTE', '');

// Numero de registros por pagina
define('REGISTRO_POR_PAGINA', 50);

// Constantes que contem os ids dos grupos
define('USUARIO_ADMINISTRADOR', 1);

// Array que contem o alfabeto
$arrayAlfabeto = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U','V' ,'W', 'X', 'Y', 'Z');

$arrayCoresFracas = array('F8F8FF', 'F5F5F5', 'DCDCDC', 'FFFAF0', 'FDF5E6', 'FAF0E6', 'FAEBD7', 'FFEFD5', 'FFEBCD', 'FFE4C4', 'FFDAB9', 'FFDEAD', 'FFE4B5', 'FFF8DC', 'FFFFF0', 'FFFACD', 'FFF5EE', 'F0FFF0', 'F5FFFA', 'F0FFFF', 'F0F8FF', 'E6E6FA', 'FFF0F5', 'FFE4E1', 'FFFFFF');

$arrMaquina = array(
    1 => "Extrusora",
    2 => "Corte/Solda",
    3 => "Refile",
    4 => "Impressora"
);

$arrEtapaMaquina = array(
    1 => "Pré-setup",
    2 => "Setup",
    3 => "Análise",
    4 => "Liberação",
    5 => "Análise",
    6 => "Liberação",
    7 => "Análise",
    8 => "Liberação",
    9 => "Análise"
);

$arrEtapaMaquinaSelect = array(
    1 =>
        array(1 => array('', 1, "Pré-setup"),
              2 => array('', 2, "Setup"),
              3 => array('', 3, "Análise")
        ),
    2 =>
        array(4 => array('', 4, "Liberação"),
            5 => array('', 5, "Análise")
        ),
    3 =>
        array(6 => array('', 6, "Liberação"),
            7 => array('', 7, "Análise")
        ),
    4 =>
        array(8 => array('', 6, "Liberação"),
            9 => array('', 7, "Análise")
        )
);

$arrCortePista = array(
    1 => array('', 1, "Pista 1"),
    2 => array('', 2, "Pista 2")
);

$arrCorteQualidadeSolda = array(
    1 => array('', 1, "Bom"),
    2 => array('', 2, "Regular"),
    3 => array('', 3, "Ruim")
);

$arrCorteQualidadeSacaria = array(
    1 => array('', 1, "Bom"),
    2 => array('', 2, "Regular"),
    3 => array('', 3, "Ruim")
);

$arrCorteQualidadeImpressao = array(
    1 => array('', 1, "Bom"),
    2 => array('', 2, "Regular"),
    3 => array('', 3, "Ruim")
);

$arrRefileQualidadePicote = array(
    1 => array('', 1, "Bom"),
    2 => array('', 2, "Regular"),
    3 => array('', 3, "Ruim")
);

$arrRefileQualidadeNovaBobina = array(
    1 => array('', 1, "Bom"),
    2 => array('', 2, "Regular"),
    3 => array('', 3, "Ruim")
);

$arrRefileQualidadeImpressao = array(
    1 => array('', 1, "Bom"),
    2 => array('', 2, "Regular"),
    3 => array('', 3, "Ruim")
);

$arrExtrusaoTratamentoImpressao = array(
    1 => array('', 1, "Adequado"),
    2 => array('', 2, "Inadequado")
);

$arrExtrusaoFaceamento = array(
    1 => array('', 1, "Conforme"),
    2 => array('', 2, "Não conforme")
);

$arrImpressaoAnaliseVisual = array(
    1 => array('', 1, "OK"),
    2 => array('', 2, "Não OK")
);

$arrImpressaoTonalidade = array(
    1 => array('', 1, "OK"),
    2 => array('', 2, "Não OK")
);

$arrImpressaoConferenciaArte = array(
    1 => array('', 1, "OK"),
    2 => array('', 2, "Não OK")
);

$arrImpressaoLadoTratamento = array(
    1 => array('', 1, "Interno"),
    2 => array('', 2, "Externo")
);

$arrImpressaoLeituraCodigoBarras = array(
    1 => array('', 1, "OK"),
    2 => array('', 2, "Não OK")
);

$arrImpressaoTesteAderenciaTinta = array(
    1 => array('', 1, "OK"),
    2 => array('', 2, "Não OK")
);

$arrImpressaoSentidoDesbobinamento = array(
    1 => array('', 1, "Pé"),
    2 => array('', 2, "Cabeça")
);

$arrayEtapaAtiva = array(1, 2, 3, 4); //colocar nesse erray as etapas ativas

define('SEM_CONTROLE_SANFONA', ' 0,00 /  0,00');
define('COM_CONTROLE_IMPRESSAO', 'Sim');
$arrMPnaoPossuiLote = array('NÃO', 'NAO', 'N', '�');

//tempo em minutos para classificar a maquina como ociosa
define('DELAY_MAQUINA' , 30);
define('MENSAGEM_MAQUINA_OCIOSA', 'Máquina ociosa a mais de ' . DELAY_MAQUINA . ' minutos');

define('TEXTO_ALERTA_AVISO_CS', 'Descrição do item não confere com roteiro de produção');
define('TEXTO_ALERTA_AVISO_EXTRUSAO', 'Descrição do item não confere com roteiro de produção');
define('TEXTO_ALERTA_AVISO_REFILE', 'Descrição do item não confere com roteiro de produção');
define('TEXTO_ALERTA_AVISO_IMPRESSAO', 'Descrição do item não confere com roteiro de produção');
//mensagens de retorno
/* Legenda
 * [tipo da maquina ou alguma referencia do local do erro][etapa da maquina ou alguma referencia do local do erro][numero sequencial][s=sucesso/e=erro]
 * */
define('EPx001s', 'O registro de pré-setup foi cadastro com sucesso.');
define('EPx002e', 'Ops, ocorreu um erro ao realizar o cadastro do pré-setup.');
define('EPx003e', 'Ops, ocorreu um erro ao realizar o cadastro do pré-setup.');
define('EPx004e', 'Ops, ocorreu um erro ao realizar o cadastro do pré-setup.');
define('EPx005s', 'O registro de pré-setup foi removido com sucesso.');
define('EPx006e', 'Ops, ocorreu um erro ao remover o registro de pré-setup.');
define('EPx007s', 'O registro de pré-setup foi editado com sucesso.');
define('EPx008e', 'Ops, ocorreu um erro ao realizar a edição do pré-setup.');
define('EPx009e', 'Ops, ocorreu um erro ao realizar a edição do pré-setup.');
define('EPx010s', 'O registro de pré-setup foi editado com sucesso.');
define('EPx011e', 'Ops, ocorreu um erro ao realizar a edição do pré-setup.');
define('EPx012e', 'Quantidade de materia-prima deve ser superior a 0.');

define('ESx001s', 'O registro de setup foi cadastro com sucesso.');
define('ESx002e', 'Ops, ocorreu um erro ao realizar o cadastro do setup.');
define('ESx003e', 'Ops, ocorreu um erro ao realizar o cadastro do setup.');
define('ESx004e', 'Ops, ocorreu um erro ao realizar o cadastro do setup.');
define('ESx005e', 'Ops, ocorreu um erro ao realizar o cadastro do setup.');
define('ESx006s', 'O registro de setup foi removido com sucesso.');
define('ESx007e', 'Ops, ocorreu um erro ao remover o registro de setup.');
define('ESx008e', 'Ops, ocorreu um erro ao realizar a edição do setup.');
define('ESx009e', 'Ops, ocorreu um erro ao realizar a edição do setup.');
define('ESx010s', 'O registro de setup foi editado com sucesso.');
define('ESx011e', 'Ops, ocorreu um erro ao realizar a edição do setup.');
define('ESx012e', 'Espessura mínima não pode ser maior que espessura máxima.');

define('EAx001s', 'O registro de análise foi cadastro com sucesso.');
define('EAx002e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('EAx003e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('EAx004e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('EAx005e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('EAx006s', 'O registro de análise foi removido com sucesso.');
define('EAx007e', 'Ops, ocorreu um erro ao remover o registro de análise.');
define('EAx008e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('EAx009e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('EAx010e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('EAx011s', 'O registro de análise foi editado com sucesso.');
define('EAx012e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('EAx013e', 'Espessura mínima não pode ser maior que espessura máxima.');

define('BBx001s', 'Ordem de produção adicionada com sucesso.');
define('BBx002e', 'A ordem de produção já foi adicionada na máquina.');
define('BBx003e', 'Ops, ocorreu um erro ao adicionar ordem de produção.');
define('BBx004e', 'Ops, A ordem de produção informada não foi encontrada.');
define('BBx005e', 'Ops, ocorreu um erro ao adicionar ordem de produção. Certifique-se que o campo número da ordem de produção foi preenchido corretamente.');
define('BBx006s', 'Ordem de produção removida com sucesso.');
define('BBx007e', 'Ops, ocorreu um erro ao remover a ordem de produção.');
define('BBx008e', 'Ops, ocorreu um erro ao remover a ordem de produção.');
define('BBx009e', 'Ops, ocorreu um erro ao adicionar ordem de produção.');
define('BBx010e', 'Ops, A ordem de produção informada não foi encontrada.');
define('BBx011e', 'Ops, ocorreu um erro ao adicionar ordem de produção.');

define('CSx001s', 'O registro de liberação de produção foi cadastro com sucesso.');
define('CSx002e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('CSx003e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('CSx004e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('CSx005e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('CSx006s', 'O registro de liberação de produção foi removido com sucesso.');
define('CSx007e', 'Ops, ocorreu um erro ao remover o registro da liberação de produção.');
define('CSx008e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('CSx009e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('CSx010s', 'O registro de liberação de produção foi editado com sucesso.');
define('CSx011e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('CSx012e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('CSx013e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');

define('CAx001s', 'O registro de análise foi cadastro com sucesso.');
define('CAx002e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('CAx003e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('CAx004e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('CAx005e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('CAx006s', 'O registro de análise foi removido com sucesso.');
define('CAx007e', 'Ops, ocorreu um erro ao remover o registro da análise.');
define('CAx008e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('CAx009e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('CAx010s', 'O registro de análise foi editado com sucesso.');
define('CAx011e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('CAx012e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('CAx013e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');

define('RSx001s', 'O registro de liberação de produção foi cadastro com sucesso.');
define('RSx002e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('RSx003e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('RSx004e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('RSx005e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('RSx006s', 'O registro de liberação de produção foi removido com sucesso.');
define('RSx007e', 'Ops, ocorreu um erro ao remover o registro da liberação de produção.');
define('RSx008e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('RSx009e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('RSx010s', 'O registro de liberação de produção foi editado com sucesso.');
define('RSx011e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('RSx012e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('RSx013e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');

define('RAx001s', 'O registro de análise foi cadastro com sucesso.');
define('RAx002e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('RAx003e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('RAx004e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('RAx005e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('RAx006s', 'O registro de análise foi removido com sucesso.');
define('RAx007e', 'Ops, ocorreu um erro ao remover o registro da análise.');
define('RAx008e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('RAx009e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('RAx010s', 'O registro de análise foi editado com sucesso.');
define('RAx011e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('RAx012e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('RAx013e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');

define('OPx001e', 'Ops, o código ERP deve ser numérico.');
define('OPx002e', 'Ops, código ERP já cadastrado para outro operador.');
define('OPx003e', 'Ops, código ERP já cadastrado para outro operador.');
define('OPx004e', 'Ops, ocorreu um erro ao realizar a edição do operador.');
define('OPx005e', 'Ops, ocorreu um erro ao realizar o cadastro do operador.');
define('OPx006s', 'O operador foi editado com sucesso.');
define('OPx007s', 'O operador foi cadastrado com sucesso.');
define('OPx008s', 'O operador foi removido com sucesso.');
define('OPx009e', 'Ops, operador não poderão ser removido, pois já estão em uso.');
define('OPx010s', 'A situação do operador foi alterada com sucesso.');
define('OPx011e', 'Ops, ocorreu um erro ao mudar a situação do operador.');

define('OBx001e', 'Ops, ocorreu um erro ao realizar a edição da observação.');
define('OBx002e', 'Ops, ocorreu um erro ao realizar o cadastro da observação.');
define('OBx003s', 'A observação foi editada com sucesso.');
define('OBx004s', 'A observação foi cadastrada com sucesso.');
define('OBx005s', 'A observação foi removida com sucesso.');
define('OBx006e', 'Ops, observação não poderão ser removida, pois já estão em uso.');
define('OBx007s', 'A situação da observação foi alterada com sucesso.');
define('OBx008e', 'Ops, ocorreu um erro ao mudar a situação da observação.');

define('UUx001s', 'O usúario foi cadastrado com sucesso.');
define('UUx002e', 'Ops, ocorreu um erro ao cadastrar o usúario.');
define('UUx003e', 'Ops, a senha e confirmação de senha estão diferentes.');
define('UUx004e', 'Ops, a senha deve ser informada.');
define('UUx005s', 'O usúario foi editado com sucesso.');
define('UUx006e', 'Ops, ocorreu um erro ao editar o usúario.');
define('UUx007s', 'A situação do usúario foi alterada com sucesso.');
define('UUx008e', 'Ops, ocorreu um erro ao mudar a situação do usúario.');
define('UUx009e', 'Ops, informe a senha atual.');
define('UUx010e', 'Ops, a senha atual informada é inválida.');
define('UUx011e', 'Ops, ocorreu um erro ao editar o usúario.');
define('UUx012s', 'O usúario foi editado com sucesso.');
define('UUx013e', 'Ops, a senha e confirmação de senha estãoo diferentes.');

define('IAx001s', 'O registro de análise foi cadastro com sucesso.');
define('IAx002e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('IAx003e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('IAx004e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('IAx005e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');
define('IAx006s', 'O registro de análise foi removido com sucesso.');
define('IAx007e', 'Ops, ocorreu um erro ao remover o registro da análise.');
define('IAx008e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('IAx009e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('IAx010s', 'O registro de análise foi editado com sucesso.');
define('IAx011e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('IAx012e', 'Ops, ocorreu um erro ao realizar a edição da análise.');
define('IAx013e', 'Ops, ocorreu um erro ao realizar o cadastro da análise.');

define('ILx001s', 'O registro de liberação de produção foi cadastro com sucesso.');
define('ILx002e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('ILx003e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('ILx004e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('ILx005e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');
define('ILx006s', 'O registro de liberação de produção foi removido com sucesso.');
define('ILx007e', 'Ops, ocorreu um erro ao remover o registro da liberação de produção.');
define('ILx008e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('ILx009e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('ILx010s', 'O registro de liberação de produção foi editado com sucesso.');
define('ILx011e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('ILx012e', 'Ops, ocorreu um erro ao realizar a edição da liberação de produção.');
define('ILx013e', 'Ops, ocorreu um erro ao realizar o cadastro da liberação de produção.');

define('PCx001e', 'Ops, ocorreu um erro ao realizar a edição da palavra chave.');
define('PCx002e', 'Ops, ocorreu um erro ao realizar o cadastro da palavra chave.');
define('PCx003s', 'A palavra chave foi editada com sucesso.');
define('PCx004s', 'A palavra chave foi cadastrada com sucesso.');
define('PCx005s', 'A palavra chave foi removida com sucesso.');
define('PCx006e', 'Ops, palavra chave não poderão ser removida.');
define('PCx007s', 'A situação da palavra chave foi alterada com sucesso.');
define('PCx008e', 'Ops, ocorreu um erro ao mudar a situação da palavra chave.');
define('PCx009e', 'A senha informada é inválida.');

?>