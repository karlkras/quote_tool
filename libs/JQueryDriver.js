$(document).ready(function () {
    var ajaxProcessingFile = '../quoteGen/ajax/taskChangeHandler.php';

    // some prototypes...
    String.prototype.isEmpty = function () {
        return (this.length === 0 || !this.trim());
    };

    $(document).on('change', ".margin", function () {
        $this = $(this);
        executeTaskChangePost('margin', $this);
    });

    $(document).on('change', ".rate", function () {
        $this = $(this);
        executeTaskChangePost('rate', $this);
        unlockRates();
    });

    $(document).on('change', ".units", function () {
        $this = $(this);
        executeTaskChangePost('units', $this);
    });

    $(document).on('change', ".pmpercent", function () {
        $this = $(this);
        executeTaskChangePost('pmpercent', $this);
    });

    function executeCategoryPrinter($element) {
        $.post(ajaxProcessingFile, buildCategoryPrinterArgs($element));
    }

    function executeTaskPrinter($element) {
        $.post(ajaxProcessingFile, buildTaskPrintArgs($element));
    }

    function buildCategoryPrinterArgs($element) {
        thisChecked = $element.is(":checked");
        theDivId = $element.closest("div").prop('id');
        var location;
        if (theDivId.indexOf('languageDiv') === 0) {
            location = theDivId.split("-")[1];
        } else {
            location = theDivId;
        }

        rollName = $element.attr('name');
        var category;
        switch (rollName) {
            case 'Linguisticrolled':
                category = 'Linguistic';
                break;
            case 'Engineeringrolled':
                category = 'Engineering';
                break;
            case 'Formattingrolled':
                category = 'Formatting';
                break;
            case 'Other_Servicesrolled':
                category = 'Other Services';
                break;
        }

        var theArgs = '{"function":"categoryPrinter", "print":' + '"' + thisChecked + '",' + '"category":' + '"' + category + '",' + '"location":' + '"' + location + '"}';
        var ret = JSON.parse(theArgs);
        return ret;
    }

    function executeTaskChangePost(name, $element) {
        if ($element.val()) {
            orgVal = $element.attr("name");
            if (orgVal.indexOf('$') != -1) {
                orgVal = orgVal.slice(1);
            }
            orgVal = parseFloat(orgVal);
            newVal = parseFloat($element.val());
            if (orgVal != newVal) {
                $sendData = buildTaskChangeArgs(name, $element);
                $.post(ajaxProcessingFile,
                        $sendData,
                        function (result) {
                            jsonObject = $.parseJSON(result);
                            processTaskChangeJson(jsonObject);
                        });
                "json";
            } else {
                $element.val($element.attr("name"));
            }
        }
    }

    function processTaskChangeJson(jsonData) {
        $.each(jsonData, function (index, element) {
            var name;
            for (name in element) {
                // if the item is either a task or total row we replace it entirely.
                if (name.search('task-') == 0) {
                    $targetElem = $('tr#' + name);
                    $sourceElem = $(element[name]);
                    doTaskUpdate($sourceElem, $targetElem);
                } else if (name.search('totalrow-') == 0) {
                    $targetElem = $('tr#' + name);
                    $sourceElem = $(element[name]);
                    doTotalRowUpdate($sourceElem, $targetElem);
                } else if (name.search('other_total_row') == 0) {
                    $targetElem = $('tr#' + name);
                    $sourceElem = $(element[name]);
                    doOtherTotalRowUpdate($sourceElem, $targetElem);
                } else {
                    // else we replace the html of the parent div...
                    $elem = $("div#" + name);
                    $elem.html(element[name]);
                }
            }
        });
    }


    function doTotalRowUpdate($source, $target) {
        // do the cost:
        $sourceCost = $source.find("[id^='cost-']");
        $targetCost = $target.find("[id^='cost-']");
        $targetCost.text($sourceCost.text());

        // do the perword stuff...
        $sourcePerword = $source.find("[id^='perword-']");
        $targetPerword = $target.find("[id^='perword-']");
        $targetPerword.val($sourcePerword.val());

        // do the actual sell price (asp) 
        $sourceASP = $source.find("[id^='asp-']");
        $targetASP = $target.find("[id^='asp-']");
        sourceParentTD = $sourceASP.closest('td');
        targetParentTD = $targetASP.closest('td');
        updateClasses(sourceParentTD, targetParentTD);
        $targetASP.val($sourceASP.val());

        // do the gross margin (gm) 
        $sourceGM = $source.find("[id^='gm-']");
        $targetGM = $target.find("[id^='gm-']");
        $targetGM.val($sourceGM.val());
    }

    function doOtherTotalRowUpdate($source, $target) {
        // do the cost:
        $sourceCost = $source.find(".cost");
        $targetCost = $target.find(".cost");
        if ($targetCost.text() != $sourceCost.text()) {
            $targetCost.text($sourceCost.text());
        }

        // do the actual sell price (asp) 
        $sourceASP = $source.find(".asp");
        $targetASP = $target.find(".asp");

        if ($targetASP.text() != $sourceASP.text()) {
            $targetASP.text($sourceASP.text());
        }

        // do the gross margin (gm) 
        $sourceGM = $source.find(".gross_margin");
        $targetGM = $target.find(".gross_margin");
        if ($targetGM.text() != $sourceGM.text()) {
            $targetGM.text($sourceGM.text());
        }
    }


    function showObject(obj) {
        var result = "";
        for (var p in obj) {
            if (obj.hasOwnProperty(p)) {
                result += p + " , " + obj[p] + "\n";
            }
        }
        return result;
    }

    function buildTaskChangeArgs(name, $element) {
        // make sure the value doesn't have a leading $ sign.
        theValue = $element.val();
        if (theValue.charAt(0) === '$') {
            theValue = theValue.slice(1);
        }

        var theArgs = '{"function":"taskchange", "taskId":' + '"' + getTaskId($element) + '",' + '"valueType":' + '"' + name + '",' + '"value":' + '"' + theValue + '"';
        if (getTaskFrame($element)) {
            theArgs += ',"targLang":' + '"' + getTaskFrame($element) + '"';
        }
        theArgs += '}';
        var ret = JSON.parse(theArgs)
        console.log(ret);

        return ret;
    }

    function buildTaskPrintArgs($element) {
        currentCheckState = $element.is(":checked");
        var theArgs = '{"function":"taskPrinter", "taskId":' + '"' + getTaskId($element) + '",' + '"print":' + '"' + currentCheckState + '"';
        if (getTaskFrame($element)) {
            theArgs += ',"targLang":' + '"' + getTaskFrame($element) + '"';
        }
        theArgs += '}';
        var ret = JSON.parse(theArgs)
        console.log(ret);

        return ret;
    }

    function doAlert(name, $element) {
        taskId = getTaskId($element);
        alert(name + " changed for task: " + taskId + "\nIn frame: " + getTaskFrame($element) + "\nWith value: " + $element.val());
    }
    


    $(document).on('click', ".task_rollup", function () {
        $this = $(this);
        var classList = $this[0].className.split(' ');
        console.log("Task Rollup hit for task# " + getTaskId($this));
        currentCheckState = $this.is(":checked");
        className = findItemInArray(classList, "group_");
        if (className) {
            console.log("class name found: " + className);
            id = "." + className;
            $(id).prop('checked', currentCheckState);
        }
//        $roller = getParentRoller($this);
//        if($roller.is(":checked") == currentCheckState) {
//            $roller.prop('checked', !currentCheckState);
//        }
        executeTaskPrinter($this);
        setParentRollerState($this);
    });

    function getParentRoller(elem) {
        var classList = elem[0].className.split(' ');
        className = findItemInArray(classList, "ChildRolled");
        var baseName = className.substr(0, className.indexOf("ChildRolled"));
        var lookupName = baseName + "rolled";
        var theDiv = elem.closest("div");
        return theMotherRoller = theDiv.find("." + lookupName);
    }

    function setParentRollerState($elem) {
        currentCheckState = $elem.is(":checked");
        var classList = $this[0].className.split(' ');
        className = findItemInArray(classList, "ChildRolled");
        var baseName = className.substr(0, className.indexOf("ChildRolled"));
        var lookupName = baseName + "rolled";
        var $theDiv = $elem.closest("div");
        var $theMotherRoller = $theDiv.find("." + lookupName);
        // get all of the child rollers in this category...
        var childElems = $theDiv.find("." + className);

        var allOn = areRolledChildernOn(childElems);
        var allOff = areRolledChildernOff(childElems);
        if (allOn || allOff) {
            //theMotherRoller.prop('checked', !currentCheckState);
            theMotherRollerState = $theMotherRoller.is(":checked");
            if (currentCheckState == theMotherRollerState) {
                $theMotherRoller.click();
            }
            //theMotherRoller.prop('checked', !currentCheckState);
        }
    }

    function areRolledChildernOn(childElems) {
        var on = true;
        $(childElems).each(function (  ) {
            if (!$(this).is(":checked")) {
                on = false;
                return false;
            }
        });
        return on;
    }

    function areRolledChildernOff(childElems) {
        var off = true;
        $(childElems).each(function (  ) {
            if ($(this).is(":checked")) {
                off = false;
                return false;
            }
        });
        return off;
    }

    $(document).on('click', ".bundle_efforts", function () {
        var className = "." + $(this).attr('value');
        currentCheckState = $(this).is(":checked");
        $(className).each(function () {
            innerClickState = $(this).is(":checked");
            if (innerClickState != currentCheckState) {
                //$(this).prop('checked', false);
                $(this).click();
            }
        });
    });
    
    $(document).on('click','.package_efforts',function(){
        var categoryID = $(this).val();
        var trimLen = 7; //length of 'package'
        var wholeLen = categoryID.length;
        var category = categoryID.substring(0,wholeLen - trimLen);
        var isChecked = $(this).is(":checked");
        var theArgs = JSON.parse('{"function":"packageBundle", "category":"'+category+'", "bundle":"'+isChecked+'"}');
        $.post(ajaxProcessingFile, theArgs);
    });

    $(document).on('click', "#UnlockRates", function () {
        if (confirm("Are you sure you want to unlock the Rate fields?")) {
            $(this).prop("disabled", true);
            unlockRates();
            $.post(ajaxProcessingFile, {ratesAreEditable: "true"});
        }
    });

    function unlockRates() {
        $rates = $('.rate');
        $rates.prop('readOnly', false);
        $rates.addClass('editable');
        $rates.removeAttr('tabIndex');
    }

    $(document).on('focus', ".wholeNumberOnly", function () {
        var $this = $(this);
        $this.attr("name", $this.val());
    });

    $(document).on('keypress', ".wholeNumberOnly", function (evt) {
//        var code = evt.keyCode || evt.which;
//        if (code == 9 || evt.shiftKey || evt.ctrlKey || code == 65 || code == 16 || code == 17) {
//            return;
//        }
        return isWholeNumber(evt, this);
    });

    $(document).on('change', ".wholeNumberOnly", function () {
        var $this = $(this);
        var $theVal = $this.val()
        // test if the field is empty and if it is, set it back to the
        // previous value...
        if (!$theVal || $theVal < 1 || $theVal.isEmpty()) {
            $this.val($this.attr("name"));
        }
    });

    $(document).on('focus', ".decimalOnly", function () {
        var $this = $(this);
        if ($this.val().charAt(0) === "$") {
            $this.val($this.val().slice(1));
        }
        $this.attr("name", $this.val());
    });

    $(document).on('change', ".decimalOnly", function () {
        var $this = $(this);
        var $theVal = $this.val();

        if (!$theVal || $theVal.isEmpty()) {
            $this.val($this.attr("name"));
        } else {

            if ($.isNumeric($theVal)) {
                if ($theVal.charAt($theVal.length - 1) == ".") {
                    $theVal = $theVal + "00";
                    $this.val($theVal);
                } else if ($theVal.indexOf(".") == -1) {
                    $theVal = $theVal + ".00";
                    $this.val($theVal);
                }
                // finally make sure we have a least 100s in decimal...
                check = $theVal.split(".");
                if (check[1].length < 2) {
                    $theVal += "0";
                    $this.val($theVal);
                }
            }
        }
    });

    $(document).on('change', ".asppu", function () {
        $this = $(this);
        if ($this.val() && !$this.val().isEmpty()) {
            //check to see this value is the same as the previous...
            var priorVal = Number($this.attr("name"));
            var newVal = Number($this.val());

            if (priorVal !== newVal) {
                // check to see if this is a custom setting...
                $tdElem = $this.closest("td");
                if ($tdElem.hasClass("customindicator")) {
                    if (confirm("This task uses a custom selling price.\nAre you sure you want to change it?")) {
                        executeTaskChangePost('asppu', $this);
                    } else {
                        $this.val($this.attr('name'));
                        return;
                    }
                } else {
                    executeTaskChangePost('asppu', $this);
                }
            } else {
                $this.val($this.attr('name'));
            }
        } else {
            $this.val($this.attr('name'));
        }
    });



    $(document).on('keyup', '.decimalOnly', function (evt) {
        var code = evt.keyCode || evt.which;
        if (evt.shiftKey || evt.ctrlKey || code == 65
                || code > 7 && code < 47) {
            return;
        }

        var val = $(this).val();
        if (isNaN(val)) {
            val = val.replace(/[^0-9\.]/g, '');
            if (val.split('.').length > 2) {
                val = val.replace(/\.+$/, "");
            }
            if (val.charAt(0) == ".") {
                val = "0" + val;
            }
        }
        $(this).val(val);
    });

    function isWholeNumber(evt, element) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if ((charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    $(document).on('click', '.printroller', function () {
        // we need to get all of the input tasks in this category...
        thisChecked = $(this).is(":checked");

        theDiv = $(this).closest("div");
        var elems;
        rollName = $(this).attr('name');
        if (rollName === "Linguisticrolled") {
            elems = $(theDiv).find(".LinguisticChildRolled");
        } else if (rollName === "Engineeringrolled") {
            elems = $(theDiv).find(".EngineeringChildRolled");
        } else if (rollName === "Other_Servicesrolled") {
            elems = $(theDiv).find(".Other_ServicesChildRolled");
        } else if (rollName === "Formattingrolled") {
            elems = $(theDiv).find(".FormattingChildRolled");
        }

        $(elems).prop('checked', !thisChecked);

        executeCategoryPrinter($(this));
    });


    function getTaskId(element) {
        $theId = element.closest('tr').attr('id');
        return  $theId;
    }

    function getTaskFrame(element) {
        // there are 
        $frameDivElement = element.closest('div').parent().closest('div');
        return $frameDivElement.attr('id');
    }

    function findItemInArray(theArray, item) {
        for (x in theArray) {
            console.log(theArray[x]);
            if (theArray[x].indexOf(item) != -1) {
                return theArray[x];
            }
        }
        return 0;
    }

    function parseTaskName(theName) {
        theSpitString = theName.split("-");
        return theSpitString;
    }


    // functions for updaing dom element from stuff returned from backend...




    function doTaskUpdate(sourceTask, targetTask) {

        updateRoller(sourceTask, targetTask);
        updateUnitCount(sourceTask, targetTask);
        updateRate(sourceTask, targetTask);
        updateCosts(sourceTask, targetTask);
        updatePMPercent(sourceTask, targetTask);
        updateMargin(sourceTask, targetTask);
        updateCalcSellPricePerUnit(sourceTask, targetTask);
        updateActualSellPricePerUnit(sourceTask, targetTask);
        updateActualSellPrice(sourceTask, targetTask);
        updateGrossMargin(sourceTask, targetTask);
    }


    function updateRoller($source, $target) {
        sourceRollup = $source.find('.task_rollup');

        if (sourceRollup.length !== 0) {
            targetRollup = $target.find('.task_rollup')
            targetRollup.prop('checked', sourceRollup.is(':checked'));
            updateClasses(sourceRollup, targetRollup);
            updateReadOnly(sourceRollup, targetRollup);
        }
    }

    function updateUnitCount($source, $target) {
        sourceCount = $source.find('.unitcount');
        if (sourceCount.length !== 0) {
            targetCount = $target.find('.unitcount');

            targetCount.val(sourceCount.val());
        }
    }

    function updateRate($source, $target) {
        sourceRate = $source.find('.rate');
        if (sourceRate.length !== 0) {
            targetRate = $target.find('.rate');
            targetRate.val(sourceRate.val());

            updateClasses(sourceRate, targetRate);
            updateReadOnly(sourceRate, targetRate);
        }
    }

    function updateCosts($source, $target) {
        sourceCost = $source.find('.cost');
        if (sourceCost.length !== 0) {
            targetCost = $target.find('.cost');

            sourceParentTD = sourceCost.closest('td');
            targetParentTD = targetCost.closest('td');

            updateClasses(sourceParentTD, targetParentTD);
            targetCost.val(sourceCost.val());
            updateTitle(sourceCost, targetCost);
            updateClasses(sourceCost, targetCost);
            updateReadOnly(sourceCost, targetCost);
        }
    }

    function updatePMPercent($source, $target) {
        sourcePMP = $source.find('.pmpercent');
        if (sourcePMP.length !== 0) {
            targetPMP = $target.find('.pmpercent');
            targetPMP.val(sourcePMP.val());
            updateClasses(sourceCost, targetCost);
            updateReadOnly(sourceCost, targetCost);
        }
    }

    function updateMargin($source, $target) {
        sourceMargin = $source.find('.margin');
        if (sourceMargin.length !== 0) {
            targetMargin = $target.find('.margin');
            targetMargin.val(sourceMargin.val());
            updateClasses(sourceMargin, targetMargin);
            updateReadOnly(sourceMargin, targetMargin);
        }

    }

    function updateCalcSellPricePerUnit($source, $target) {
        sourceCSPPU = $source.find('.csppu');
        if (sourceCSPPU.length !== 0) {
            targetCSPPU = $target.find('.csppu');

            targetCSPPU.val(sourceCSPPU.val());

            updateClasses(sourceCSPPU, targetCSPPU);
            updateReadOnly(sourceCSPPU, targetCSPPU);
        }
    }

    function updateActualSellPricePerUnit($source, $target) {
        sourceASPPU = $source.find('.asppu');
        if (sourceASPPU.length !== 0) {
            targetASPPU = $target.find('.asppu');
            sourceParentTD = sourceASPPU.closest('td');
            targetParentTD = targetASPPU.closest('td');
            updateClasses(sourceParentTD, targetParentTD);
            targetASPPU.val(sourceASPPU.val());
            updateTitle(sourceASPPU, targetASPPU);
            updateClasses(sourceASPPU, targetASPPU);
            updateReadOnly(sourceASPPU, targetASPPU);
        }
    }

    function updateActualSellPrice($source, $target) {
        sourceASP = $source.find('.asp');
        if (sourceASP.length !== 0) {
            targetASP = $target.find('.asp');
            sourceParentTD = sourceASP.closest('td');
            targetParentTD = targetASP.closest('td');
            targetASP.val(sourceASP.val());
            updateClasses(sourceParentTD, targetParentTD);
            updateTitle(sourceASP, targetASP);
            updateClasses(sourceASP, targetASP);
            updateReadOnly(sourceASP, targetASP);
        }
    }

    function updateGrossMargin($source, $target) {
        sourceGMP = $source.find('.gmp');
        if (sourceGMP.length !== 0) {
            targetGMP = $target.find('.gmp');
            targetGMP.text(sourceGMP.text());
        }
    }


// a few helpers...

    function updateClasses($sourceElem, $targetElem) {
        theClasses = $sourceElem.attr('class');
        if (theClasses) {
            theClasses = theClasses.trim();
        }
        $targetElem.attr('class', theClasses);
    }

    function updateReadOnly($sourceElem, $targetElem) {
        if ($sourceElem.attr('readonly')) {
            $targetElem.attr('readonly', true);
        }
    }

    function updateTitle($sourceElem, $targElem) {
        title = $sourceElem.attr('title');
        if (title) {
            $targElem.attr('title', title);
        }
    }

});