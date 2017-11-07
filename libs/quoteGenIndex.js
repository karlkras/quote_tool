// since the normal process for tool use is to hit
// the browser's back button to return to the main page
// we need to force the form to reset defaults and then
// rerun the scheme change to sync it up.
$(window).bind("pageshow", function() {
    var form = $('#project_form'); 
    // let the browser natively reset defaults
    form[0].reset();
    
    $("#project_form :input").attr("disabled","disabled");
    $("#project").removeAttr("disabled");
    $('input[name="Load"]').removeAttr("disabled");
});