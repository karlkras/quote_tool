$(document).ready(function () {
    $(function () {
        if ($(".document_formatting_row").length > 0) {
            $(".document_formatting_row").hide();
        }
    });    
    $(function () {
        var config = {
            '.chosen-select-format': {},
            '.chosen-select-format-deselect': {allow_single_deselect: true},
            '.chosen-select-format-no-single': {disable_search_threshold: 10},
            '.chosen-select-format-no-results': {no_results_text: 'Oops, nothing found!'},
            '.chosen-select-format-width': {width: "95%"}
        }
        for (var selector in config) {
            $(selector).chosen(config[selector]);
        }
    });
    
    
    $(function () {
        $("#fileformat_chosen").chosen();
        $.validator.setDefaults({ignore: ":hidden:not(select)"})
        $("#ballParkerForm3").validate({
            rules: {chosen: "required"},
            message: {chosen: "Select File Format"}
        });
    });

    $(".chosen-select-format").chosen().change(function () {
        $.get($('input[name="qtroot"]').val() + "/ajax/formatHours.php", {format: $(this).val()})
                .done(function (data) {
                    jsonObject = $.parseJSON(data);
                    processFileFormattingJson(jsonObject);
                }, "json");
    });

    $('#otherEngineering').change(function () {
        if ($(this).is(":checked")) {
            $(".engineeringHours").show();
            $("#engineeringHours").attr("required", true);
            $("#engineeringHours").val(0);
        } else {
            $(".engineeringHours").hide();
            $("#engineeringHours").val(0);
            $("#engineeringHours").removeAttr('required');

        }
    });

    $('#tmWorkNeeded').change(function () {
        if ($(this).is(":checked")) {
            $(".tmFilePrepHours").show();
            $("#tmFilePrepHours").attr("required", true);
            $("#tmFilePrepHours").val(0);
        } else {
            $(".tmFilePrepHours").hide();
            $("#tmFilePrepHours").val(0);
            $("#tmFilePrepHours").removeAttr('required');

        }
    });

    function processFileFormattingJson(jsonObject) {
        if ($("#formattingPagesPerHour").length > 0) {
            $("#formattingPagesPerHour").remove();
        }

        var pagesPerHour = Number(jsonObject.pages_per_hour);
        if (pagesPerHour > 0) {
            $(".document_formatting_row").show();
            $(".document_formatting").attr("required", true);
        } else {
            $(".document_formatting").removeAttr('required');
            $(".document_formatting_row").hide();
        }
        $('<input>').attr({
            type: 'hidden',
            id: 'formattingPagesPerHour',
            value: pagesPerHour
        }).appendTo('form');
    }
    
    $(function () {
        $("#slider-vertical-pm").slider({
            orientation: "vertical",
            range: "min",
            min: 0,
            max: 100,
            value: Number($('input[name="clientPMPercent"]').val()),
            step: 1,
            slide: function (event, ui) {
                $("#projectManagement").val(ui.value + '%');
                $("#pmpercent").val(ui.value);
                if($("#projectManagement").hasClass('pbPMCustomState')) {
                    $("#projectManagement").addClass('pbPMNormalState').removeClass('pbPMCustomState');
                }
            }
        });
        var startVal = $("#slider-vertical-pm").slider("value");
        $("#projectManagement").val(startVal + '%');
        if(startVal != '10') {
            $("#projectManagement").addClass('pbPMCustomState').removeClass('pbPMNormalState');
        }
        $("#pmpercent").val(startVal);
    });
    
    $("#formattingPageCount").on("change", function (event, ui) {
        var pagesPerHour = Number($('#formattingPagesPerHour').val());
        var pageCount = Number($(this).val());
        var qaPagesPerHour = Number($("#qaPagesPerHour").val());

        if (pagesPerHour != 0) {
            if (pageCount == 0) {
                $('#formattingHours').val(pageCount);
            } else if (pageCount <= pagesPerHour) {
                $('#formattingHours').val(1);
            } else {
                var hours = Math.ceil((pageCount / pagesPerHour) / .25) * .25;
                $('#formattingHours').val(hours);
            }
        } else {
            $('#formattingHours').val(0);
        }

        if (qaPagesPerHour != 0) {
            if (pageCount == 0) {
                $('#qaHours').val("no");
            } else if (pageCount <= qaPagesPerHour) {
                $('#qaHours').val("yes");
            } else {
                var hours = Math.ceil((pageCount / qaPagesPerHour) / .25) * .25;
                $('#qaHours').val("yes");
            }
        } else {
            $('#qaHours').val("no");
        }

    });

    $("#qaPagesPerHour").on("change", function (event, ui) {
        var pageCount = Number($('#formattingPageCount').val());
        var qaPagesPerHour = Number($(this).val());
        if (qaPagesPerHour != 0) {
            if (pageCount == 0) {
                $('#qaHours').val("no");
            } else if (pageCount <= qaPagesPerHour) {
                $('#qaHours').val("yes");
            } else {
                $('#qaHours').val("yes");
            }
        } else {
            $('#qaHours').val("no");
        }

    });    
});


