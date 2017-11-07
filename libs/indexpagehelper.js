$(document).ready(function () {

    $('#radio1').attr('checked', true);
    $('#norushfee_radio').attr('checked', true);
    $('select#project>option:eq(0)').prop('selected', true);


    // jQuery plugin to prevent double submission of forms
    $.fn.preventDoubleSubmission = function () {

        $(this).on('submit', function (e) {
            var $form = $(this);

            if ($form.data('submitted') === true) {
                // Previously submitted - don't submit again
                e.preventDefault();
            } else {
                // Mark it so that the next submit can be ignored
                $form.data('submitted', true);
            }
        });
        // Keep chainability
        return this;
    };

    $("select#project").bind('change', function (e) {
        if ($(this).val() != '0') {
            enablePage();
            setDefaults();
            changeScheme();
        } else {
            window.location.reload();
        }
    });
    
    $("#discountAmount").on('change', function () {
        if($(this).hasClass('customprice')) {
            $(this).removeClass('customprice');
        }
    });

    function setDefaults() {
        $('#radio1').prop('checked', true);
        $('#norushfee_radio').prop('checked', true);
        $('#noQARequired_id').prop('checked', true);
        changeQA("no");
        $("#cycleOther").val('');
        $("#numpages").val('');
        $("#pagesPerHour").val('');
        $("select[name='cycle']>option:eq(0)").prop('selected', true);
        changeCycle("sdfsdf");
        $("input[name='estDeliveryDate']").val('');
    }

    $("form[name='project']").preventDoubleSubmission();

//    $("form[name='project']").submit(function (event) {
//        event.preventDefault();
//        if ($("select#project").val() != '0') {
//            return true;
//        } else {
//            return false;
//        }
//    });

    function enablePage() {
        $("#project_form :input").removeAttr("disabled", "disabled");
        $('input[name="rushFees"]').removeAttr('disabled', 'disabled');
        $('input[name="proofReading"]').removeAttr('disabled', 'disabled');
        $('input[name="discountType"]').removeAttr('disabled', 'disabled');
        $('input[name="pmMinPerLanguage"]').removeAttr('disabled', 'disabled');
        $('input[name="qaRequired"]').removeAttr('disabled', 'disabled');
    }
});

function changeQA(qa)
{
    if (qa === 'yes') {
        document.getElementById('pagesRow').style.display = '';
        document.getElementById('numpages').disabled = '';
        document.getElementById('rateRow').style.display = '';
        document.getElementById('pagesPerHour').disabled = '';
    } else {
        document.getElementById('pagesRow').style.display = 'none';
        document.getElementById('numpages').disabled = 'disabled';
        document.getElementById('rateRow').style.display = 'none';
        document.getElementById('pagesPerHour').disabled = 'disabled';
    }
}

function changeCycle(cycle)
{
    if (cycle === "Progress") {
        document.getElementById('cycleOther').disabled = false;
    } else {
        document.getElementById('cycleOther').disabled = true;
    }
}


