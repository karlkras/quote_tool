<?php
class updateCategory {
  public $taskID; // int
  public $isTrados; // boolean
}

class updateCategoryResponse {
  public $return; // int
}

class processFaultBean {
  public $message; // string
}

class createLinguistInformation {
  public $userObject; // user
  public $isLinguist; // boolean
}

class user {
  public $firstName; // string
  public $lastName; // string
  public $address; // string
  public $address2; // string
  public $city; // string
  public $country; // string
  public $state; // string
  public $postalCode; // string
  public $email; // string
  public $scheduleID; // string
  public $email2; // string
  public $emailSecondaryAddresses; // string
  public $phone; // string
  public $userName; // string
  public $title; // string
  public $roles; // string
  public $company; // company
  public $timeZone; // string
  public $llsClientID; // string
  public $billingInstructions; // string
  public $billingEmail; // string
  public $os; // string
  public $linguistSource; // string
  public $sourceLanguage; // string
  public $targetLanguage; // string
  public $translatorSince; // string
  public $altPhone1; // string
  public $altPhone2; // string
  public $fax; // string
  public $pager; // string
  public $taxID; // string
  public $certificationDetails; // string
  public $adminNotes; // string
  public $pmComments; // string
  public $agreements; // string
  public $memberships; // string
  public $certifications; // string
  public $specialties; // string
  public $tools; // string
  public $qualified; // string
  public $copyedit; // double
  public $hourly; // double
  public $min; // double
  public $trReps; // double
  public $trFuzzy; // double
  public $trNew; // double
  public $trceReps; // double
  public $trceFuzzy; // double
  public $trceNew; // double
  public $mktReps; // double
  public $mktFuzzy; // double
  public $mktNew; // double
  public $medicalReps; // double
  public $medicalFuzzy; // double
  public $medicalNew; // double
  public $techReps; // double
  public $techFuzzy; // double
  public $techNew; // double
  public $uiReps; // double
  public $uiFuzzy; // double
  public $uiNew; // double
  public $helpReps; // double
  public $helpFuzzy; // double
  public $helpNew; // double
  public $rushReps; // double
  public $rushFuzzy; // double
  public $rushNew; // double
  public $lingoNetUser; // boolean
  public $legalEntity; // string
  public $clientPO; // string
  public $clientID1; // string
  public $clientID2; // string
  public $clientID3; // string
}

class baseAtTaskObject {
  public $id; // int
  public $name; // string
}

class company {
  public $docTransPricingScheme; // string
  public $paymentTerms; // int
  public $dtBucket; // string
  public $commissionType; // string
  public $addPMSurchargeforDocTrans; // boolean
  public $usLinguistsRequired; // boolean
  public $passTradosLeveraging; // boolean
  public $checkKnowledgeMgt; // boolean
  public $clientID; // int
  public $userDataID; // string
  public $greatPlainsID; // string
  public $prospect; // string
  public $llsClientID; // string
  public $billingInstructions; // string
  public $billingEmail; // string
  public $legalEntity; // string
  public $legalEntityCompany; // string
  public $specialInstructions; // string
  public $abbreviation; // string
  public $guid; // string
}

class createLinguistInformationResponse {
  public $return; // user
}

class getLingoStaff {
}

class getLingoStaffResponse {
  public $return; // userService
}

class userService {
  public $projectManagers; // user
  public $salesReps; // user
}

class projectAwaitingClientApproval {
  public $ProjectID; // int
}

class projectAwaitingClientApprovalResponse {
}

class createLNTask {
  public $LNTsk; // lnTask
}

class lnTask {
  public $assignedToID; // int
  public $fullMatchWords; // int
  public $fuzzyMatchWords; // int
  public $hours; // double
  public $lingoNetProjectID; // int
  public $newWords; // int
  public $projectID; // int
  public $sourceLang; // string
  public $targetLang; // string
  public $taskID; // int
  public $taskType; // string
  public $trados; // boolean
}

class createLNTaskResponse {
  public $return; // int
}

class completeTask {
  public $TaskID; // int
}

class completeTaskResponse {
}

class updateProjectPricing {
  public $projectObject; // project
  public $tskService; // taskService
}

class project {
  public $company; // company
  public $contact; // user
  public $sponsor; // user
  public $portfolio; // portfolio
  public $type; // string
  public $pricingScheme; // string
  public $budget; // double
  public $fileInventory; // string
  public $fileCount; // double
  public $pageCount; // double
  public $wordCount; // double
}

class portfolio {
}

class taskService {
  public $lingTasks; // linguistTask
  public $billableTasks; // billableTask
  public $projID; // int
  public $rushFee; // double
  public $discount; // double
  public $minimum; // double
}

class linguistTask {
  public $ltask; // task
  public $sourceLang; // string
  public $targLang; // string
  public $categoryName; // string
  public $wordRateDetails; // linguistRateDetails
  public $wordCounts; // wordCounts
  public $lingCosts; // lingCosts
}

class task {
  public $price; // double
  public $projectID; // int
  public $type; // string
  public $workRequired; // double
}

class linguistRateDetails {
  public $copyEdit; // double
  public $defaultToMinimum; // boolean
  public $hourly; // double
  public $minimum; // double
  public $name; // string
  public $sourceLanguage; // string
  public $targetLanguage; // string
  public $tr_100Match; // double
  public $tr_Help_100Match; // double
  public $tr_Help_fuzzy; // double
  public $tr_Help_new; // double
  public $tr_Marketing_100Match; // double
  public $tr_Marketing_fuzzy; // double
  public $tr_Marketing_new; // double
  public $tr_Medical_100Match; // double
  public $tr_Medical_fuzzy; // double
  public $tr_Medical_new; // double
  public $tr_Rush_100Match; // double
  public $tr_Rush_fuzzy; // double
  public $tr_Rush_new; // double
  public $tr_Technical_100Match; // double
  public $tr_Technical_fuzzy; // double
  public $tr_Technical_new; // double
  public $tr_UI_100Match; // double
  public $tr_UI_fuzzy; // double
  public $tr_UI_new; // double
  public $tr_fuzzy; // double
  public $tr_new; // double
  public $trce_100Match; // double
  public $trce_Help_100Match; // double
  public $trce_Help_fuzzy; // double
  public $trce_Help_new; // double
  public $trce_Marketing_100Match; // double
  public $trce_Marketing_fuzzy; // double
  public $trce_Marketing_new; // double
  public $trce_Medical_100Match; // double
  public $trce_Medical_fuzzy; // double
  public $trce_Medical_new; // double
  public $trce_Rush_100Match; // double
  public $trce_Rush_fuzzy; // double
  public $trce_Rush_new; // double
  public $trce_Technical_100Match; // double
  public $trce_Technical_fuzzy; // double
  public $trce_Technical_new; // double
  public $trce_UI_100Match; // double
  public $trce_UI_fuzzy; // double
  public $trce_UI_new; // double
  public $trce_fuzzy; // double
  public $trce_new; // double
  public $US_based_100Match; // double
  public $US_based_fuzzy; // double
  public $US_based_hourly; // double
  public $US_based_new; // double
  public $UStr_based_100Match; // double
  public $UStr_based_fuzzy; // double
  public $UStr_based_new; // double
}

class wordCounts {
  public $formattingHours; // double
  public $fuzzyWords; // int
  public $matchRepsWords; // int
  public $newWords; // int
  public $wordCount; // int
}

class lingCosts {
  public $fuzzyCost; // double
  public $hourlyCost; // double
  public $matchRepCost; // double
  public $minimumCost; // double
  public $newCost; // double
}

class billableTask {
  public $btask; // task
  public $hourlyRate; // double
  public $distributionStrategy; // string
}

class updateProjectPricingResponse {
}

class getUsersByCompanyID {
  public $companyID; // int
}

class getUsersByCompanyIDResponse {
  public $return; // user
}

class getSupportedLanguages {
  public $lingAccount; // linguistAccount
}

class linguistAccount {
  public $address; // string
  public $address2; // string
  public $city; // string
  public $contactData; // user
  public $email; // string
  public $email2; // string
  public $firstName; // string
  public $groupFilter; // string
  public $ID; // int
  public $lastName; // string
  public $name; // string
  public $paymentTerms; // int
  public $phone; // string
  public $postalCode; // string
  public $roles; // string
  public $state; // string
  public $userName; // string
}

class getSupportedLanguagesResponse {
  public $return; // languagePair
}

class languagePair {
  public $sourceLang; // string
  public $targetLang; // string
}

class validateUserRole {
  public $UserName; // string
  public $UserRole; // string
}

class validateUserRoleResponse {
  public $return; // boolean
}

class getUserByUsername {
  public $accountName; // string
}

class getUserByUsernameResponse {
  public $return; // user
}

class updateLinguistAccount {
  public $accountObject; // linguistAccount
}

class updateLinguistAccountResponse {
}

class equals {
  public $arg0; // anyType
}

class equalsResponse {
  public $return; // boolean
}

class getProjectFinancial {
  public $projectID; // int
}

class getProjectFinancialResponse {
  public $return; // projectFinancial
}

class projectFinancial {
  public $plannedRevenue; // double
  public $projectID; // int
  public $projectName; // string
  public $tasks; // taskFinancial
}

class taskFinancial {
  public $actualCost; // double
  public $taskID; // int
  public $taskName; // string
}

class createAudioTask {
  public $projectID; // int
  public $AudioTaskInfo; // audioInfo
}

class audioInfo {
  public $hourlyBudgetedRate; // double
  public $linguisticHrs; // double
}

class createAudioTaskResponse {
}

class getLinguistCompanies {
}

class getLinguistCompaniesResponse {
  public $return; // company
}

class loadClient {
  public $firstName; // string
  public $lastName; // string
}

class loadClientResponse {
}

class resourceNotExistFaultBean {
  public $message; // string
}

class getQuotableProjects {
}

class getQuotableProjectsResponse {
  public $return; // projectStub
}

class projectStub {
}

class getTaskService {
  public $projStub; // projectStub
}

class getTaskServiceResponse {
  public $return; // taskService
}

class getInternalStandardRates {
  public $roleNames; // string
}

class getInternalStandardRatesResponse {
  public $return; // internalCostDetails
}

class internalCostDetails {
  public $billHourly; // double
  public $costHourly; // double
  public $name; // string
}

class createUser {
  public $userObject; // user
}

class createUserResponse {
  public $return; // user
}

class getClientCompanyView {
}

class getClientCompanyViewResponse {
  public $return; // clientCompanyView
}

class clientCompanyView {
  public $addPMSurchargeForDocTrans; // customParameter
  public $docTransPricingScheme; // customParameter
  public $doctransClientNumber; // customParameter
  public $dtBucket; // customParameter
  public $legalEntity; // customParameter
  public $legalEntityCompany; // customParameter
  public $lingoDBClientNumber; // customParameter
  public $paymentTerms; // customParameter
}

class customParameter {
  public $dataType; // int
  public $defaultBooleanValue; // boolean
  public $defaultListValue; // listItem
  public $defaultTextValue; // string
  public $hasDefaultValue; // boolean
  public $listOptions; // listItem
  public $listUnhiddenOptions; // listItem
  public $name; // string
  public $viewType; // int
}

class listItem {
  public $hidden; // boolean
  public $label; // string
  public $value; // string
}

class getLingoUsers {
}

class getLingoUsersResponse {
  public $return; // user
}

class getLinguistAccount {
  public $accountName; // string
}

class getLinguistAccountResponse {
  public $return; // linguistAccount
}

class getLingoNETUsers {
}

class getLingoNETUsersResponse {
  public $return; // user
}

class getLinguistStandardRates {
  public $langPairs; // languagePair
}

class getLinguistStandardRatesResponse {
  public $return; // linguistRateDetails
}

class getLingoNETLinguistAccounts {
}

class getLingoNETLinguistAccountsResponse {
  public $return; // linguistAccount
}

class updateUser {
  public $userObject; // user
}

class updateUserResponse {
}

class getAccessLevelByUsername {
  public $accountName; // string
}

class getAccessLevelByUsernameResponse {
  public $return; // string
}

class createLinguistContact {
  public $userObject; // user
}

class createLinguistContactResponse {
  public $return; // user
}

class projectCurrent {
  public $ProjectID; // int
}

class projectCurrentResponse {
}

class getLibraryTaskService {
}

class getLibraryTaskServiceResponse {
  public $return; // taskService
}

class getJAS {
  public $taskID; // int
}

class getJASResponse {
  public $return; // string
}

class completeProject {
  public $ProjectID; // int
}

class completeProjectResponse {
  public $return; // boolean
}

class createFileTreatmentTask {
  public $projectID; // int
  public $fileTreatmentTaskInfo; // fileTreatmentInfo
}

class fileTreatmentInfo {
  public $capOrFixedAmt; // double
  public $workRequired; // int
}

class createFileTreatmentTaskResponse {
}

class createLNEstimateProject {
  public $LNProj; // lnProject
}

class lnProject {
  public $customIdMap; // customIdMap
  public $deliveryFmt; // string
  public $docTrans; // boolean
  public $ID; // int
  public $instructions; // string
  public $legalEntity; // string
  public $name; // string
  public $ownerID; // int
  public $portfolioID; // int
  public $reqClientID; // int
  public $sourceFmt; // string
  public $sourceLang; // string
  public $sponsorID; // int
  public $targetLang; // string
}

class customIdMap {
  public $entry; // entry
}

class entry {
  public $key; // string
  public $value; // string
}

class createLNEstimateProjectResponse {
  public $return; // int
}

class getClientCompany {
  public $companyName; // int
}

class getClientCompanyResponse {
  public $return; // company
}

class getGuid {
  public $legacyID; // int
}

class getGuidResponse {
  public $return; // string
}

class createTaskWithEmail {
  public $LNTsk; // lnTask
  public $email; // string
}

class createTaskWithEmailResponse {
  public $return; // int
}

class getClient {
  public $clientID; // int
}

class getClientResponse {
  public $return; // clientStub
}

class clientStub {
  public $companyID; // int
  public $companyName; // string
}

class userStub {
  public $firstName; // string
  public $lastName; // string
}

class getLinguistView {
}

class getLinguistViewResponse {
  public $return; // linguistView
}

class linguistView {
  public $agreement; // customParameter
  public $certification; // customParameter
  public $legalEntity; // customParameter
  public $linguistSource; // customParameter
  public $membership; // customParameter
  public $os; // customParameter
  public $qualified; // customParameter
  public $sourceLanguage; // customParameter
  public $specialty; // customParameter
  public $targetLanguage; // customParameter
  public $tool; // customParameter
}

class getLinguistAccounts {
}

class getLinguistAccountsResponse {
  public $return; // linguistAccount
}

class uploadFileToEstimateTask {
  public $projID; // int
  public $fileName; // string
  public $fileData; // string
}

class uploadFileToEstimateTaskResponse {
}

class getUser {
  public $UserID; // int
}

class getUserResponse {
  public $return; // user
}

class setProjectStatus {
  public $projectObject; // project
  public $status; // string
}

class setProjectStatusResponse {
}

class getClientCompanies {
}

class getClientCompaniesResponse {
  public $return; // company
}

class updateWordCounts {
  public $LNTsk; // lnTask
}

class updateWordCountsResponse {
}

class attachFileToEstimateTask {
  public $projectObject; // projectStub
  public $fileName; // string
  public $fileData; // string
}

class attachFileToEstimateTaskResponse {
}

class attachFileToProject {
  public $projectObject; // projectStub
  public $fileName; // string
  public $fileData; // string
}

class attachFileToProjectResponse {
}

class getAttaskProjectID {
  public $projID; // int
}

class getAttaskProjectIDResponse {
  public $return; // string
}

class getLanguageService {
}

class getLanguageServiceResponse {
  public $return; // languageService
}

class languageService {
  public $sourceLanguages; // string
  public $targetLanguages; // string
}

class getProject {
  public $projStub; // projectStub
}

class getProjectResponse {
  public $return; // project
}

class projectDead {
  public $ProjectID; // int
}

class projectDeadResponse {
}

class validateUserPassword {
  public $UserName; // string
  public $Password; // string
}

class validateUserPasswordResponse {
  public $return; // boolean
}

class createLNProject {
  public $LNProj; // lnProject
}

class createLNProjectResponse {
  public $return; // int
}


/**
 * LingoAtTaskService class
 * 
 *  
 * 
 * @author    {author}
 * @copyright {copyright}
 * @package   {package}
 */
class LingoAtTaskService extends SoapClient {

  private static $classmap = array(
                                    'updateCategory' => 'updateCategory',
                                    'updateCategoryResponse' => 'updateCategoryResponse',
                                    'processFaultBean' => 'processFaultBean',
                                    'createLinguistInformation' => 'createLinguistInformation',
                                    'user' => 'user',
                                    'baseAtTaskObject' => 'baseAtTaskObject',
                                    'company' => 'company',
                                    'createLinguistInformationResponse' => 'createLinguistInformationResponse',
                                    'getLingoStaff' => 'getLingoStaff',
                                    'getLingoStaffResponse' => 'getLingoStaffResponse',
                                    'userService' => 'userService',
                                    'projectAwaitingClientApproval' => 'projectAwaitingClientApproval',
                                    'projectAwaitingClientApprovalResponse' => 'projectAwaitingClientApprovalResponse',
                                    'createLNTask' => 'createLNTask',
                                    'lnTask' => 'lnTask',
                                    'createLNTaskResponse' => 'createLNTaskResponse',
                                    'completeTask' => 'completeTask',
                                    'completeTaskResponse' => 'completeTaskResponse',
                                    'updateProjectPricing' => 'updateProjectPricing',
                                    'project' => 'project',
                                    'portfolio' => 'portfolio',
                                    'taskService' => 'taskService',
                                    'linguistTask' => 'linguistTask',
                                    'task' => 'task',
                                    'linguistRateDetails' => 'linguistRateDetails',
                                    'wordCounts' => 'wordCounts',
                                    'lingCosts' => 'lingCosts',
                                    'billableTask' => 'billableTask',
                                    'updateProjectPricingResponse' => 'updateProjectPricingResponse',
                                    'getUsersByCompanyID' => 'getUsersByCompanyID',
                                    'getUsersByCompanyIDResponse' => 'getUsersByCompanyIDResponse',
                                    'getSupportedLanguages' => 'getSupportedLanguages',
                                    'linguistAccount' => 'linguistAccount',
                                    'getSupportedLanguagesResponse' => 'getSupportedLanguagesResponse',
                                    'languagePair' => 'languagePair',
                                    'validateUserRole' => 'validateUserRole',
                                    'validateUserRoleResponse' => 'validateUserRoleResponse',
                                    'getUserByUsername' => 'getUserByUsername',
                                    'getUserByUsernameResponse' => 'getUserByUsernameResponse',
                                    'updateLinguistAccount' => 'updateLinguistAccount',
                                    'updateLinguistAccountResponse' => 'updateLinguistAccountResponse',
                                    'equals' => 'equals',
                                    'equalsResponse' => 'equalsResponse',
                                    'getProjectFinancial' => 'getProjectFinancial',
                                    'getProjectFinancialResponse' => 'getProjectFinancialResponse',
                                    'projectFinancial' => 'projectFinancial',
                                    'taskFinancial' => 'taskFinancial',
                                    'createAudioTask' => 'createAudioTask',
                                    'audioInfo' => 'audioInfo',
                                    'createAudioTaskResponse' => 'createAudioTaskResponse',
                                    'getLinguistCompanies' => 'getLinguistCompanies',
                                    'getLinguistCompaniesResponse' => 'getLinguistCompaniesResponse',
                                    'loadClient' => 'loadClient',
                                    'loadClientResponse' => 'loadClientResponse',
                                    'resourceNotExistFaultBean' => 'resourceNotExistFaultBean',
                                    'getQuotableProjects' => 'getQuotableProjects',
                                    'getQuotableProjectsResponse' => 'getQuotableProjectsResponse',
                                    'projectStub' => 'projectStub',
                                    'getTaskService' => 'getTaskService',
                                    'getTaskServiceResponse' => 'getTaskServiceResponse',
                                    'getInternalStandardRates' => 'getInternalStandardRates',
                                    'getInternalStandardRatesResponse' => 'getInternalStandardRatesResponse',
                                    'internalCostDetails' => 'internalCostDetails',
                                    'createUser' => 'createUser',
                                    'createUserResponse' => 'createUserResponse',
                                    'getClientCompanyView' => 'getClientCompanyView',
                                    'getClientCompanyViewResponse' => 'getClientCompanyViewResponse',
                                    'clientCompanyView' => 'clientCompanyView',
                                    'customParameter' => 'customParameter',
                                    'listItem' => 'listItem',
                                    'getLingoUsers' => 'getLingoUsers',
                                    'getLingoUsersResponse' => 'getLingoUsersResponse',
                                    'getLinguistAccount' => 'getLinguistAccount',
                                    'getLinguistAccountResponse' => 'getLinguistAccountResponse',
                                    'getLingoNETUsers' => 'getLingoNETUsers',
                                    'getLingoNETUsersResponse' => 'getLingoNETUsersResponse',
                                    'getLinguistStandardRates' => 'getLinguistStandardRates',
                                    'getLinguistStandardRatesResponse' => 'getLinguistStandardRatesResponse',
                                    'getLingoNETLinguistAccounts' => 'getLingoNETLinguistAccounts',
                                    'getLingoNETLinguistAccountsResponse' => 'getLingoNETLinguistAccountsResponse',
                                    'updateUser' => 'updateUser',
                                    'updateUserResponse' => 'updateUserResponse',
                                    'getAccessLevelByUsername' => 'getAccessLevelByUsername',
                                    'getAccessLevelByUsernameResponse' => 'getAccessLevelByUsernameResponse',
                                    'createLinguistContact' => 'createLinguistContact',
                                    'createLinguistContactResponse' => 'createLinguistContactResponse',
                                    'projectCurrent' => 'projectCurrent',
                                    'projectCurrentResponse' => 'projectCurrentResponse',
                                    'getLibraryTaskService' => 'getLibraryTaskService',
                                    'getLibraryTaskServiceResponse' => 'getLibraryTaskServiceResponse',
                                    'getJAS' => 'getJAS',
                                    'getJASResponse' => 'getJASResponse',
                                    'completeProject' => 'completeProject',
                                    'completeProjectResponse' => 'completeProjectResponse',
                                    'createFileTreatmentTask' => 'createFileTreatmentTask',
                                    'fileTreatmentInfo' => 'fileTreatmentInfo',
                                    'createFileTreatmentTaskResponse' => 'createFileTreatmentTaskResponse',
                                    'createLNEstimateProject' => 'createLNEstimateProject',
                                    'lnProject' => 'lnProject',
                                    'customIdMap' => 'customIdMap',
                                    'entry' => 'entry',
                                    'createLNEstimateProjectResponse' => 'createLNEstimateProjectResponse',
                                    'getClientCompany' => 'getClientCompany',
                                    'getClientCompanyResponse' => 'getClientCompanyResponse',
                                    'getGuid' => 'getGuid',
                                    'getGuidResponse' => 'getGuidResponse',
                                    'createTaskWithEmail' => 'createTaskWithEmail',
                                    'createTaskWithEmailResponse' => 'createTaskWithEmailResponse',
                                    'getClient' => 'getClient',
                                    'getClientResponse' => 'getClientResponse',
                                    'clientStub' => 'clientStub',
                                    'userStub' => 'userStub',
                                    'getLinguistView' => 'getLinguistView',
                                    'getLinguistViewResponse' => 'getLinguistViewResponse',
                                    'linguistView' => 'linguistView',
                                    'getLinguistAccounts' => 'getLinguistAccounts',
                                    'getLinguistAccountsResponse' => 'getLinguistAccountsResponse',
                                    'uploadFileToEstimateTask' => 'uploadFileToEstimateTask',
                                    'uploadFileToEstimateTaskResponse' => 'uploadFileToEstimateTaskResponse',
                                    'getUser' => 'getUser',
                                    'getUserResponse' => 'getUserResponse',
                                    'setProjectStatus' => 'setProjectStatus',
                                    'setProjectStatusResponse' => 'setProjectStatusResponse',
                                    'getClientCompanies' => 'getClientCompanies',
                                    'getClientCompaniesResponse' => 'getClientCompaniesResponse',
                                    'updateWordCounts' => 'updateWordCounts',
                                    'updateWordCountsResponse' => 'updateWordCountsResponse',
                                    'attachFileToEstimateTask' => 'attachFileToEstimateTask',
                                    'attachFileToEstimateTaskResponse' => 'attachFileToEstimateTaskResponse',
                                    'attachFileToProject' => 'attachFileToProject',
                                    'attachFileToProjectResponse' => 'attachFileToProjectResponse',
                                    'getAttaskProjectID' => 'getAttaskProjectID',
                                    'getAttaskProjectIDResponse' => 'getAttaskProjectIDResponse',
                                    'getLanguageService' => 'getLanguageService',
                                    'getLanguageServiceResponse' => 'getLanguageServiceResponse',
                                    'languageService' => 'languageService',
                                    'getProject' => 'getProject',
                                    'getProjectResponse' => 'getProjectResponse',
                                    'projectDead' => 'projectDead',
                                    'projectDeadResponse' => 'projectDeadResponse',
                                    'validateUserPassword' => 'validateUserPassword',
                                    'validateUserPasswordResponse' => 'validateUserPasswordResponse',
                                    'createLNProject' => 'createLNProject',
                                    'createLNProjectResponse' => 'createLNProjectResponse',
                                   );

  public function LingoAtTaskService($wsdl = "http://pm.llts.com:9090/USProjectServices/LingoAtTaskService?wsdl", $options = array()) {
    foreach(self::$classmap as $key => $value) {
      if(!isset($options['classmap'][$key])) {
        $options['classmap'][$key] = $value;
      }
    }
    $options['features'] = SOAP_SINGLE_ELEMENT_ARRAYS;
    parent::__construct($wsdl, $options);
  }

  /**
   *  
   *
   * @param equals $parameters
   * @return equalsResponse
   */
  public function equals(equals $parameters) {
    return $this->__soapCall('equals', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param completeTask $parameters
   * @return completeTaskResponse
   */
  public function completeTask(completeTask $parameters) {
    return $this->__soapCall('completeTask', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param updateUser $parameters
   * @return updateUserResponse
   */
  public function updateUser(updateUser $parameters) {
    return $this->__soapCall('updateUser', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getClient $parameters
   * @return getClientResponse
   */
  public function getClient(getClient $parameters) {
    return $this->__soapCall('getClient', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getUser $parameters
   * @return getUserResponse
   */
  public function getUser(getUser $parameters) {
    return $this->__soapCall('getUser', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getJAS $parameters
   * @return getJASResponse
   */
  public function getJAS(getJAS $parameters) {
    return $this->__soapCall('getJAS', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createLNTask $parameters
   * @return createLNTaskResponse
   */
  public function createLNTask(createLNTask $parameters) {
    return $this->__soapCall('createLNTask', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param projectDead $parameters
   * @return projectDeadResponse
   */
  public function projectDead(projectDead $parameters) {
    return $this->__soapCall('projectDead', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getProject $parameters
   * @return getProjectResponse
   */
  public function getProject(getProject $parameters) {
    return $this->__soapCall('getProject', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param loadClient $parameters
   * @return loadClientResponse
   */
  public function loadClient(loadClient $parameters) {
    return $this->__soapCall('loadClient', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getGuid $parameters
   * @return getGuidResponse
   */
  public function getGuid(getGuid $parameters) {
    return $this->__soapCall('getGuid', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getQuotableProjects $parameters
   * @return getQuotableProjectsResponse
   */
  public function getQuotableProjects(getQuotableProjects $parameters) {
    return $this->__soapCall('getQuotableProjects', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param updateCategory $parameters
   * @return updateCategoryResponse
   */
  public function updateCategory(updateCategory $parameters) {
    return $this->__soapCall('updateCategory', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLanguageService $parameters
   * @return getLanguageServiceResponse
   */
  public function getLanguageService(getLanguageService $parameters) {
    return $this->__soapCall('getLanguageService', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getAttaskProjectID $parameters
   * @return getAttaskProjectIDResponse
   */
  public function getAttaskProjectID(getAttaskProjectID $parameters) {
    return $this->__soapCall('getAttaskProjectID', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param updateProjectPricing $parameters
   * @return updateProjectPricingResponse
   */
  public function updateProjectPricing(updateProjectPricing $parameters) {
    return $this->__soapCall('updateProjectPricing', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getSupportedLanguages $parameters
   * @return getSupportedLanguagesResponse
   */
  public function getSupportedLanguages(getSupportedLanguages $parameters) {
    return $this->__soapCall('getSupportedLanguages', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param projectCurrent $parameters
   * @return projectCurrentResponse
   */
  public function projectCurrent(projectCurrent $parameters) {
    return $this->__soapCall('projectCurrent', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param projectAwaitingClientApproval $parameters
   * @return projectAwaitingClientApprovalResponse
   */
  public function projectAwaitingClientApproval(projectAwaitingClientApproval $parameters) {
    return $this->__soapCall('projectAwaitingClientApproval', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createLinguistContact $parameters
   * @return createLinguistContactResponse
   */
  public function createLinguistContact(createLinguistContact $parameters) {
    return $this->__soapCall('createLinguistContact', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param attachFileToEstimateTask $parameters
   * @return attachFileToEstimateTaskResponse
   */
  public function attachFileToEstimateTask(attachFileToEstimateTask $parameters) {
    return $this->__soapCall('attachFileToEstimateTask', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createFileTreatmentTask $parameters
   * @return createFileTreatmentTaskResponse
   */
  public function createFileTreatmentTask(createFileTreatmentTask $parameters) {
    return $this->__soapCall('createFileTreatmentTask', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createAudioTask $parameters
   * @return createAudioTaskResponse
   */
  public function createAudioTask(createAudioTask $parameters) {
    return $this->__soapCall('createAudioTask', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLinguistView $parameters
   * @return getLinguistViewResponse
   */
  public function getLinguistView(getLinguistView $parameters) {
    return $this->__soapCall('getLinguistView', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLinguistAccount $parameters
   * @return getLinguistAccountResponse
   */
  public function getLinguistAccount(getLinguistAccount $parameters) {
    return $this->__soapCall('getLinguistAccount', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLingoNETUsers $parameters
   * @return getLingoNETUsersResponse
   */
  public function getLingoNETUsers(getLingoNETUsers $parameters) {
    return $this->__soapCall('getLingoNETUsers', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param updateWordCounts $parameters
   * @return updateWordCountsResponse
   */
  public function updateWordCounts(updateWordCounts $parameters) {
    return $this->__soapCall('updateWordCounts', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param validateUserPassword $parameters
   * @return validateUserPasswordResponse
   */
  public function validateUserPassword(validateUserPassword $parameters) {
    return $this->__soapCall('validateUserPassword', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getClientCompany $parameters
   * @return getClientCompanyResponse
   */
  public function getClientCompany(getClientCompany $parameters) {
    return $this->__soapCall('getClientCompany', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLingoNETLinguistAccounts $parameters
   * @return getLingoNETLinguistAccountsResponse
   */
  public function getLingoNETLinguistAccounts(getLingoNETLinguistAccounts $parameters) {
    return $this->__soapCall('getLingoNETLinguistAccounts', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getUsersByCompanyID $parameters
   * @return getUsersByCompanyIDResponse
   */
  public function getUsersByCompanyID(getUsersByCompanyID $parameters) {
    return $this->__soapCall('getUsersByCompanyID', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getTaskService $parameters
   * @return getTaskServiceResponse
   */
  public function getTaskService(getTaskService $parameters) {
    return $this->__soapCall('getTaskService', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getInternalStandardRates $parameters
   * @return getInternalStandardRatesResponse
   */
  public function getInternalStandardRates(getInternalStandardRates $parameters) {
    return $this->__soapCall('getInternalStandardRates', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createLNEstimateProject $parameters
   * @return createLNEstimateProjectResponse
   */
  public function createLNEstimateProject(createLNEstimateProject $parameters) {
    return $this->__soapCall('createLNEstimateProject', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getClientCompanyView $parameters
   * @return getClientCompanyViewResponse
   */
  public function getClientCompanyView(getClientCompanyView $parameters) {
    return $this->__soapCall('getClientCompanyView', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param completeProject $parameters
   * @return completeProjectResponse
   */
  public function completeProject(completeProject $parameters) {
    return $this->__soapCall('completeProject', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param validateUserRole $parameters
   * @return validateUserRoleResponse
   */
  public function validateUserRole(validateUserRole $parameters) {
    return $this->__soapCall('validateUserRole', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getProjectFinancial $parameters
   * @return getProjectFinancialResponse
   */
  public function getProjectFinancial(getProjectFinancial $parameters) {
    return $this->__soapCall('getProjectFinancial', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLinguistAccounts $parameters
   * @return getLinguistAccountsResponse
   */
  public function getLinguistAccounts(getLinguistAccounts $parameters) {
    return $this->__soapCall('getLinguistAccounts', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getAccessLevelByUsername $parameters
   * @return getAccessLevelByUsernameResponse
   */
  public function getAccessLevelByUsername(getAccessLevelByUsername $parameters) {
    return $this->__soapCall('getAccessLevelByUsername', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getClientCompanies $parameters
   * @return getClientCompaniesResponse
   */
  public function getClientCompanies(getClientCompanies $parameters) {
    return $this->__soapCall('getClientCompanies', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createLinguistInformation $parameters
   * @return createLinguistInformationResponse
   */
  public function createLinguistInformation(createLinguistInformation $parameters) {
    return $this->__soapCall('createLinguistInformation', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param uploadFileToEstimateTask $parameters
   * @return uploadFileToEstimateTaskResponse
   */
  public function uploadFileToEstimateTask(uploadFileToEstimateTask $parameters) {
    return $this->__soapCall('uploadFileToEstimateTask', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLinguistCompanies $parameters
   * @return getLinguistCompaniesResponse
   */
  public function getLinguistCompanies(getLinguistCompanies $parameters) {
    return $this->__soapCall('getLinguistCompanies', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param setProjectStatus $parameters
   * @return setProjectStatusResponse
   */
  public function setProjectStatus(setProjectStatus $parameters) {
    return $this->__soapCall('setProjectStatus', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param updateLinguistAccount $parameters
   * @return updateLinguistAccountResponse
   */
  public function updateLinguistAccount(updateLinguistAccount $parameters) {
    return $this->__soapCall('updateLinguistAccount', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLibraryTaskService $parameters
   * @return getLibraryTaskServiceResponse
   */
  public function getLibraryTaskService(getLibraryTaskService $parameters) {
    return $this->__soapCall('getLibraryTaskService', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param attachFileToProject $parameters
   * @return attachFileToProjectResponse
   */
  public function attachFileToProject(attachFileToProject $parameters) {
    return $this->__soapCall('attachFileToProject', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createTaskWithEmail $parameters
   * @return createTaskWithEmailResponse
   */
  public function createTaskWithEmail(createTaskWithEmail $parameters) {
    return $this->__soapCall('createTaskWithEmail', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLinguistStandardRates $parameters
   * @return getLinguistStandardRatesResponse
   */
  public function getLinguistStandardRates(getLinguistStandardRates $parameters) {
    return $this->__soapCall('getLinguistStandardRates', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getUserByUsername $parameters
   * @return getUserByUsernameResponse
   */
  public function getUserByUsername(getUserByUsername $parameters) {
    return $this->__soapCall('getUserByUsername', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createLNProject $parameters
   * @return createLNProjectResponse
   */
  public function createLNProject(createLNProject $parameters) {
    return $this->__soapCall('createLNProject', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param createUser $parameters
   * @return createUserResponse
   */
  public function createUser(createUser $parameters) {
    return $this->__soapCall('createUser', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLingoStaff $parameters
   * @return getLingoStaffResponse
   */
  public function getLingoStaff(getLingoStaff $parameters) {
    return $this->__soapCall('getLingoStaff', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

  /**
   *  
   *
   * @param getLingoUsers $parameters
   * @return getLingoUsersResponse
   */
  public function getLingoUsers(getLingoUsers $parameters) {
    return $this->__soapCall('getLingoUsers', array($parameters),       array(
            'uri' => 'http://ws.attask.lingosys.com/',
            'soapaction' => ''
           )
      );
  }

}

?>
