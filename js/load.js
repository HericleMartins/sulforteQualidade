$(function(){

    $('.campo-mascara-quantidade, .campo-mascara-largura, .campo-mascara-espessura, .campo-mascara-sanfona, .campo-mascara-comprimento, .campo-mascara-peso').keypress(function(event) {
        var $this = $(this);
        if ((event.which != 44 || $this.val().indexOf(',') != -1) &&
            ((event.which < 48 || event.which > 57) &&
            (event.which != 0 && event.which != 8))) {
            event.preventDefault();
        }

        var text = $(this).val();
        if ((event.which == 44) && (text.indexOf(',') == -1)) {
            setTimeout(function() {
                if ($this.val().substring($this.val().indexOf(',')).length > 3) {
                    $this.val($this.val().substring(0, $this.val().indexOf(',') + 3));
                }
            }, 1);
        }

        if ((text.indexOf(',') != -1) &&
            (text.substring(text.indexOf(',')).length > 2) &&
            (event.which != 0 && event.which != 8) &&
            ($(this)[0].selectionStart >= text.length - 2)) {
            event.preventDefault();
        }
    });

    $('input.dataCalendario').datepicker({
        format: 'dd/mm/yyyy',
        language: 'pt-BR'
    });

    $('.clickable-remove').on('click',function(){
        var obj = this;
        bootbox.confirm({
            message: "Confirma remoção de ordem de produção da área de trabalho?",
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
                if(result){
                    var dados = {};
                    dados.idareaTrabalho = $(obj).closest('.area-trabalho-op').attr('idareaTrabalho');
                    $(".area-trabalho-op[idareaTrabalho="+dados.idareaTrabalho+"] div h3").prepend("<i class='fa fa-spinner fa-spin'></i>&nbsp;");
                    $.ajax({
                        type: "POST",
                        url: '../ajax/removerOrdemProducaoAreaTrabalho.php',
                        data: dados,
                        success: function(r) {
                            var data = $.parseJSON(r);
                            if(data.status){
                                _alert(1, data.msg, false, true);
                            } else {
                                _alert(2, data.msg, false, false);
                            }
                        }
                    });
                }
            }
        });
    });

    $('.modal').on('show.bs.modal', function (e) {
        $('.selectpicker').selectpicker('refresh');
    });

    $('button[modal="true"]').on('click',function(){
        $.getScript("../js/bootstrap-select.min.js")
            .done(function( script, textStatus ) {
                var modal_url = $(this).attr('modal-url');
                var modal_target = $(this).attr('modal-target');
                $('#' + modal_target + ' .modal-dialog .modal-content .modal-body').load('../c/' + modal_url + '.php');
                $('#' + modal_target).modal('show');
            })
            .fail(function( jqxhr, settings, exception ) {
                alert('erro');
            });
    });

    jQuery.validator.setDefaults({
        errorPlacement: function(error, element) {
            //não mostra mensagem de erro
        },
        /*errorPlacement: function(error, element) {
            error.addClass("help-block");
            element.parents(".form-group").addClass("has-feedback");
            if (element.prop("type") === "checkbox") {
                error.insertAfter(element.parent("label"));
            } else {
                error.insertAfter(element);
            }
            if (!element.next("span")[0]) {
                $("<span class='glyphicon glyphicon-remove form-control-feedback'></span>").insertAfter(element);
            }
        },*/
        success: function (label, element) {
            /*if (!$(element).next("span")[0]) {
                //$("<span class='glyphicon glyphicon-ok form-control-feedback'></span>").insertAfter($(element));
            }*/
        },
        highlight: function (element, errorClass, validClass) {
            $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            //$(element).next("span").addClass("glyphicon-remove").removeClass("glyphicon-ok");
        },
        unhighlight: function (element, errorClass, validClass) {
            $(element).parents(".form-group").addClass("has-success").removeClass("has-error");
            //$(element).next("span").addClass("glyphicon-ok").removeClass("glyphicon-remove");
        }
    });

    $('.modal').on('hidden.bs.modal', function () {
        var modalName = $(this).attr('id');
        var nameForm = $('#' + modalName + ' form').attr('id');
        $('#' + nameForm)[0].reset();
        $('.glyphicon, em').remove();
        $('div').removeClass("has-success has-feedback has-error");
        $('#formExtrusoraAnalise .espessuraMedida input, #formExtrusoraAnalise .espessuraPeso input').val('').prop('disabled', true);
        $('#formExtrusoraAnalise .espessuraMedida, #formExtrusoraAnalise .espessuraPeso').hide();

        $('#' + nameForm + ' #semAnalise').prop('disabled', false);

        if (nameForm == 'formExtrusoraAnalise'){
            semAnaliseExtrusao('#' + nameForm);
        } else if (nameForm == 'formCorteAnalise'){
            semAnaliseCorte('#' + nameForm);
        } else if (nameForm == 'formRefileAnalise'){
            semAnaliseRefile('#' + nameForm);
        }
    });

    $('#modalCadastrarExtrusoraPresetup').on('hidden.bs.modal', function () {
        $('.table-presetup-materia tbody').html('');
    });
});

$(document).on('click', '.panel-heading span.clickable', function(e){
    var $this = $(this);
    if(!$this.hasClass('panel-collapsed')) {
        $this.parents('.panel').find('.panel-body').slideUp();
        $this.addClass('panel-collapsed');
        $this.find('i').removeClass('fa-chevron-circle-up').addClass('fa-chevron-circle-down');
    } else {
        $this.parents('.panel').find('.panel-body').slideDown();
        $this.removeClass('panel-collapsed');
        $this.find('i').removeClass('fa-chevron-circle-down').addClass('fa-chevron-circle-up');
    }
});

