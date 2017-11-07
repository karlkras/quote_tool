<?PHP

//session_start();
include_once('classes/ProjectManager.php');

function save_to_xml($taskNameArray, $rolledNameArray, $updateAtTask, $projectManager) {
    ob_start();
    $filename = export_xml($taskNameArray, $rolledNameArray, $projectManager, $updateAtTask);

    if ($updateAtTask) {
        $projectManager->saveToWorkfront();
    }
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    ob_end_flush();
}

/* * *****************************************************************
 * NAME :            export_xml($taskService, $projectObj, $thisProject, $projectData, $langCount)
 *
 * DESCRIPTION :     Exports the dataset to an xml document in a format
 * 					suitable for import into an Acrobat PDF form document.
 * 					Also provides a "data dump" of all current object that
 * 					can later be read into the program.
 *
 * INPUTS :
 *       PARAMETERS:
 *           OBJ     taskService				An instance of the SOAP client's taskService class
 * 											that describes all the tasks associated with a project.
 * 			OBJ		projectObj				An instance of the SOAP client's project class that
 * 											contains the information about the current project.
 * 			ARR		thisProject				An array of various tasks, based on the taskService,
 * 											that contains extra information not available in the
 * 											taskService object
 * 			OBJ		projectData				An instance of the projectData class that contains
 * 											extra information note available in the projectObj
 * 			INT		langCount				The number of languages in the project
 *
 *       GLOBALS :
 *           none
 *
 * *************************************************************************** */

function export_xml($taskNameArray, $rolledNameArray, $projectManager, $updateAtTask) {
    $doc = $projectManager->exportXml($taskNameArray, $rolledNameArray, $updateAtTask);

    $filename = $projectManager->getProjectObj()->name . "_Quote.xml";
    $filename = sanitizeFilename($filename);

    

    echo $doc->saveXML() . "\n";
    return $filename;
}

function requestedServices($taskService, $packageEng, $packageAll) {
    $langServs = array();
    $langServs['ids'] = array();
    $langServs['name'] = array();
    $index = 0;

    $lingArray = array();
    //do TR+CE first so it's in the right order
    foreach ($taskService->lingTasks as $lingt) {
        if ($lingt->ltask->type == 'TR+CE') {
            array_push($lingArray, $lingt);
        }
    }
    // and now...everything else
    foreach ($taskService->lingTasks as $lingt) {
        if ($lingt->ltask->type != 'TR+CE') {
            array_push($lingArray, $lingt);
        }
    }

    foreach ($lingArray as $lingTask) {
        if ($lingTask->ltask->price > 0) {
            if (!in_array($lingTask->ltask->type, $langServs['name'])) {
                $langServs['ids'][$index][] = $lingTask->ltask->id;
                $langServs['name'][$index] = $lingTask->ltask->type;
                $index++;
            } else {
                //find the array index that corresponds to this name
                $idx = array_search($lingTask->ltask->type, $langServs['name']);
                $langServs['ids'][$idx][] = $lingTask->ltask->id;
            }
        }
    }


    foreach ($taskService->billableTasks as $billTask) {
        if (($packageAll) && ($billTask->btask->name != 'Project Management')) {
            if ($billTask->btask->price > 0) {
                getPackagedServicesAll($billTask, $langServs, $index);
            }
        } elseif ($billTask->btask->price > 0) {
            if ($packageEng) { //check for bundled engineering
                getPackagedServicesEng($billTask, $langServs, $index);
            } else {
                getStandardServices($billTask, $langServs, $index);
            }
        }
    }

    //rename the items to more friendsly names
    foreach ($langServs['name'] as $indx => $name) {
        switch ($name) {
            case 'TR': $langServs['name'][$indx] = 'Translation';
                break;
            case 'CE': $langServs['name'][$indx] = 'Copy Editing';
                break;
            case 'TR+CE': $langServs['name'][$indx] = 'Translation and Copyediting';
                break;
            case 'PR': $langServs['name'][$indx] = 'Proofreading';
                break;
            case 'OLR': $langServs['name'][$indx] = 'Online Review';
                break;
            case 'Format': $langServs['name'][$indx] = 'Formatting';
                break;
            case 'Format 1': $langServs['name'][$indx] = 'Formatting';
                break;
            case 'Format 2': $langServs['name'][$indx] = 'Formatting';
                break;
            case 'Format 3': $langServs['name'][$indx] = 'Formatting';
                break;
            case 'QA': $langServs['name'][$indx] = 'Quality Assurance Review';
                break;
            case 'TM Work': $langServs['name'][$indx] = 'Translation Memory Maintenance';
                break;
            case 'TM Work 1': $langServs['name'][$indx] = 'Translation Memory Maintenance';
                break;
            case 'TM Work 2': $langServs['name'][$indx] = 'Translation Memory Maintenance';
                break;
        }
    }
    return $langServs;
}

function getPackagedServicesAll($task, &$langServs, &$index) {
    if ($task->distributionStrategy != 'not distributed') {
        if (!in_array('Format', $langServs['name'])) {
            $langServs['name'][$index] = 'Format';
            $langServs['ids'][$index][] = $task->btask->id;
            $index++;
        } else {
            $idx = array_search('Format', $langServs['name']);
            $langServs['ids'][$idx][] = $task->btask->id;
        }
    } else {
        getStandardServices($task, $langServs, $index);
    }
}

function getPackagedServicesEng($task, &$langServs, &$index) {
    if ($task->distributionStrategy != 'not distributed') {
        if ($task->btask->type == "Localization Engineer") {
            if (!in_array('Format', $langServs['name'])) {
                $langServs['name'][$index] = 'Format';
                $langServs['ids'][$index][] = $task->btask->id;
                $index++;
            } else {
                $idx = array_search('Format', $langServs['name']);
                $langServs['ids'][$idx][] = $task->btask->id;
            }
        } else {
            getStandardServices($task, $langServs, $index);
        }
    } else {
        getStandardServices($task, $langServs, $index);
    }
}

function getStandardServices($task, &$langServs, &$index) {
    if (!in_array($task->btask->name, $langServs['name'])) {
        if (($task->btask->name[strlen($task->btask->name) - 1] == '0') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '1') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '2') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '3') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '4') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '5') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '6') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '7') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '8') ||
                ($task->btask->name[strlen($task->btask->name) - 1] == '9')) {
            if (!in_array(substr($task->btask->name, 0, strlen($task->btask->name) - 2), $langServs['name'])) {
                $langServs['name'][$index] = substr($task->btask->name, 0, strlen($task->btask->name) - 2); //add it to the array 
                $langServs['ids'][$index][] = $task->btask->id;
                $index++;
            } else {
                //find the array index that corresponds to this name
                $idx = array_search(substr($task->btask->name, 0, strlen($task->btask->name) - 2), $langServs['name']);
                $langServs['ids'][$idx][] = $task->btask->id;
            }
        } else {
            //don't want final format or any coordination task to show up, so check for those
            if (($task->btask->name != 'Final Format') && (stristr($task->btask->name, 'coord') == FALSE)) {
                $langServs['name'][$index] = $task->btask->name;
                $langServs['ids'][$index][] = $task->btask->id;
                $index++;
            } else {
                //find the array index that corresponds to this name
                $idx = array_search($task->btask->name, $langServs['name']);
                $langServs['ids'][$idx][] = $task->btask->id;
            }
        }
    } else {
        $idx = array_search($task->btask->name, $langServs['name']);
        $langServs['ids'][$idx][] = $task->btask->id;
    }
}

function sanitizeFilename($inputStr) {
    $extension = strrchr($inputStr, '.');
    $basename = substr($inputStr, 0, (strlen($inputStr)) - (strlen($extension)));
    $basename = strip_tags($basename);
    $basename = preg_replace('/[\r\n\t]+/', '', $basename);
    $basename = str_replace('&', ' and ', $basename);
    $basename = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $basename);
    $basename = html_entity_decode($basename, ENT_QUOTES, "utf-8");
    //$basename = htmlentities($basename, ENT_QUOTES, "utf-8");
    $basename = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $basename);
    $basename = str_replace(' ', '-', $basename);
    //$basename = rawurlencode($basename);
    $basename = str_replace('%', '-', $basename);

    return $basename . $extension;
}
