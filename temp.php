<?PHP

function populate_temp(&$tempObj)
{
	if ($tempObj == NULL)
		return FALSE;
	elseif (!isset($_POST['quoteType']) || !isset($_POST['submit']))
		return FALSE;
	else
	{
		$tempObj->set_quoteType($_POST['quoteType']);
		$tempObj->set_estimateDate($_POST['estDate']);
		$tempObj->set_projectID($_POST['projectid']);
		$tempObj->set_representative($_POST['rep']);
		$tempObj->set_projectManager($_POST['pm']);
		$tempObj->set_clientType($_POST['cusType']);
		$tempObj->set_clientName($_POST['cusName']);
		$tempObj->set_prospectName($_POST['prosName']);
		$tempObj->set_projectType($_POST['projType']);
		$tempObj->set_projectTypeOther($_POST['ptOtherText']);
		$tempObj->set_fileType($_POST['fileType']);
		$tempObj->set_deliverable($_POST['deliverable']);
		
		//process the requested services array
		foreach($_POST['requestedServices'] as $reqServ)
		{
			$tempObj->addReqServ($reqServ, $_POST[str_replace(" ", "_", $reqServ)]);
		}
		
		$tempObj->set_sourceLanguage($_POST['sourceL']);
		
		//process the target languages array
		if (count($_POST['targetL'] > 0))
			foreach($_POST['targetL'] as $language)
			{
				$tempObj->addTargetLang($language);
			}
		
		$tempObj->set_otherLangName($_POST['otherLangName']);
		$tempObj->set_otherLangNewTextCost($_POST['otherLangNewTextCost']);
		$tempObj->set_otherLangFuzzyTextCost($_POST['otherLangFuzzyTextCost']);
		$tempObj->set_otherLangMatchTextCost($_POST['otherLangMatchTextCost']);
		$tempObj->set_otherLangTransHourlyCost($_POST['otherLangTransHourly']);
		$tempObj->set_otherLangPRHourlyCost($_POST['otherLangPRHourly']);
		$tempObj->set_deliveryDate($_POST['estDeliveryDate']);
		$tempObj->set_rushFee($_POST['rushFees']);
		$tempObj->set_discountType($_POST['discountType']);
		$tempObj->set_discountAmount($_POST['discountAmount']);
		$tempObj->set_description($_POST['projDesc']);
		$tempObj->set_notes($_POST['general_notes']);
		$tempObj->set_billingTerms($_POST['terms']);
		$tempObj->set_billingTermsOther($_POST['termsOther']);
		$tempObj->set_billingCycle($_POST['cycle']);
		$tempObj->set_billingCycleOther($_POST['cycleOther']);
		$tempObj->set_wordCountStyle($_POST['wordCountStyle']);
		$tempObj->set_newText($_POST['new']);
		$tempObj->set_fuzzyText($_POST['fuzzy']);
		$tempObj->set_matchText($_POST['100']);
		$tempObj->set_totalText($_POST['totalText']);
		$tempObj->set_percentLeverage($_POST['percentLeverage']);
		$tempObj->set_linguisticCostType($_POST['linguisticCostType']);
		$tempObj->set_linguisticSellType($_POST['linguisticSellType']);
		
		if (isset($_POST['lockunits']) && ($_POST['lockunits'] == 'lockunits'))
			$tempObj->set_unitsLocked(TRUE);
		else
			$tempObj->set_unitsLocked(FALSE);
			
		$tempObj->set_proofreading($_POST['proofreading']);
		$tempObj->set_numberPages($_POST['pageNumber']);
		$tempObj->set_dtpPagesPerHour($_POST['fmtPageHour']);
		$tempObj->set_dtpHours($_POST['fmtHours']);
		$tempObj->set_costPerPage($_POST['fmtCostPer']);
		$tempObj->set_dtpCostType($_POST['DTPCostunits']);
		$tempObj->set_dtpSellType($_POST['DTPSellunits']);
		$tempObj->set_numberGraphics($_POST['engGraphNum']);
		$tempObj->set_graphicsPerHour($_POST['engGraphHour']);
		$tempObj->set_dtpCoordPercent($_POST['fmtCoord']);
		$tempObj->set_tmWork($_POST['engTM']);
		$tempObj->set_fileTreatment($_POST['engineer']);
		$tempObj->set_numberScaps($_POST['engScap']);
		$tempObj->set_scapsPerHour($_POST['engScapHour']);
		$tempObj->set_qaPagesPerHour($_POST['qaPagesHour']);
		$tempObj->set_qaHours($_POST['qaHours']);
		$tempObj->set_qaCoordPercent($_POST['qaCoord']);
		$tempObj->set_pmPercent($_POST['pmPercentage']);
		$tempObj->set_addTask1_cost($_POST['addTask1']);
		$tempObj->set_addTask1_description($_POST['addDesc1']);
		$tempObj->set_addTask2_cost($_POST['addTask2']);
		$tempObj->set_addTask2_description($_POST['addDesc2']);
		$tempObj->set_addTask3_cost($_POST['addTask3']);
		$tempObj->set_addTask3_description($_POST['addDesc3']);
		
		return TRUE;
	}
	
	return FALSE;


}

?>