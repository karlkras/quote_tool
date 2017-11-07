$(document).ready(function () {
    var MIN_LENGTH = 2;
    var DEFAULT_PMPERCENT = 10;
    var DEFAULT_DISCOUNT = 0;
    // for the company
    $(function () {
        $(".company_auto").autocomplete({
            source: $('input[name="qtroot"]').val() + "/ajax/auto-complete.php",
            minLength: MIN_LENGTH
        });
    });

    $(function () {
        var config = {
            '.chosen-select': {},
            '.chosen-select-deselect': {allow_single_deselect: true},
            '.chosen-select-no-single': {disable_search_threshold: 10},
            '.chosen-select-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-width': {width: "95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
    });

    $(function () {
        $("#salesrep_chosen").chosen();
        $.validator.setDefaults({ignore: ":hidden:not(select)"})
        $("#ballParkerForm1").validate({
            rules: {chosen: "required"},
            message: {chosen: "Select a Sales Rep."}
        });
    });

    function resetPricingSchemeSelect() {
        $('select#pricingSchemeSelect option[value="Client-Specific Pricing"]').remove();
        $('select#pricingSchemeSelect').find('option').each(function () {
            if ($(this).val() == "Margin Pricing") {
                $(this).attr('selected', 'selected');
            }
        });
    }

    function resetBallParkerForm1() {
        $('#ballParkerForm1')[0].reset();
        $(".chosen-select").val('').trigger("chosen:updated");
        resetControls();
    }

    function resetControls() {
        $("input:radio[value='0']").click();
        $('#companyName').removeAttr('value');
        $('#pricingdb').remove();
        if ($("#errorMsg").hasClass("errorMsg")) {
            $("#errorMsg").text("");
            $("#errorMsg").removeClass("errorMsg");
        }

        if ($("#discountAmount").hasClass("customprice")) {
            $("#discountAmount").removeClass("customprice");
        }

        resetPricingSchemeSelect();
    }

    function resetBallParkerPage1() {
        resetBallParkerForm1();
        $("input:radio[name=companytype][value=client]").prop('checked', true);
        $("input:radio[name=companytype][value=client]").trigger('change');
    }

    $("#pricingSchemeSelect").change(function () {
        if ($("#discountAmount").hasClass('customprice')) {
            $("#discountAmount").removeClass('customprice');
        }

        if ($(this).val() == 'Client-Specific Pricing') {
            $("#discountAmount").val($("#clientPricingDiscount").val());
        } else {
            $("#discountAmount").val(0);
        }
    });

    $("#companyProspectInput").change(function () {
        $('#companyName').val($(this).val());
    });

    $("#discountAmount").on("change", function () {
        if ($(this).hasClass('customprice')) {
            $(this).removeClass('customprice');
        }
    });

    $("input[name=companytype]:radio").change(function () {
        var currentName = $("input[name='companyNameSwitch']").val();
        resetBallParkerForm1();
        $('#companyType').val($(this).val());
        if ($(this).val() == 'client') {
            $('.companyClient').removeAttr('hidden');
            $('#companyClientInput').attr('required', 'true');

            $('.companyProspect').attr('hidden', true);
            $('#companyProspectInput').removeAttr('required');
        } else {
            $('.companyProspect').removeAttr('hidden');
            $('#companyProspectInput').attr('required', 'true');

            $('.companyClient').attr('hidden', true);
            $('#companyClientInput').removeAttr('required');
            if (currentName) {
                $('#companyProspectInput').val(currentName);
                $("input[name='companyNameSwitch']").val("");
            }
        }
    });

    $("#resetPage1").on("click", function () {
        resetBallParkerPage1();
    });

    $(".company_auto").on("autocompletechange", function (event, ui) {
        resetControls();

        $('select#pricingSchemeSelect').find('option').each(function () {
            if ($(this).val() == "Client-Specific Pricing") {
                $(this).remove();
            }
        });

        if ($('#pricingdb').length > 0) {
            $('#pricingdb').remove();
        }

        $('#clientPMPercent').val('10');
        $('#companyName').val($(this).val());

        $.get($('input[name="qtroot"]').val() + "/ajax/companyInfo.php", {company: $(this).val()})
                .done(function (data) {
                    jsonObject = $.parseJSON(data);
                    processCompanyJson(jsonObject);
                }, "json");
    });

    function processCompanyJson(jsonObject) {
        // first set these back to a default state just in case.
        $("input[name='clientPMPercent']").val(DEFAULT_PMPERCENT);
        $('#clientPricingDiscount').val(DEFAULT_DISCOUNT);
        $("input[name='pricingdb']").remove();
        
        // if the return data is empty, they've entered 
        // an unknown client...
        if (! $.isEmptyObject(jsonObject)) {
            var pricingScheme = jsonObject.docTransPricingScheme;
            
            if (jsonObject.errorMsg) {
                $("#errorMsg").text(jsonObject.errorMsg);
                $("#errorMsg").addClass("errorMsg");
                $("select#pricingSchemeSelect").find("option[value='" + pricingScheme + "']").prop('selected', 'selected');
                return;
            }
            $('#discountAmount').val(jsonObject.discount);
            if (jsonObject.discount != "0") {
                $('#discountAmount').addClass("customprice");
            }
            $('#clientPricingDiscount').val(jsonObject.discount);

            $('#companyType').val('client');

            if ($("#pricingSchemeSelect option[value='" + pricingScheme + "']").length == 0) {
                $('<option>').val(pricingScheme).text(pricingScheme).appendTo('#pricingSchemeSelect');
            }

            $("select#pricingSchemeSelect").find("option[value='" + pricingScheme + "']").prop('selected', 'selected');
            
            if(jsonObject.pricingdatabase) {
                $('<input>').attr({
                    type: 'hidden',
                    id: 'pricingdb',
                    name: 'pricingdb',
                    value: jsonObject.pricingdatabase
                }).appendTo('form');
            }
            $("input[name='clientPMPercent']").val(jsonObject.pmpercent);

        } else {
            // trigger a click to set to prospect...
            $("input[name='companyNameSwitch']").val($("#companyClientInput").val());
            $('#companyType').val('prospect');
            $("#companyProspect").click();
        }
    }


    $(function () {
        var params = $_GET();
        if (!$.isEmptyObject(params)) {
            var firstName = params.clientFirstName;
            var lastName = params.clientLastName;
            var sales_rep = params.sales_rep;
            var projectName = params.projectName;
            var priceScheme = params.priceScheme;
            var rushFees = params.rushFees;
            var discountAmount = params.discountAmount;
            var discountType = params.discountType;
            var companyName = params.companyName;
            var companyType = params.companyType;
            var pricingdb = params.pricingdb;
            var qtroot = params.qtroot;
            var clientPricingDiscount = params.clientPricingDiscount;
            var clientPMPercent = params.clientPMPercent;

            if (companyType) {
                if (companyType == 'client') {
                    $("input:radio#companyClient").click();
                    $("#companyClientInput").val(companyName);
                    $("#companyType").val('client');
                } else {
                    $("input:radio#companyProspect").click();
                    $("#companyProspectInput").val(companyName);
                    $("#companyType").val('prospect');
                }
                $("#companyName").val(companyName);

            }

            if (qtroot) {
                $("#qtroot").val(qtroot);
            }

            if (clientPricingDiscount) {
                $("#clientPricingDiscount").val(clientPricingDiscount);
            }

            if (firstName) {
                $('input[name="clientFirstName"]').val(firstName);
            }
            if (lastName) {
                $('input[name="clientLastName"]').val(lastName);
            }
            if (projectName) {
                $('input[name="projectName"]').val(projectName);
            }

            if (rushFees) {
                var test = "input:radio[value='" + rushFees + "']";
                $(test).click();
            }
            if (sales_rep) {
                $(".chosen-select").val(sales_rep).trigger("chosen:updated");
            }
            if (discountAmount) {
                var test = "input:radio[value='" + discountType + "']";
                $(test).click();
                $("#discountAmount").val(discountAmount);
            }

            if (priceScheme) {
                var found = false;
                if (priceScheme == "Client-Specific Pricing") {
                    $('select#pricingSchemeSelect option:selected').removeAttr('selected');

                    $('select#pricingSchemeSelect').find('option').each(function () {
                        if ($(this).val() == priceScheme) {
                            found = true;
                        }
                    });
                    if (!found) {
                        $('<option>').val(priceScheme).text(priceScheme).appendTo('#pricingSchemeSelect');
                    }
                }
                $('select#pricingSchemeSelect').find('option').each(function () {
                    if ($(this).val() == priceScheme) {
                        $(this).attr('selected', 'selected');
                    }
                });
            }

            if (pricingdb) {
                if (priceScheme = "Client-Specific Pricing") {
                    $('<option>').val(priceScheme).text(priceScheme).appendTo('#pricingSchemeSelect');
                }

                $('<input>').attr({
                    type: 'hidden',
                    id: 'pricingdb',
                    name: 'pricingdb',
                    value: pricingdb
                }).appendTo('form');
            }

            if (clientPMPercent) {
                $("input[name='clientPMPercent']").val(clientPMPercent)
            }
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
//        $(this).find('option').each(function () {
//            console.log($(this).val());
//        });
    });

});


