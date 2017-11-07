<?PHP

function update_taskService($projectManager) {
    require_once (__DIR__ . '/../classes/ProjectManager.php');
    $projectManager->updateTaskService();
}
