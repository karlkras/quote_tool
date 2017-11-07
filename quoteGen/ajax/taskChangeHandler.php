<?php

require_once(__DIR__ . '/../classes/ProjectManager.php');
session_start();

if(isset($_POST['function'])){
    $function = $_POST['function'];
    ob_start();
    
    if($function == "taskchange"){
        $projectManager = \unserialize($_SESSION['projectManager']);
        $taskId = $_POST['taskId'];
        $valueType = $_POST['valueType'];
        $value = $_POST['value'];
        $targLang = null;

        if(isset($_POST['targLang'])){
            $targLang = $_POST['targLang'];
        }
    
        if($valueType == 'pmpercent') {
            $projectManager->setPMTaskPercent($taskId, $value, $targLang);
        } else {
            $projectManager->setValueItemOnTask($taskId, $valueType . "_" . $value, $targLang);
        }
        // store back the changes...
        $_SESSION['projectManager'] = serialize($projectManager);

        $theEncoding = json_encode($projectManager );
        ob_clean();

        echo $theEncoding; 
    } elseif($function == "categoryPrinter") {
        $projectManager = \unserialize($_SESSION['projectManager']);
        $print = $_POST['print'];
        $category = $_POST['category'];
        $location = $_POST['location'];
        
        $projectManager->categoryPrinter($print, $category, $location);
        
        // store back the changes...
        $_SESSION['projectManager'] = serialize($projectManager);
        
        ob_clean();
        
    } elseif($function == "taskPrinter") {
        $projectManager = \unserialize($_SESSION['projectManager']);
        $taskId = $_POST['taskId'];
        $print = $_POST['print'];
        $targLang = null;

        if(isset($_POST['targLang'])){
            $targLang = $_POST['targLang'];
        }
        
        $projectManager->taskPrinter($taskId, $print, $targLang);
        
        // store back the changes...
        $_SESSION['projectManager'] = serialize($projectManager);
        
        ob_clean();
        
    } elseif($function == "packageBundle"){
        $projectManager = \unserialize($_SESSION['projectManager']);
        $category = $_POST['category'];
        $isChecked = $_POST['bundle'];
        
        switch($category){
            case 'Engineering':
                $projectManager->setPackageEngineering($isChecked);
                break;
            case 'AllInternal':
                $projectManager->setPackageAllInternal($isChecked);
                break;
        }
        
        $_SESSION['projectManager'] = serialize($projectManager);
        ob_clean();
    }
} elseif(isset($_POST['ratesAreEditable'])){
    $_SESSION['ratesAreEditable'] = "true";
}


    


