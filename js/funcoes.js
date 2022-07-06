function _alert(tipo, msg, reset, reload) {
    /*
     * tipo: 1 = sucesso; tipo: 2 = erro; tipo: 3 = alerta
     * msg : mensagem de retorno
     * reset : parametro para resetar o formulario, informar o id do formulario que será resetado
     * reload : atualizar página
    * */

    var titulo = false;
    var classe = false;
    var icone = false;

    if (tipo == 1) {
        titulo = 'Confirmação';
        classe = 'bootbox-confirmacao text-success';
        icone = 'fa-check-circle';

    } else if (tipo == 2) {
        titulo = 'Erro';
        classe = 'bootbox-erro text-danger';
        icone = 'fa-exclamation-circle';
    } else {
        titulo = 'Aviso';
        classe = 'bootbox-aviso text-warning';
        icone = 'fa-exclamation-triangle';
    }

    bootbox.alert({
        message: msg,
        title: "<i class='fa " + icone + "' aria-hidden='true'></i><b> " + titulo + "</b>",
        className: classe + ' bootbox-geral',
        size: 'small',
        callback: function () {
            (reload ? location.reload() : false);
        }
    });

    (reset ? $(reset)[0].reset() : false);

}

/*
 * Função responsavel por gerar o select
 * param1 -> pagina onde vai retornar o xml com os registros
 * param2 -> nome do select
 * param4 -> id selecionado, caso já tenha algum id selecionado
 */
function montarSelect(pagina, select, idselecionado) {

    var dfd = new $.Deferred();

    //$("<img src='../img/loading.gif' class='loading-combo' id='loading_"+select+"' alt='carregando'/>").insertAfter("select#"+select);
    // funcao para carregar 1 de cada vez. Para nao ter o problema de carregar
    // tudo de uma vez sá, e depois dar pau para carregar o form
    $.post("../ajax/" + pagina + ".php", {

    }, function (response) {
        $("#" + select + " option").remove();
        $("select#" + select).append('<option value="">Selecione..</option>');

        $(response).find("registro").each(function () {
            $("select#" + select).append(
                '<option value=' + $(this).find('id').text() +
                ' ' + ($(this).find('id').text() == idselecionado ? 'selected="selected"' : ' ') +
                '>' + $(this).find('nome').text() +
                '</option>'
            );
        });
        $("#loading_" + select).remove();
        dfd.resolve(response);
    })

    return dfd.promise();
}

/*
 * Função responsavel por gerar o select filho
 * Ex: Select de cidades que depende do select de estados, para carregar somente as cidades do estado X
 * param1 -> pagina onde vai retornar o xml com os registros
 * param2 -> nome do select
 * param3 -> idpai, onde vai pegar o id dependente
 * param4 -> id selecionado, caso já tenha algum id selecionado
 */
function montarSelectFilho(pagina, select, idpai, idselecionado) {

    var dfd = new $.Deferred();

    if ($("#" + idpai).val() == 0) {
        dfd.resolve();
        return dfd.promise();
    }

    //$("<img src='../img/loading.gif' class='loading-combo' id='loading_"+select+"' alt='carregando'/>").insertAfter("select#"+select);
    // funcao para carregar 1 de cada vez. Para nao ter o problema de carregar
    // tudo de uma vez sá, e depois dar pau para carregar o form
    $.post("../ajax/" + pagina + ".php", {
        idpai: idpai
    }, function (response) {
        $("#" + select + " option").remove();
        $("select#" + select).append('<option value="">Selecione..</option>');

        var attr;

        $(response).find("registro").each(function () {

            if ($(this).find('attr').text()) {
                attr = $(this).find('attr').text();
            } else {
                attr = '';
            }

            if ($(this).find('divisor').text() != '') {
                $("select#" + select).append('<option data-divider="true"></option>');
            } else {
                $("select#" + select).append(
                    '<option ' + attr + ' value=' + $(this).find('id').text() +
                    ' ' + ($(this).find('id').text() == idselecionado ? 'selected="selected"' : ' ') +
                    '>' + $(this).find('nome').text() +
                    '</option>'
                );
            }
        });
        $("#loading_" + select).remove();

        dfd.resolve(response);
    })

    return dfd.promise();
}

function carregarOrdemProducao(idordemProducao, extrusoraPresetup) {

    var dfd = $.Deferred();
    var retorno;
    if (typeof idextrusoraPresetup == 'undefined') {
        extrusoraPresetup = '';
    }

    $.ajax({
        type: "POST",
        url: '../ajax/carregarOrdemProducao.php',
        data: {
            idordemProducao: idordemProducao,
            idextrusoraPresetup: extrusoraPresetup
        },
        success: function (r) {
            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function verificarExisteRegistroMateriaPrima() {
    if ($('.table-presetup-materia tbody tr').length > 0) {
        $('#formExtrusoraPresetup .btn-success').prop("disabled", false);
        $('#formExtrusoraPresetup .semVistoria').prop("disabled", true);
    } else {
        $('#formExtrusoraPresetup .btn-success').prop("disabled", true);
        $('#formExtrusoraPresetup .semVistoria').prop("disabled", false);
    }
}

function carregarExtrusoraPresetup(idextrusoraPresetup) {
    var dfd = $.Deferred();
    var retorno;
    var modal = '#modalCadastrarExtrusoraPresetup';
    $.ajax({
        type: "POST",
        url: '../ajax/carregarExtrusoraPresetup.php',
        data: {
            idextrusoraPresetup: idextrusoraPresetup
        },
        success: function (r) {
            if (idextrusoraPresetup) {
                r = $.parseJSON(r);
                $(modal + ' #observacao').val(r.observacao);
            }
            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function montarExtrusoraPresetup(idordemProducao, idmaquina, idextrusoraPresetup, acao) {
    waitingDialog.show();
    var modal = '#modalCadastrarExtrusoraPresetup';
    $.when(carregarOrdemProducao(idordemProducao, idextrusoraPresetup), montarSelectFilho('carregarMateriaPrima', 'materiaPrima', idordemProducao, false)).then(
        function (r) {
            r = $.parseJSON(r);
            $(modal + ' .carregar-cliente').html(r.cliente);
            $(modal + ' .carregar-item').html(r.item);
            $(modal + ' .carregar-medidas').html(r.medidas);
            $(modal + ' .carregar-larguraBalao').html(r.larguraBalao + ' cm');
            $(modal + ' .carregar-pesoExtrusao').html(r.pesoExtrusao + ' Kg');
            $(modal + ' .carregar-mp').html(r.detalhe);
            $(modal + ' #carregar-idordemProducao').val(idordemProducao);
            $(modal + ' #carregar-idmaquina').val(idmaquina);
            $(modal + ' #idextrusoraPresetup').val(idextrusoraPresetup);
            materias = $.parseJSON(r.materias);
            $.each(materias, function (i, val) {

                $('.table-presetup-materia > tbody:last-child').append(
                    ''
                    + '<tr id="mp-linha-' + val.idextrusoraPresetupMateria + '">'
                    + '<td mpid="' + val.idmateriaPrima + '"><i class="fa fa-check text-success" aria-hidden="true"></i> ' + val.materiaPrima + ' | ' + val.lote + ' | ' + val.quantidade.replace('.', ',') + '</td>'
                    + '<td align="center"><button type="button" class="btn btn-danger m-top-xs-5 remove-presetup-materia" title="Excluir" onclick="excluirPresetupMateria(' + val.idextrusoraPresetupMateria + ');"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
                    + '</tr>'
                );
            });
        }
    ).done(
        function () {
            $.when(carregarExtrusoraPresetup(idextrusoraPresetup)).then(
                function () {
                    $(modal).modal();
                    if (acao != 'editar') {
                        $('.table-presetup-materia tbody').html('');
                    } else {
                        $(modal + ' #salvarPresetup').html('Salvar');
                    }
                    verificarExisteRegistroMateriaPrima();
                    waitingDialog.hide()
                }
            );
        }
    );
}

function montarExtrusoraSetup(idordemProducao, idmaquina, idextrusoraSetup, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarExtrusoraSetup' : '#modalCadastrarExtrusoraSetup');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 1, false),
        montarSelectFilho('carregarOperador', 'operador', 1, false),
        montarSelect('carregarTratamentoImpressao', 'impressao', false),
        montarSelect('carregarFaceamento', 'faceamento', false)
    ).then(
        function (r) {
            r = $.parseJSON(r);
            $(modal + ' .carregar-cliente').html(r.cliente);
            $(modal + ' #carregar-idordemProducao').val(idordemProducao);
            $(modal + ' #carregar-idmaquina').val(idmaquina);
            $(modal + ' .carregar-item').html(r.item);
            $(modal + ' .carregar-medidas').html(r.medidas);
            $(modal + ' .carregar-larguraBalao').html(r.larguraBalao + ' cm');
            $(modal + ' .carregar-pesoExtrusao').html(r.pesoExtrusao + ' Kg');

            if (r.sanfona == '2' && $(modal + ' #labelSanfona').find("span").length == 0) {
                $(modal + ' #sanfonaEsq').prop('disabled', true);
                $(modal + ' #sanfonaDir').prop('disabled', true);
                $(modal + ' #labelSanfona').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
            }

            if (r.impresso == '2' && $(modal + ' #labelImpressao').find("span").length == 0) {
                $(modal + ' #impressao').prop('disabled', true);
                $(modal + ' #labelImpressao').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
            }
        }
    ).done(
        function () {
            $.when(carregarDadosSetup(idextrusoraSetup, acao)).then(
                function () {
                    $(modal).modal();
                    waitingDialog.hide()
                }
            );
        }
    );
}

function montarCorteAnalise(idordemProducao, idmaquina, idcorteAnalise, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarCorteAnalise' : '#modalCadastrarCorteAnalise');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 2, false),
        montarSelectFilho('carregarOperador', 'operador', 2, false),
        montarSelect('carregarCorteQualidadeSolda', 'qualidadeSolda', false),
        montarSelect('carregarCorteQualidadeSacaria', 'qualidadeSacaria', false),
        montarSelect('carregarCodigoFaca', 'codigoFaca', false),
        montarSelect('carregarCorteQualidadeImpressao', 'qualidadeImpressao', false),
        montarSelect('carregarCortePista', 'pista', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false))
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);
                if ($(modal + ' #bobina' + ' option').size() <= 1) {
                    $('.corteAnalise-outra-bobina').show();
                } else {
                    $('.corteAnalise-outra-bobina').hide();
                }

                if (r.sanfona == '2' && $(modal + ' #labelSanfona').find("span").length == 0) {
                    $(modal + ' #sanfonaEsq').prop('disabled', true);
                    $(modal + ' #sanfonaDir').prop('disabled', true);
                    $(modal + ' #labelSanfona').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
                }
            }
        ).done(
            function () {
                $.when(carregarDadosCorteAnalise(idcorteAnalise, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}

function montarExtrusoraAnalise(idordemProducao, idmaquina, idextrusoraAnalise, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarExtrusoraAnalise' : '#modalCadastrarExtrusoraAnalise');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 1, false),
        montarSelectFilho('carregarOperador', 'operador', 1, false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false),
        montarSelect('carregarTratamentoImpressao', 'impressao', false),
        montarSelect('carregarFaceamento', 'faceamento', false))
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);

                $('#formExtrusoraAnalise #bobinaValorOutro').prop('disabled', true).hide();
                $('#formExtrusoraAnalise #bobina').selectpicker('show');

                if (r.sanfona == '2' && $(modal + ' #labelSanfona').find("span").length == 0) {
                    $(modal + ' #sanfonaEsq').prop('disabled', true);
                    $(modal + ' #sanfonaDir').prop('disabled', true);
                    $(modal + ' #labelSanfona').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
                }

                if (r.impresso == '2' && $(modal + ' #labelImpressao').find("span").length == 0) {
                    $(modal + ' #impressao').prop('disabled', true);
                    $(modal + ' #labelImpressao').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
                }
            }
        ).done(
            function () {
                $.when(carregarDadosAnalise(idextrusoraAnalise, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}

function carregarDadosSetup(idextrusoraSetup, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarExtrusoraSetup.php',
        data: {
            idextrusoraSetup: idextrusoraSetup
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosSetup(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function carregarDadosCorteAnalise(idcorteAnalise, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarCorteAnalise.php',
        data: {
            idcorteAnalise: idcorteAnalise
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosCorteAnalise(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function carregarDadosAnalise(idextrusoraAnalise, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarExtrusoraAnalise.php',
        data: {
            idextrusoraAnalise: idextrusoraAnalise
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosAnalise(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function visualizarOrdemProducao(idordemProducao, idmaquina, maquina) {
    waitingDialog.show();
    window.location.href = 'visualizarOrdemProducao.php?idordemProducao=' + idordemProducao + '&idmaquina=' + idmaquina + '&maquina=' + maquina;
}

function editarDetalhe(id, tipo, idordemProducao, idmaquina) {
    if (tipo == 1) {
        montarExtrusoraSetup(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 2) {
        montarExtrusoraAnalise(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 3) {
        montarExtrusoraPresetup(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 4) {
        montarCorteLiberacao(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 5) {
        montarCorteAnalise(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 6) {
        montarRefileLiberacao(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 7) {
        montarRefileAnalise(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 8) {
        montarImpressoraLiberacao(idordemProducao, idmaquina, id, 'editar');
    } else if (tipo == 9) {
        montarImpressoraAnalise(idordemProducao, idmaquina, id, 'editar');
    }
}

function visualizarDetalhe(id, tipo, idordemProducao, idmaquina) {
    if (tipo == 1) {
        montarExtrusoraSetup(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 2) {
        montarExtrusoraAnalise(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 4) {
        montarCorteLiberacao(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 5) {
        montarCorteAnalise(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 6) {
        montarRefileLiberacao(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 7) {
        montarRefileAnalise(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 8) {
        montarImpressoraLiberacao(idordemProducao, idmaquina, id, 'visualizar');
    } else if (tipo == 9) {
        montarImpressoraAnalise(idordemProducao, idmaquina, id, 'visualizar');
    }
}

function montarCorteLiberacao(idordemProducao, idmaquina, idcorteLiberacao, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarCorteLiberacao' : '#modalCadastrarCorteLiberacao');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 2, false),
        montarSelectFilho('carregarOperador', 'operador', 2, false),
        montarSelect('carregarCorteQualidadeSolda', 'qualidadeSolda', false),
        montarSelect('carregarCorteQualidadeSacaria', 'qualidadeSacaria', false),
        montarSelect('carregarCodigoFaca', 'codigoFaca', false),
        montarSelect('carregarCorteQualidadeImpressao', 'qualidadeImpressao', false),
        montarSelect('carregarCortePista', 'pista', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false)
    ).then(
        function (r) {
            r = $.parseJSON(r);
            $(modal + ' .carregar-cliente').html(r.cliente);
            $(modal + ' #carregar-idordemProducao').val(idordemProducao);
            $(modal + ' #carregar-idmaquina').val(idmaquina);
            $(modal + ' .carregar-item').html(r.item);
            $(modal + ' .carregar-medidas').html(r.medidas);
            $(modal + ' .carregar-larguraBalao').html(r.larguraBalao + ' cm');
            $(modal + ' .carregar-pesoExtrusao').html(r.pesoExtrusao + ' Kg');

            if (r.sanfona == '2' && $(modal + ' #labelSanfona').find("span").length == 0) {
                $(modal + ' #sanfonaEsq').prop('disabled', true);
                $(modal + ' #sanfonaDir').prop('disabled', true);
                $(modal + ' #labelSanfona').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
            }
        }
    ).done(
        function () {
            $.when(carregarDadosCorteLiberacao(idcorteLiberacao, acao)).then(
                function () {
                    $(modal).modal();
                    waitingDialog.hide()
                }
            );
        }
    );
}

function carregarDadosCorteLiberacao(idcorteLiberacao, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarCorteLiberacao.php',
        data: {
            idcorteLiberacao: idcorteLiberacao
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosCorteLiberacao(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function populaDadosCorteLiberacao(dados, acao) {

    var obj = JSON.parse(dados);

    if (acao == 'editar') {

        var modal = '#modalCadastrarCorteLiberacao';

        $(modal + " #salvarSetup").html('Salvar');

        $(modal + " input[name='largura']").val(obj.largura.replace('.', ','));
        $(modal + " input[name='comprimento']").val(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadeSolda").val(obj.solda);
        $(modal + " #qualidadeSacaria").val(obj.sacaria);
        $(modal + " #codigoFaca").val(obj.idcodigoFaca);
        $(modal + " #qualidadeImpressao").val(obj.impressao);
        $(modal + " #pista").val(obj.pista);

        if (obj.reinspecao == 1) {
            $(modal + " #reinspecao").prop('checked', true);
        } else {
            $(modal + " #reinspecao").prop('checked', false);
        }

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " input[name='sanfonaEsq']").val(obj.sanfonaEsq.replace('.', ','));
        $(modal + " input[name='sanfonaDir']").val(obj.sanfonaDir.replace('.', ','));

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idcorteLiberacao").val(obj.idcorteLiberacao);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];

        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {
        var modal = '#modalVisualizarCorteLiberacao';
        $(modal + " span[name='largura']").html(obj.largura.replace('.', ','));
        $(modal + " span[name='comprimento']").html(obj.comprimento.replace('.', ','));

        $(modal + " #operador").html(obj.operador);
        $(modal + " #qualidadeSolda").html(obj.soldaTexto);
        $(modal + " #qualidadeSacaria").html(obj.sacariaTexto);
        $(modal + " #codigoFaca").html(obj.codigoFacaTexto);
        $(modal + " #qualidadeImpressao").html(obj.impressaoTexto);
        $(modal + " #pista").html(obj.pistaTexto);
        $(modal + " #bobina").html(obj.numero);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);

        $(modal + " span[name='sanfonaEsq']").html(obj.sanfonaEsq ? obj.sanfonaEsq.replace('.', ',') : '-');
        $(modal + " span[name='sanfonaDir']").html(obj.sanfonaDir ? obj.sanfonaDir.replace('.', ',') : '-');

        if (obj.reinspecao === '1') {
            $(modal + " .reinspecao").show();
        } else {
            $(modal + " .reinspecao").hide();
        }

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));

        if (obj.semVistoria != '1') {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        } else {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        }
    }
}

function semAnaliseExtrusao(form) {
    if ($('#semAnalise:checkbox:checked').length > 0) {
        $(form + ' #largura').val('').prop('disabled', true);
        $(form + ' #espessuraMedia').val('').prop('disabled', true);
        $(form + ' #espessuraComprimento').val('').prop('disabled', true);
        $(form + ' #espessuraLargura').val('').prop('disabled', true);
        $(form + ' #espessuraPeso').val('').prop('disabled', true);
        $(form + ' #espessura').val('').prop('disabled', true);
        $(form + ' #espessuraMax').val('').prop('disabled', true);
        $(form + ' #sanfonaEsq').val('').prop('disabled', true);
        $(form + ' #sanfonaDir').val('').prop('disabled', true);
        $(form + ' #impressao').val('').prop('disabled', true);
        $(form + ' #operador').val('').prop('disabled', true);
        $(form + ' #faceamento').val('').prop('disabled', true);
        $(form + ' input[type=radio]').prop('disabled', true);
    } else {
        $(form + ' #largura').val('').prop('disabled', false);
        $(form + ' #espessuraMedia').val('').prop('disabled', false);
        $(form + ' #espessuraComprimento').val('').prop('disabled', false);
        $(form + ' #espessuraLargura').val('').prop('disabled', false);
        $(form + ' #espessuraPeso').val('').prop('disabled', false);
        $(form + ' #espessura').val('').prop('disabled', false);
        $(form + ' #espessuraMax').val('').prop('disabled', false);
        $(form + ' #operador').val('').prop('disabled', false);
        $(form + ' #faceamento').val('').prop('disabled', false);
        $(form + ' input[type=radio]').prop('disabled', false);

        if ($(form + ' #labelSanfona').find("span").length == 0) {
            $(form + ' #sanfonaEsq').val('').prop('disabled', false);
            $(form + ' #sanfonaDir').val('').prop('disabled', false);
        }
        if ($(form + ' #labelImpressao').find("span").length == 0) {
            $(form + ' #impressao').val('').prop('disabled', false);
        }
    }
    $(form + ' .selectpicker').selectpicker('refresh');
}

function semAnaliseCorte(form) {
    if ($(form + ' #semAnalise:checkbox:checked').length > 0) {
        $(form + ' #pista').val('').prop('disabled', true);
        $(form + ' #largura').val('').prop('disabled', true);
        $(form + ' #comprimento').val('').prop('disabled', true);
        $(form + ' #qualidadeSolda').val('').prop('disabled', true);
        $(form + ' #qualidadeSacaria').val('').prop('disabled', true);
        $(form + ' #codigoFaca').val('').prop('disabled', true);
        $(form + ' #sanfonaEsq').val('').prop('disabled', true);
        $(form + ' #sanfonaDir').val('').prop('disabled', true);
        $(form + ' #qualidadeImpressao').val('').prop('disabled', true);
        $(form + ' #operador').val('').prop('disabled', true);
        $(form + ' #reinspecao').prop('disabled', true);
    } else {
        $(form + ' #pista').val('').prop('disabled', false);
        $(form + ' #largura').val('').prop('disabled', false);
        $(form + ' #comprimento').val('').prop('disabled', false);
        $(form + ' #qualidadeSolda').val('').prop('disabled', false);
        $(form + ' #qualidadeSacaria').val('').prop('disabled', false);
        $(form + ' #codigoFaca').val('').prop('disabled', false);
        $(form + ' #qualidadeImpressao').val('').prop('disabled', false);
        $(form + ' #operador').val('').prop('disabled', false);
        $(form + ' #reinspecao').prop('disabled', false);

        if ($(form + ' #labelSanfona').find("span").length == 0) {
            $(form + ' #sanfonaEsq').val('').prop('disabled', false);
            $(form + ' #sanfonaDir').val('').prop('disabled', false);
        }
    }
    $(form + ' .selectpicker').selectpicker('refresh');
}

function semAnaliseRefile(form) {
    if ($(form + ' #semAnalise:checkbox:checked').length > 0) {
        $(form + ' #largura').val('').prop('disabled', true);
        $(form + ' #comprimento').val('').prop('disabled', true);
        $(form + ' #qualidadeNovaBobina').val('').prop('disabled', true);
        $(form + ' #qualidadePicote').val('').prop('disabled', true);
        $(form + ' #qualidadeImpressao').val('').prop('disabled', true);
        $(form + ' #operador').val('').prop('disabled', true);
        $(form + ' #reinspecao').prop('disabled', true);
    } else {
        $(form + ' #largura').val('').prop('disabled', false);
        $(form + ' #comprimento').val('').prop('disabled', false);
        $(form + ' #qualidadeNovaBobina').val('').prop('disabled', false);
        $(form + ' #qualidadePicote').val('').prop('disabled', false);
        $(form + ' #qualidadeImpressao').val('').prop('disabled', false);
        $(form + ' #operador').val('').prop('disabled', false);
        $(form + ' #reinspecao').prop('disabled', false);
    }
    $(form + ' .selectpicker').selectpicker('refresh');
}

function semAnaliseImpressora(form) {
    if ($(form + ' #semAnalise:checkbox:checked').length > 0) {
        $(form + ' #analiseVisual').val('').prop('disabled', true);
        $(form + ' #tonalidade').val('').prop('disabled', true);
        $(form + ' #conferenciaArte').val('').prop('disabled', true);
        $(form + ' #ladoTratamento').val('').prop('disabled', true);
        $(form + ' #larguraEmbalagem').val('').prop('disabled', true);
        $(form + ' #passoEmbalagem').val('').prop('disabled', true);
        $(form + ' #leituraCodigoBarras').val('').prop('disabled', true);
        $(form + ' #testeAderenciaTinta').val('').prop('disabled', true);
        $(form + ' #sentidoDesbobinamento').val('').prop('disabled', true);
        $(form + ' #operador').val('').prop('disabled', true);
        $(form + ' #reinspecao').prop('disabled', true);
    } else {
        $(form + ' #analiseVisual').val('').prop('disabled', false);
        $(form + ' #tonalidade').val('').prop('disabled', false);
        $(form + ' #conferenciaArte').val('').prop('disabled', false);
        $(form + ' #ladoTratamento').val('').prop('disabled', false);
        $(form + ' #larguraEmbalagem').val('').prop('disabled', false);
        $(form + ' #passoEmbalagem').val('').prop('disabled', false);
        $(form + ' #leituraCodigoBarras').val('').prop('disabled', false);
        $(form + ' #testeAderenciaTinta').val('').prop('disabled', false);
        $(form + ' #sentidoDesbobinamento').val('').prop('disabled', false);
        $(form + ' #operador').val('').prop('disabled', false);
        $(form + ' #reinspecao').prop('disabled', false);
    }
    $(form + ' .selectpicker').selectpicker('refresh');
}

function populaDadosAnalise(dados, acao) {

    var modal = '#modalCadastrarExtrusoraAnalise';
    var obj = JSON.parse(dados);

    if (acao == 'editar') {

        $(modal + " #semAnalise").prop('checked', obj.semAnalise);
        semAnaliseExtrusao('#modalCadastrarExtrusoraAnalise');

        if (obj.tipoMedida == 1) {
            $(modal + " input[name='tipoMedida']")[0].checked = true;
            $('.espessuraMedida input').prop('disabled', false);
            $(modal + " input[name='espessura']").val(obj.espessuraMin.replace('.', ','));
            $(modal + " input[name='espessuraMax']").val(obj.espessuraMax.replace('.', ','));
            $('#formExtrusoraAnalise .espessuraMedida').show();
            $('#formExtrusoraAnalise .espessuraPeso').hide();
        } else if (obj.tipoMedida == 2) {
            $(modal + " input[name='tipoMedida']")[1].checked = true;
            $('.espessuraPeso input').prop('disabled', false);
            $(modal + " input[name='espessuraLargura']").val(obj.espessuraLargura.replace('.', ','));
            $(modal + " input[name='espessuraComprimento']").val(obj.espessuraComprimento.replace('.', ','));
            $(modal + " input[name='espessuraPeso']").val(obj.espessuraPeso.replace('.', ','));
            $(modal + " input[name='espessuraMedia']").val(obj.espessuraMedia.replace('.', ','));
            $(modal + " #espessuraMediaText span").html(obj.espessuraMedia.replace('.', ','));
            $('#formExtrusoraAnalise .espessuraPeso').show();
            $('#formExtrusoraAnalise .espessuraMedida').hide();
        }

        $(modal + " #salvarAnalise").html('Salvar');

        $(modal + " input[name='largura']").val(obj.largura.replace('.', ','));
        $(modal + " input[name='sanfonaEsq']").val(obj.sanfonaEsq.replace('.', ','));
        $(modal + " input[name='sanfonaDir']").val(obj.sanfonaDir.replace('.', ','));
        $(modal + " #impressao").val(obj.impressao);

        $(modal + " #faceamento").val(obj.faceamento);

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idextrusoraAnalise").val(obj.idextrusoraAnalise);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarExtrusoraAnalise';

        if (obj.tipoMedida == 1) {

            $(modal + " span[name='espessuraLargura']").html('');
            $(modal + " span[name='espessuraComprimento']").html('');
            $(modal + " span[name='espessuraPeso']").html('');
            $(modal + " span[name='espessuraMedia']").html('');

            $(modal + " span[name='espessura']").html(obj.espessuraMin.replace('.', ','));
            $(modal + " span[name='espessuraMax']").html(obj.espessuraMax.replace('.', ','));
            $(modal + ' .espessuraMedida').show();
        } else {

            $(modal + " span[name='espessura']").html('');
            $(modal + " span[name='espessuraMax']").html('');

            $(modal + " span[name='espessuraLargura']").html(obj.espessuraLargura.replace('.', ','));
            $(modal + " span[name='espessuraComprimento']").html(obj.espessuraComprimento.replace('.', ','));
            $(modal + " span[name='espessuraPeso']").html(obj.espessuraPeso.replace('.', ','));
            $(modal + " span[name='espessuraMedia']").html(obj.espessuraMedia.replace('.', ','));
            $(modal + ' .espessuraPeso').show();
        }
        $(modal + " span[name='largura']").html(obj.largura.replace('.', ','));
        $(modal + " span[name='sanfonaEsq']").html(obj.sanfonaEsq ? obj.sanfonaEsq.replace('.', ',') : '-');
        $(modal + " span[name='sanfonaDir']").html(obj.sanfonaDir ? obj.sanfonaDir.replace('.', ',') : '-');
        $(modal + " #impressao").html(obj.impressaoTexto);
        $(modal + " #faceamento").html(obj.faceamentoTexto);
        $(modal + " #operador").html(obj.operador);
        $(modal + " #bobina").html(obj.numero);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));

        if (obj.semAnalise == '1') {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        } else {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        }
    }
}

function populaDadosCorteAnalise(dados, acao) {

    var modal = '#modalCadastrarCorteAnalise';
    var obj = JSON.parse(dados);

    if (acao == 'editar') {

        $(modal + " #semAnalise").prop('checked', obj.semAnalise);
        semAnaliseCorte('#modalCadastrarCorteAnalise');

        $(modal + " #salvarAnalise").html('Salvar');

        $(modal + " input[name='largura']").val(obj.largura.replace('.', ','));
        $(modal + " input[name='comprimento']").val(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadeSolda").val(obj.solda);
        $(modal + " #qualidadeSacaria").val(obj.sacaria);
        $(modal + " #codigoFaca").val(obj.idcodigoFaca);
        $(modal + " #qualidadeImpressao").val(obj.impressao);
        $(modal + " #pista").val(obj.pista);

        $(modal + " input[name='sanfonaEsq']").val(obj.sanfonaEsq.replace('.', ','));
        $(modal + " input[name='sanfonaDir']").val(obj.sanfonaDir.replace('.', ','));

        if (obj.reinspecao == 1) {
            $(modal + " #reinspecao").prop('checked', true);
        } else {
            $(modal + " #reinspecao").prop('checked', false);
        }

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idcorteAnalise").val(obj.idcorteAnalise);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarCorteAnalise';

        $(modal + " span[name='bobina']").html(obj.numero);
        $(modal + " span[name='largura']").html(obj.largura.replace('.', ','));
        $(modal + " span[name='comprimento']").html(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadeSolda").html(obj.soldaTexto);
        $(modal + " #qualidadeSacaria").html(obj.sacariaTexto);
        $(modal + " #codigoFaca").html(obj.codigoFacaTexto);
        $(modal + " #qualidadeImpressao").html(obj.impressaoTexto);
        $(modal + " #pista").html(obj.pistaTexto);
        $(modal + " #operador").html(obj.operador);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);

        $(modal + " span[name='sanfonaEsq']").html(obj.sanfonaEsq ? obj.sanfonaEsq.replace('.', ',') : '-');
        $(modal + " span[name='sanfonaDir']").html(obj.sanfonaDir ? obj.sanfonaDir.replace('.', ',') : '-');

        if (obj.reinspecao === '1') {
            $(modal + " .reinspecao").show();
        } else {
            $(modal + " .reinspecao").hide();
        }

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));

        if (obj.semAnalise == '1') {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        } else {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        }
    }
}

function populaDadosSetup(dados, acao) {

    var obj = JSON.parse(dados);

    if (acao == 'editar') {

        var modal = '#modalCadastrarExtrusoraSetup';

        $(modal + " #salvarSetup").html('Salvar');

        $(modal + " input[name='largura']").val(obj.largura.replace('.', ','));

        if (obj.tipoMedida == 1) {
            $(modal + " input[name='tipoMedida']")[0].checked = true;
            $('.espessuraMedida input').prop('disabled', false);
            $(modal + " input[name='espessura']").val(obj.espessuraMin.replace('.', ','));
            $(modal + " input[name='espessuraMax']").val(obj.espessuraMax.replace('.', ','));
            $('#formExtrusoraSetup .espessuraMedida').show();
            $('#formExtrusoraSetup .espessuraPeso').hide();
        } else if (obj.tipoMedida == 2) {
            $(modal + " input[name='tipoMedida']")[1].checked = true;
            $('.espessuraPeso input').prop('disabled', false);
            $(modal + " input[name='espessuraLargura']").val(obj.espessuraLargura.replace('.', ','));
            $(modal + " input[name='espessuraComprimento']").val(obj.espessuraComprimento.replace('.', ','));
            $(modal + " input[name='espessuraPeso']").val(obj.espessuraPeso.replace('.', ','));
            $(modal + " input[name='espessuraMedia']").val(obj.espessuraMedia.replace('.', ','));
            $(modal + " #espessuraMediaText span").html(obj.espessuraMedia.replace('.', ','));
            $('#formExtrusoraSetup .espessuraPeso').show();
            $('#formExtrusoraSetup .espessuraMedida').hide();
        }

        $(modal + " input[name='sanfonaEsq']").val(obj.sanfonaEsq.replace('.', ','));
        $(modal + " input[name='sanfonaDir']").val(obj.sanfonaDir.replace('.', ','));
        $(modal + " #impressao").val(obj.impressao);

        $(modal + " #faceamento").val(obj.faceamento);

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #analise").val(obj.analise);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idextrusoraSetup").val(obj.idextrusoraSetup);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarExtrusoraSetup';
        $(modal + " span[name='largura']").html(obj.largura.replace('.', ','));

        if (obj.tipoMedida == 1) {

            $(modal + " span[name='espessuraLargura']").html('');
            $(modal + " span[name='espessuraComprimento']").html('');
            $(modal + " span[name='espessuraPeso']").html('');
            $(modal + " span[name='espessuraMedia']").html('');

            $(modal + " span[name='espessura']").html(obj.espessuraMin.replace('.', ','));
            $(modal + " span[name='espessuraMax']").html(obj.espessuraMax.replace('.', ','));
            $(modal + ' .espessuraMedida').show();
        } else {

            $(modal + " span[name='espessura']").html('');
            $(modal + " span[name='espessuraMax']").html('');

            $(modal + " span[name='espessuraLargura']").html(obj.espessuraLargura.replace('.', ','));
            $(modal + " span[name='espessuraComprimento']").html(obj.espessuraComprimento.replace('.', ','));
            $(modal + " span[name='espessuraPeso']").html(obj.espessuraPeso.replace('.', ','));
            $(modal + " span[name='espessuraMedia']").html(obj.espessuraMedia.replace('.', ','));
            $(modal + ' .espessuraPeso').show();
        }

        $(modal + " span[name='sanfonaEsq']").html(obj.sanfonaEsq ? obj.sanfonaEsq.replace('.', ',') : '-');
        $(modal + " span[name='sanfonaDir']").html(obj.sanfonaDir ? obj.sanfonaDir.replace('.', ',') : '-');
        $(modal + " #impressao").html(obj.impressaoTexto);
        $(modal + " #faceamento").html(obj.faceamentoTexto);
        $(modal + " #operador").html(obj.operador);
        $(modal + " #analise").html(obj.analise);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));

        if (obj.semVistoria != '1') {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        } else {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        }
    }
}

function removerExtrusoraPresetup(idextrusoraPresetup) {
    bootbox.confirm({
        message: "Confirma exclusão do registro?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerExtrusoraPresetup.php',
                    data: {
                        idextrusoraPresetup: idextrusoraPresetup
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function excluirSetupAnalise(id, tipo) {
    bootbox.confirm({
        message: "Confirma exclusão do registro?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerExtrusoraSetupAnalise.php',
                    data: {
                        id: id,
                        tipo: tipo
                    },
                    success: function (r) {
                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function excluirCorte(id, tipo) {
    bootbox.confirm({
        message: "Confirma exclusão do registro?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerCorte.php',
                    data: {
                        id: id,
                        tipo: tipo
                    },
                    success: function (r) {
                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function excluirRefile(id, tipo) {
    bootbox.confirm({
        message: "Confirma exclusão do registro?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerRefile.php',
                    data: {
                        id: id,
                        tipo: tipo
                    },
                    success: function (r) {
                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function excluirImpressora(id, tipo) {
    bootbox.confirm({
        message: "Confirma exclusão do registro?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerImpressora.php',
                    data: {
                        id: id,
                        tipo: tipo
                    },
                    success: function (r) {
                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function paginar(numero, acao) {
    if (acao == '-') {
        $('#pagina').val(parseInt(numero) - 1);
    } else if (acao == '+') {
        $('#pagina').val(parseInt(numero) + 1);
    } else {
        $('#pagina').val(numero);
    }
    $('#formularioConsulta').submit();
}

function limparFiltro() {
    $('input').val('');
    $('select').val('');
    $('.selectpicker').selectpicker('refresh');
    $('#formularioConsulta').submit();
};

/*CORTE*/

function carregarDadosSetupCorte(idcorteSetup, acao) {
    /*adequar*/
}

function montarCorteSetup(idordemProducao, idmaquina, idcorteSetup, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarCorteSetup' : '#modalCadastrarCorteSetup');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 2, false),
        montarSelectFilho('carregarOperador', 'operador', 2, false),
        montarSelect('carregarCorteQualidadeSolda', 'qualidadeSolda', false),
        montarSelect('carregarCorteQualidadeSacaria', 'qualidadeSacaria', false),
        montarSelect('carregarCodigoFaca', 'codigoFaca', false),
        montarSelect('carregarCorteQualidadeImpressao', 'qualidadeImpressao', false),
        montarSelect('carregarCortePista', 'pista', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false))
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);
                if ($(modal + ' #bobina' + ' option').size() <= 1) {
                    $('.corte-outra-bobina').show();
                } else {
                    $('.corte-outra-bobina').hide();
                }

                if (r.sanfona == '2' && $(modal + ' #labelSanfona').find("span").length == 0) {
                    $(modal + ' #sanfonaEsq').prop('disabled', true);
                    $(modal + ' #sanfonaDir').prop('disabled', true);
                    $(modal + ' #labelSanfona').append('<span class="font-italic text-danger"> (Não aplicável)</span>');
                }
            }
        ).done(
            function () {
                $.when(carregarDadosSetupCorte(idcorteSetup, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}



/*REFILE*/

function carregarDadosLiberacaoRefile(idrefileLiberacao, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarRefileLiberacao.php',
        data: {
            idrefileLiberacao: idrefileLiberacao
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosRefileLiberacao(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function carregarDadosLiberacaoImpressora(idimpressoraLiberacao, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarImpressoraLiberacao.php',
        data: {
            idimpressoraLiberacao: idimpressoraLiberacao
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosImpressoraLiberacao(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function populaDadosRefileLiberacao(dados, acao) {

    var modal = '#modalCadastrarRefileLiberacao';
    var obj = JSON.parse(dados);

    if (acao == 'editar') {
        $(modal + " #salvarSetup").html('Salvar');

        $(modal + " input[name='largura']").val(obj.largura.replace('.', ','));
        $(modal + " input[name='comprimento']").val(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadePicote").val(obj.picote);
        $(modal + " #qualidadeNovaBobina").val(obj.novaBobina);
        $(modal + " #qualidadeImpressao").val(obj.impressao);

        if (obj.reinspecao == 1) {
            $(modal + " #reinspecao").prop('checked', true);
        } else {
            $(modal + " #reinspecao").prop('checked', false);
        }

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idrefileLiberacao").val(obj.idrefileLiberacao);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarRefileLiberacao';

        $(modal + " span[name='bobina']").html(obj.numero);
        $(modal + " span[name='largura']").html(obj.largura.replace('.', ','));
        $(modal + " span[name='comprimento']").html(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadePicote").html(obj.picoteTexto);
        $(modal + " #qualidadeNovaBobina").html(obj.novaBobinaTexto);
        $(modal + " #qualidadeImpressao").html(obj.impressaoTexto);
        $(modal + " #operador").html(obj.operador);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);
        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        if (obj.reinspecao === '1') {
            $(modal + " .reinspecao").show();
        } else {
            $(modal + " .reinspecao").hide();
        }

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));
        if (obj.semVistoria != '1') {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        } else {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        }
    }
}

function montarRefileLiberacao(idordemProducao, idmaquina, idrefileLiberacao, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarRefileLiberacao' : '#modalCadastrarRefileLiberacao');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 3, false),
        montarSelectFilho('carregarOperador', 'operador', 3, false),
        montarSelect('carregarRefileQualidadePicote', 'qualidadePicote', false),
        montarSelect('carregarRefileQualidadeNovaBobina', 'qualidadeNovaBobina', false),
        montarSelect('carregarRefileQualidadeImpressao', 'qualidadeImpressao', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false))
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);
                if ($(modal + ' #bobina' + ' option').size() <= 1) {
                    $('.refile-outra-bobina').show();
                } else {
                    $('.refile-outra-bobina').hide();
                }
            }
        ).done(
            function () {
                $.when(carregarDadosLiberacaoRefile(idrefileLiberacao, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}

function carregarDadosAnaliseRefile(idrefileAnalise, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarRefileAnalise.php',
        data: {
            idrefileAnalise: idrefileAnalise
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosRefileAnalise(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function carregarDadosAnaliseImpressora(idimpressoraAnalise, acao) {
    var dfd = $.Deferred();
    var retorno;
    $.ajax({
        type: "POST",
        url: '../ajax/carregarImpressoraAnalise.php',
        data: {
            idimpressoraAnalise: idimpressoraAnalise
        },
        success: function (r) {

            if (typeof acao != 'undefined') {
                populaDadosImpressoraAnalise(r, acao);
            }

            dfd.resolve(r);
        }
    });
    return dfd.promise();
}

function populaDadosRefileAnalise(dados, acao) {

    var modal = '#modalCadastrarRefileAnalise';
    var obj = JSON.parse(dados);

    if (acao == 'editar') {

        $(modal + " #semAnalise").prop('checked', obj.semAnalise);
        semAnaliseRefile('#modalCadastrarRefileAnalise');

        $(modal + " #salvarAnalise").html('Salvar');

        $(modal + " input[name='largura']").val(obj.largura.replace('.', ','));
        $(modal + " input[name='comprimento']").val(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadePicote").val(obj.picote);
        $(modal + " #qualidadeNovaBobina").val(obj.novaBobina);
        $(modal + " #qualidadeImpressao").val(obj.impressao);

        if (obj.reinspecao == 1) {
            $(modal + " #reinspecao").prop('checked', true);
        } else {
            $(modal + " #reinspecao").prop('checked', false);
        }

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idrefileAnalise").val(obj.idrefileAnalise);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarRefileAnalise';

        $(modal + " span[name='bobina']").html(obj.numero);
        $(modal + " span[name='largura']").html(obj.largura.replace('.', ','));
        $(modal + " span[name='comprimento']").html(obj.comprimento.replace('.', ','));
        $(modal + " #qualidadePicote").html(obj.picoteTexto);
        $(modal + " #qualidadeNovaBobina").html(obj.novaBobinaTexto);
        $(modal + " #qualidadeImpressao").html(obj.impressaoTexto);
        $(modal + " #operador").html(obj.operador);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);

        if (obj.reinspecao === '1') {
            $(modal + " .reinspecao").show();
        } else {
            $(modal + " .reinspecao").hide();
        }

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));

        if (obj.semAnalise == '1') {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        } else {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        }
    }
}

function montarRefileAnalise(idordemProducao, idmaquina, idrefileAnalise, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarRefileAnalise' : '#modalCadastrarRefileAnalise');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 3, false),
        montarSelectFilho('carregarOperador', 'operador', 3, false),
        montarSelect('carregarRefileQualidadePicote', 'qualidadePicote', false),
        montarSelect('carregarRefileQualidadeNovaBobina', 'qualidadeNovaBobina', false),
        montarSelect('carregarRefileQualidadeImpressao', 'qualidadeImpressao', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false))
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);
                if ($(modal + ' #bobina' + ' option').size() <= 1) {
                    $('.refileAnalise-outra-bobina').show();
                } else {
                    $('.refileAnalise-outra-bobina').hide();
                }
            }
        ).done(
            function () {
                $.when(carregarDadosAnaliseRefile(idrefileAnalise, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}

function SomenteNumero(e) {
    var tecla = (window.event) ? event.keyCode : e.which;
    if ((tecla > 47 && tecla < 58)) return true;
    else {
        if (tecla == 8 || tecla == 0) return true;
        else return false;
    }
}

function editarOperador(idoperador, operador, idtipoMaquina, codigo) {
    $('#idoperador').val(idoperador);
    $('#operador').val(operador);
    $('#idtipoMaquina').val(idtipoMaquina);
    $('#codigo').val(codigo);

    $('#cadastrar').html('Salvar');

    $('#modalOperador').modal();
}

function removerOperador(idoperador) {
    bootbox.confirm({
        message: "Confirma exclusão do operador?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerOperador.php',
                    data: {
                        idoperador: idoperador
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function mudarSituacaoOperador(idoperador) {
    bootbox.confirm({
        message: "Confirma alteração da situação do operador?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoOperador.php',
                    data: {
                        idoperador: idoperador
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function editarObservacao(idobservacao, observacao, idtipoMaquina) {
    $('#idobservacao').val(idobservacao);
    $('#observacao').val(observacao);
    $('#idtipoMaquina').val(idtipoMaquina);

    $('#cadastrar span').html('Salvar');

    $('#modalObservacao').modal();
}

function removerObservacao(idobservacao) {
    bootbox.confirm({
        message: "Confirma exclusão da observação?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerObservacao.php',
                    data: {
                        idobservacao: idobservacao
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function mudarSituacaoObservacao(idobservacao) {
    bootbox.confirm({
        message: "Confirma alteração da situação da observação?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoObservacao.php',
                    data: {
                        idobservacao: idobservacao
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function editarUsuario(idusuario, nome, email, idgrupo) {
    $('#idusuario').val(idusuario);
    $('#usuario').val(nome);
    $('#email').val(email);
    $('#idgrupo').val(idgrupo);
    $('.msg-alterar-senha').show();
    $('#modalUsuario').modal();
}

function mudarSituacaoUsuario(idusuario) {
    bootbox.confirm({
        message: "Confirma alteração da situação do usuario?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoUsuario.php',
                    data: {
                        idusuario: idusuario
                    },
                    success: function (r) {
                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function carregarObservacoesBobina() {
    $("#modalCarregarObservacoes .modal-body #observacaoExtrusora tbody").html("<tr><td colspan='3' align='center'><i class='fa fa-circle-o-notch fa-spin fa-fw margin-bottom'></i><br>Aguarde, buscando observações </td></tr>");
    var modal = "#" + $(".modal:visible").attr("id");
    var idordemProducao = $(modal + ' #carregar-idordemProducao').val();
    var idmaquina = $(modal + ' #carregar-idmaquina').val();
    var bobina = $(modal + ' #bobina').val();

    if (!bobina) {
        _alert(2, "Selecione a bobina que deseja consulta o histórico de observações.");
    } else {
        $("#modalCarregarObservacoes").modal({
            backdrop: false
        });
        $.ajax({
            type: "POST",
            url: '../ajax/carregarObservacaoBobina.php',
            data: {
                idmaquina: idmaquina,
                bobina: bobina,
                idordemProducao: idordemProducao
            },
            success: function (r) {
                $("#modalCarregarObservacoes .modal-body #observacaoExtrusora tbody").html(r);
            }
        });
    }

}

function verificaSelectNegativo(form) {
    var envia = true;
    $(form + ' select').each(function () {
        if ($(this).find('option:selected').text() === 'Ruim'
            || $(this).find('option:selected').text() === 'Inadequado'
            || $(this).find('option:selected').text() === 'Não conforme'
            || $(this).find('option:selected').text() === 'Não OK') {
            envia = false;
            return false;
        }
    });
    return envia;
}

function verificaObservacaoTexto(form) {
    return ($.trim($(form + ' #observacaoTexto').val().replace(/[\t\n]+/g, ' ')) === 'Aut: Rep:');
}

/*FUNçõeS DA IMPRESSORA*/

function montarImpressoraLiberacao(idordemProducao, idmaquina, idimpressoraLiberacao, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarImpressoraLiberacao' : '#modalCadastrarImpressoraLiberacao');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 4, false),
        montarSelectFilho('carregarOperador', 'operador', 4, false),
        montarSelect('carregarImpressaoAnaliseVisual', 'analiseVisual', false),
        montarSelect('carregarImpressaoTonalidade', 'tonalidade', false),
        montarSelect('carregarImpressaoConferenciaArte', 'conferenciaArte', false),
        montarSelect('carregarImpressaoLadoTratamento', 'ladoTratamento', false),
        montarSelect('carregarImpressaoLeituraCodigoBarras', 'leituraCodigoBarras', false),
        montarSelect('carregarImpressaoTesteAderenciaTinta', 'testeAderenciaTinta', false),
        montarSelect('carregarImpressaoSentidoDesbobinamento', 'sentidoDesbobinamento', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false)
    )
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);
                if ($(modal + ' #bobina' + ' option').size() <= 1) {
                    $('.impressora-outra-bobina').show();
                } else {
                    $('.impressora-outra-bobina').hide();
                }
            }
        ).done(
            function () {
                $.when(carregarDadosLiberacaoImpressora(idimpressoraLiberacao, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}

function montarImpressoraAnalise(idordemProducao, idmaquina, idimpressoraAnalise, acao) {
    waitingDialog.show();
    var modal = (acao == 'visualizar' ? '#modalVisualizarImpressoraAnalise' : '#modalCadastrarImpressoraAnalise');
    $.when(carregarOrdemProducao(idordemProducao),
        montarSelectFilho('carregarObservacao', 'observacao', 4, false),
        montarSelectFilho('carregarOperador', 'operador', 4, false),
        montarSelect('carregarImpressaoAnaliseVisual', 'analiseVisual', false),
        montarSelect('carregarImpressaoTonalidade', 'tonalidade', false),
        montarSelect('carregarImpressaoConferenciaArte', 'conferenciaArte', false),
        montarSelect('carregarImpressaoLadoTratamento', 'ladoTratamento', false),
        montarSelect('carregarImpressaoLeituraCodigoBarras', 'leituraCodigoBarras', false),
        montarSelect('carregarImpressaoTesteAderenciaTinta', 'testeAderenciaTinta', false),
        montarSelect('carregarImpressaoSentidoDesbobinamento', 'sentidoDesbobinamento', false),
        montarSelectFilho('carregarOrdemProducaoBobina', 'bobina', idordemProducao, false)
    )
        .then(
            function (r) {
                r = $.parseJSON(r);
                $(modal + ' .carregar-cliente').html(r.cliente);
                $(modal + ' #carregar-idordemProducao').val(idordemProducao);
                $(modal + ' #carregar-idmaquina').val(idmaquina);
                if ($(modal + ' #bobina' + ' option').size() <= 1) {
                    $('.impressoraAnalise-outra-bobina').show();
                } else {
                    $('.impressoraAnalise-outra-bobina').hide();
                }
                $('#formImpressoraAnalise #bobinaValorOutro').prop('disabled', true).hide();
                $('#formImpressoraAnalise #botaoCarregarObservacoesBobina').prop('disabled', false);
                $('#formImpressoraAnalise #bobina').selectpicker('show');
            }
        ).done(
            function () {
                $.when(carregarDadosAnaliseImpressora(idimpressoraAnalise, acao)).then(
                    function () {
                        $(modal).modal();
                        waitingDialog.hide()
                    }
                );
            }
        );
}

function populaDadosImpressoraLiberacao(dados, acao) {

    var modal = '#modalCadastrarImpressoraLiberacao';
    var obj = JSON.parse(dados);

    if (acao == 'editar') {
        $(modal + " #salvarSetup").html('Salvar');

        $(modal + " input[name='larguraEmbalagem']").val(obj.larguraEmbalagem.replace('.', ','));
        $(modal + " input[name='passoEmbalagem']").val(obj.passoEmbalagem.replace('.', ','));

        $(modal + " #analiseVisual").val(obj.analiseVisual);
        $(modal + " #tonalidade").val(obj.tonalidade);
        $(modal + " #conferenciaArte").val(obj.conferenciaArte);
        $(modal + " #ladoTratamento").val(obj.ladoTratamento);
        $(modal + " #leituraCodigoBarras").val(obj.leituraCodigoBarras);
        $(modal + " #testeAderenciaTinta").val(obj.testeAderenciaTinta);
        $(modal + " #sentidoDesbobinamento").val(obj.sentidoDesbobinamento);

        if (obj.reinspecao == 1) {
            $(modal + " #reinspecao").prop('checked', true);
        } else {
            $(modal + " #reinspecao").prop('checked', false);
        }

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idimpressoraLiberacao").val(obj.idimpressoraLiberacao);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarImpressoraLiberacao';

        $(modal + " span[name='bobina']").html(obj.numero);
        $(modal + " span[name='larguraEmbalagem']").html(obj.larguraEmbalagem.replace('.', ','));
        $(modal + " span[name='passoEmbalagem']").html(obj.passoEmbalagem.replace('.', ','));

        $(modal + " #analiseVisual").html(obj.analiseVisualTexto);
        $(modal + " #tonalidade").html(obj.tonalidadeTexto);
        $(modal + " #conferenciaArte").html(obj.conferenciaArteTexto);
        $(modal + " #ladoTratamento").html(obj.ladoTratamentoTexto);
        $(modal + " #leituraCodigoBarras").html(obj.leituraCodigoBarrasTexto);
        $(modal + " #testeAderenciaTinta").html(obj.testeAderenciaTintaTexto);
        $(modal + " #sentidoDesbobinamento").html(obj.sentidoDesbobinamentoTexto);

        $(modal + " #operador").html(obj.operador);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);
        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        if (obj.reinspecao === '1') {
            $(modal + " .reinspecao").show();
        } else {
            $(modal + " .reinspecao").hide();
        }

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));
        if (obj.semVistoria != '1') {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        } else {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        }
    }
}

function populaDadosImpressoraAnalise(dados, acao) {

    var modal = '#modalCadastrarImpressoraAnalise';
    var obj = JSON.parse(dados);

    if (acao == 'editar') {

        $(modal + " #semAnalise").prop('checked', obj.semAnalise);
        semAnaliseImpressora('#modalCadastrarImpressoraAnalise');

        $(modal + " #salvarAnalise").html('Salvar');

        $(modal + " input[name='larguraEmbalagem']").val(obj.larguraEmbalagem.replace('.', ','));
        $(modal + " input[name='passoEmbalagem']").val(obj.passoEmbalagem.replace('.', ','));

        $(modal + " #analiseVisual").val(obj.analiseVisual);
        $(modal + " #tonalidade").val(obj.tonalidade);
        $(modal + " #conferenciaArte").val(obj.conferenciaArte);
        $(modal + " #ladoTratamento").val(obj.ladoTratamento);
        $(modal + " #leituraCodigoBarras").val(obj.leituraCodigoBarras);
        $(modal + " #testeAderenciaTinta").val(obj.testeAderenciaTinta);
        $(modal + " #sentidoDesbobinamento").val(obj.sentidoDesbobinamento);

        if (obj.reinspecao == 1) {
            $(modal + " #reinspecao").prop('checked', true);
        } else {
            $(modal + " #reinspecao").prop('checked', false);
        }

        //verifica se operador está ativo
        if ($(modal + " #operador option[value='" + obj.idoperador + "']").length == 0) {
            $(modal + " #operador").append("<option value='" + obj.idoperador + "'>" + obj.operador + "</option>").val(obj.idoperador);
        } else {
            $(modal + " #operador").val(obj.idoperador);
        }

        $(modal + " #bobina").val(obj.idordemProducaoBobina);
        $(modal + " textarea[name='observacaoTexto']").html(obj.obs);
        $(modal + " #idimpressoraAnalise").val(obj.idimpressoraAnalise);

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['idobservacao']);
            if ($(modal + " #observacao option[value='" + val['idobservacao'] + "']").length == 0) {
                $(modal + " #observacao").append("<option value='" + val['idobservacao'] + "'>" + val['observacao'] + "</option>").val(val['idobservacao']);
            }
        });

        $(modal + ' #observacao').selectpicker('val', obs);

    } else if (acao == 'visualizar') {

        var modal = '#modalVisualizarImpressoraAnalise';

        $(modal + " span[name='bobina']").html(obj.numero);

        $(modal + " span[name='larguraEmbalagem']").html(obj.larguraEmbalagem.replace('.', ','));
        $(modal + " span[name='passoEmbalagem']").html(obj.passoEmbalagem.replace('.', ','));

        $(modal + " #analiseVisual").html(obj.analiseVisualTexto);
        $(modal + " #tonalidade").html(obj.tonalidadeTexto);
        $(modal + " #conferenciaArte").html(obj.conferenciaArteTexto);
        $(modal + " #ladoTratamento").html(obj.ladoTratamentoTexto);
        $(modal + " #leituraCodigoBarras").html(obj.leituraCodigoBarrasTexto);
        $(modal + " #testeAderenciaTinta").html(obj.testeAderenciaTintaTexto);
        $(modal + " #sentidoDesbobinamento").html(obj.sentidoDesbobinamentoTexto);

        $(modal + " #operador").html(obj.operador);
        $(modal + " #analista").html(obj.analista);
        $(modal + " #dataHora").html(obj.dataCriacaoData + ' - ' + obj.dataCriacaoHora);

        if (obj.reinspecao === '1') {
            $(modal + " .reinspecao").show();
        } else {
            $(modal + " .reinspecao").hide();
        }

        var observacoes = $.parseJSON(obj.observacoes);
        var obs = [];
        $.each(observacoes, function (i, val) {
            obs.push(val['observacao']);
        });

        $(modal + ' #observacaoTexto').html(obs.join(', ') + (obj.obs != '' ? (obs.length > 0 ? ', ' : '') + obj.obs : ''));

        if (obj.semAnalise == '1') {
            $(modal + ' .comAnalise').hide();
            $(modal + ' .semAnalise').show();
        } else {
            $(modal + ' .semAnalise').hide();
            $(modal + ' .comAnalise').show();
        }
    }
}

function removerCortePalavraChaveAlerta(idCortePalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma exclusão da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerPalavraChaveAlertaCS.php',
                    data: {
                        idCortePalavraChaveAlerta: idCortePalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}
function editarCortePalavraChaveAlerta(idCortePalavraChaveAlerta, cortePalavraChaveAlerta, mensagem) {
    $('#idCortePalavraChaveAlerta').val(idCortePalavraChaveAlerta);
    $('#cortePalavraChaveAlerta').val(cortePalavraChaveAlerta);
    $('#mensagem').val(mensagem);
    $('#cadastrar span').html('Salvar');
    $('#modalCortePalavraChaveAlerta').modal();
}
function mudarSituacaoCortePalavraChaveAlerta(idCortePalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma alteração da situação da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoPalavraChaveAlertaCS.php',
                    data: {
                        idCortePalavraChaveAlerta: idCortePalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}

function editarExtrusaoPalavraChaveAlerta(idExtrusaoPalavraChaveAlerta, extrusaoPalavraChaveAlerta, mensagem) {
    $('#idExtrusaoPalavraChaveAlerta').val(idExtrusaoPalavraChaveAlerta);
    $('#extrusaoPalavraChaveAlerta').val(extrusaoPalavraChaveAlerta);
    $('#mensagem').val(mensagem);
    $('#cadastrar span').html('Salvar');
    $('#modalExtrusaoPalavraChaveAlerta').modal();
}
function mudarSituacaoExtrusaoPalavraChaveAlerta(idExtrusaoPalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma alteração da situação da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoPalavraChaveAlertaExtrusao.php',
                    data: {
                        idExtrusaoPalavraChaveAlerta: idExtrusaoPalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}
function removerExtrusaoPalavraChaveAlerta(idExtrusaoPalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma exclusão da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerPalavraChaveAlertaExtrusao.php',
                    data: {
                        idExtrusaoPalavraChaveAlerta: idExtrusaoPalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}
function editarRefilePalavraChaveAlerta(idRefilePalavraChaveAlerta, refilePalavraChaveAlerta, mensagem) {
    $('#idRefilePalavraChaveAlerta').val(idRefilePalavraChaveAlerta);
    $('#refilePalavraChaveAlerta').val(refilePalavraChaveAlerta);
    $('#mensagem').val(mensagem);
    $('#cadastrar span').html('Salvar');
    $('#modalRefilePalavraChaveAlerta').modal();
}
function mudarSituacaoRefilePalavraChaveAlerta(idRefilePalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma alteração da situação da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoPalavraChaveAlertaRefile.php',
                    data: {
                        idRefilePalavraChaveAlerta: idRefilePalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}
function removerRefilePalavraChaveAlerta(idRefilePalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma exclusão da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerPalavraChaveAlertaRefile.php',
                    data: {
                        idRefilePalavraChaveAlerta: idRefilePalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}
function editarImpressaoPalavraChaveAlerta(idImpressaoPalavraChaveAlerta, impressaoPalavraChaveAlerta, mensagem) {
    $('#idImpressaoPalavraChaveAlerta').val(idImpressaoPalavraChaveAlerta);
    $('#impressaoPalavraChaveAlerta').val(impressaoPalavraChaveAlerta);
    $('#mensagem').val(mensagem);
    $('#cadastrar span').html('Salvar');
    $('#modalImpressaoPalavraChaveAlerta').modal();
}
function mudarSituacaoImpressaoPalavraChaveAlerta(idImpressaoPalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma alteração da situação da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/alterarSituacaoPalavraChaveAlertaImpressao.php',
                    data: {
                        idImpressaoPalavraChaveAlerta: idImpressaoPalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}
function removerImpressaoPalavraChaveAlerta(idImpressaoPalavraChaveAlerta) {
    bootbox.confirm({
        message: "Confirma exclusão da palavra chave?",
        title: "Confirmação",
        size: 'small',
        buttons: {
            confirm: {
                label: 'Sim',
                className: 'btn-success'
            },
            cancel: {
                label: 'Não',
                className: 'btn-danger'
            }
        },
        callback: function (result) {
            if (result) {
                $.ajax({
                    type: "POST",
                    url: '../ajax/removerPalavraChaveAlertaImpressao.php',
                    data: {
                        idImpressaoPalavraChaveAlerta: idImpressaoPalavraChaveAlerta
                    },
                    success: function (r) {

                        var data = $.parseJSON(r);
                        (data.status ? _alert(1, data.msg, false, true) : _alert(2, data.msg, false, false));
                    }
                });
            }
        }
    });
}