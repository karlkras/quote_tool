$(document).ready(function () {
    var word_toggle_state = false;
    
    $(function () {
        var config = {
            '.chosen-select-targlangs': {},
            '.chosen-select-targlangs-deselect': {allow_single_deselect: true},
            '.chosen-select-targlangs-no-single': {disable_search_threshold: 10},
            '.chosen-select-targlangs-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-targlangs-width': {width: "95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
    });

    $(function () {
        $("#targlang_chosen").chosen();
        $.validator.setDefaults({ignore: ":hidden:not(select)"})
        $("#ballParkerForm2").validate({
            rules: {chosen: "required"},
            message: {chosen: "Select Target Languages"}
        });
    });
    
    $("input[name=proofReading]:radio").change(function () {
        if ($(this).val() == 'none') {
            $('#proofreading_rate').attr('disabled', 'true');
            $('#proofreading_calc').attr('disabled', 'true');
            $('#proofreading_calc').val(0);

        } else {
            $('#proofreading_rate').removeAttr('disabled');
            $('#proofreading_calc').removeAttr('disabled');
            var totalWords = Number($('#words_total').val());
            var rate = Number($('#proofreading_rate').val());
            updateProofreadCalc(totalWords, rate);
        }
    });
    
    $(function () {
        $("#slider-vertical").slider({
            orientation: "vertical",
            range: "min",
            min: 0,
            max: 100,
            value: 0,
            step: 5,
            disabled: true,
            slide: function (event, ui) {
                $("#amount").val(ui.value + '%');
                if (ui.value === 100) {
                    $('#words_type_new').val("");
                    $('#words_type_match').val($('#words_total').val());
                } else {
                    if (ui.value > 0) {
                        var newPercent = (100 - ui.value) / 100;
                        var matchPercent = (ui.value) / 100;
                        $('#words_type_new').val(Math.round($('#words_total').val() * newPercent));
                        $('#words_type_match').val(Math.round($('#words_total').val() * matchPercent));
                    } else {
                        $('#words_type_new').val($('#words_total').val());
                        $('#words_type_match').val("");
                    }
                }
            }
        });
        $("#amount").val($("#slider-vertical").slider("value") + '%');
        // initialize word controls to be disabled...
        $("[id^=words_type]").attr("disabled", "true");
    });


    $(document).on('keyup change input', "#words_total", function () {
        var totalWords = Number($(this).val());
        toggleLeverageControls(totalWords >= 10 ? true : false);
        if (!$('#words_type_new').is(':disabled')) {
            $("[id^=words_type]").val("");
            $('#words_type_new').val($('#words_total').val());
            $("#slider-vertical").slider("value", 0);
            $("#amount").val($("#slider-vertical").slider("value") + '%');
        }
        if (!$('#proofreading_calc').is(':disabled')) {
            var proofRate = Number($('#proofreading_rate').val());
            if (proofRate > 0) {
                updateProofreadCalc(totalWords, proofRate);
            } else {
                $('#proofreading_calc').val('0');
            }
        }
    });

    $("#proofreading_rate").on("change", function (event, ui) {
        var totalWords = Number($('#words_total').val());
        var rate = Number($(this).val());

        updateProofreadCalc(totalWords, rate);
    });
    

    function updateProofreadCalc(words, rate) {
        if (rate > 0) {
            if (words > 0) {
                if (words < rate) {
                    $('#proofreading_calc').val(1);
                } else {
                    var hours = Math.ceil((words / rate) / .25) * .25;
                    $('#proofreading_calc').val(hours);
                }
                return;
            }
        }
        $('#proofreading_calc').val(0);
    }

    function toggleLeverageControls(trueOrfalse) {
        if (word_toggle_state !== trueOrfalse) {
            if (trueOrfalse === true) {
                $("[id^=words_type]").removeAttr("disabled");
                $("#slider-vertical").slider("option", "disabled", false);
            } else {
                $("[id^=words_type]").val("");
                $("[id^=words_type]").attr("disabled", "true");
                $("#slider-vertical").slider("value", 0);
                $("#amount").val($("#slider-vertical").slider("value") + '%');
                $("#slider-vertical").slider("option", "disabled", true);
            }
            word_toggle_state = trueOrfalse;
        }
    }    
    
    // reset values on the page...
    var onClickString;
    

    $("#backButton").on("click", function () {
        var args = "";
        if (!onClickString) {
            args = getHiddenArgs();
        } else {
            args = onClickString;
        }
        window.location.href = args;
        return false;
    });

    function getHiddenArgs() {
        var theArgs = "";
        $("input[type='hidden']").each(function () {
            if(theArgs.length !== 0) {
                theArgs += '&';
            }
            theArgs += $(this).attr('name') + "=" + encodeURIComponent($(this).val());
        })
        return  "estimate_1.php?" + theArgs;
    }

    $(function () {
        var params = $_GET();
        if (!$.isEmptyObject(params)) {
            var source_lang = params.source_lang;
            var targ_langs = params.targ_langs;
            var words_type_new = params.words_type_new;
            var words_type_match = params.words_type_match;
            var words_type_fuzzy = params.words_type_fuzzy;
            var words_total = params.words_total;
            var proofReading = params.proofReading;
            var proofreadingHours = params.proofreadingHours;
            var proofreading_rate = params.proofreading_rate;
            var assumedLeveraging = params.assumedLeveraging;
            


//            if (qtroot) {
//                $("#qtroot").val(qtroot);
//            }

            if (proofreadingHours) {
                $("#proofreading_calc").val(proofreadingHours);
            }

            if (proofreading_rate) {
                $("#proofreading_rate").val(proofreading_rate);
            }

            if (proofReading) {
                var test = "input:radio[value='" + proofReading + "']";
                $(test).click();
            }
            
            if (words_total) {
                // need to enable all word fields...
                $("[id^=words_type_").removeAttr("disabled");
                $("#words_total").val(words_total);
                $("#words_total").change();
            }
            
            if (words_type_new) {
                $("#words_type_new").val(words_type_new);
            }
            
            if (words_type_match) {
                $("#words_type_match").val(words_type_match);
            }

            if (words_type_fuzzy) {
                $("#words_type_fuzzy").val(words_type_fuzzy);
            }            

            if (assumedLeveraging) {
                var value = assumedLeveraging.slice(0, -1);
                $("#slider-vertical").slider("enable").slider("value", Number(value));
                $("#amount").val($("#slider-vertical").slider("value") + '%');
            }

            if (source_lang) {
                $(".chosen-select-sourcelang").val(source_lang).trigger("chosen:updated");
            }

            if (targ_langs) {
                var array = targ_langs.split(',');

                $(".chosen-select-targlangs").val(array).trigger("chosen:updated");
            }

            // now we need to make sure the prior page values are in the form...
            var page1Map = {
                "clientFirstName": params.clientFirstName,
                "clientLastName": params.clientLastName,
                "sales_rep": params.sales_rep,
                "projectName": params.projectName,
                "priceScheme": params.priceScheme,
                "rushFees": params.rushFees,
                "discountAmount": params.discountAmount,
                "discountType": params.discountType,
                "qtroot": params.qtroot,
                "callingPage": params.callingPage,
                "cycle": params.cycle,
                "companyName": params.companyName,
                "companyType": params.companyType,
                "clientPricingDiscount": params.clientPricingDiscount,
                "pricingdb": params.pricingdb,
                "clientPMPercent" : params.clientPMPercent
            };

            for (var key in page1Map) {
                if (page1Map[key]) {
                    $('<input>').attr({
                        type: 'hidden',
                        name: key,
                        value: page1Map[key]
                    }).appendTo('form');
                }
            }

            // now we need to rejigger the back link with the values...
            var argPart = document.URL.substr(document.URL.indexOf('?'));
            onClickString = "estimate_1.php" + argPart;

            $("#backButton").attr("onclick", onClickString);
        }
    });

    $('select#pricingSchemeSelect').click(function () {
        var map = {};
        $(this).find('option').each(function () {
            if (map[this.value]) {
                $(this).remove();
            }
            map[this.value] = true;
        })

    });


});


