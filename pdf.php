<?php
define('FPDF_FONTPATH','C:\\PHP\\fpdf\\font\\');
require('fpdf.php');

$ClientName = 'My Company';
$ProjectName = 'A Really Really Big Project';

class PDF extends FPDF
{
//Page header
function Header()
{
    
    //Arial bold 15
    $this->SetFont('Times','B',14);
	$this->SetTextColor(0,0,0);
	

	$width = $this->GetStringWidth("Language Services Estimate for ".$GLOBALS['ClientName'])+150;
	


    $this->Cell(190,100, $this->Image('../images/LingoLogo_new.jpg',$this->GetX(),$this->GetY()), 0,0, "L", false);
	//$this->Cell(0,15,"Language Services Estimate for ".$GLOBALS['ClientName']."\nProject: ".$GLOBALS['ProjectName'],1,0,'C');
	$this->MultiCell(0,18,"Language Services Estimate for ".$GLOBALS['ClientName']."\n\nProject: ".$GLOBALS['ProjectName'], 1, 'C', false);
	$this->Cell(10,20, '',0,1);
    
    
    //Line break
    $this->Ln();
}

//Page footer
function Footer()
{
    //Position at 1.5 cm from bottom
    $this->SetY(-65);
    //Arial italic 8
    $this->SetFont('Arial','I',8);
	$this->SetTextColor(0,0,0);
    //Page number
	$this->MultiCell(0,11,"Lingo Systems\n15115 SW Sequoia Pkwy, #200 - Portland, Oregon - USA * Phone: 503.419.4956 * Toll Free: 800.878.8523 * Fax: 503.419.4873\nwww.llts.com",'T','C',false);
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}

}

//Instanciation of inherited class
$pdf=new PDF('P', 'pt', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',12);


//determine our line height
$lineHeight = 14;
$lines = 0;
foreach(explode("\n","Mike van Grunsven\nSenior Localization Engineer\n503-419-4845\nmikevg@lingosys.com") as $line)
{
	if ($pdf->GetStringWidth($line) > 150)
		$lines++;
	$lines++;
}


$pdf->SetFillColor(200,200,200);
$pdf->MultiCell(100,($lineHeight*$lines)/4, "\n".$ClientName."\nContact\n\n",1,'L',true);
$pdf->SetXY($pdf->GetX()+100, $pdf->GetY()-($lineHeight*$lines));
$pdf->MultiCell(150,($lineHeight*$lines)/4, "\nJohn Doe\nJDoe@email.com\n\n",1, 'C', false);
$pdf->SetXY($pdf->GetX()+250, $pdf->GetY()-($lineHeight*$lines));
$pdf->MultiCell(100,($lineHeight*$lines)/4, "\nLingo Systems\nContact\n\n",1,'L',true);
$pdf->SetXY($pdf->GetX()+350, $pdf->GetY()-($lineHeight*$lines));
$pdf->MultiCell(150,$lineHeight, "Mike van Grunsven\nSenior Localization Engineer\n503-419-4845\nmikevg@lingosys.com",1,'C',false);

$pdf->Cell(100,($lineHeight*$lines),"Date Prepared:",1,0,'L',true);
$pdf->Cell(150,($lineHeight*$lines),"Apr 15, 2007",1,0,'C', false);
$pdf->MultiCell(100,($lineHeight*$lines)/4, "\nLingo Systems\nProject Manager:\n\n",1,'L',true);
$pdf->SetXY($pdf->GetX()+350, $pdf->GetY()-($lineHeight*$lines));
$pdf->MultiCell(150,$lineHeight, "Mike van Grunsven\nSenior Localization Engineer\n503-419-4845\nmikevg@lingosys.com",1,'C',false);



$pdf->Cell(0,28,'Description of Project:',0,1);
$pdf->MultiCell(0,14,'Ad retia sedebam: erat in proximo non venabulum aut lancea, sed stilus et pugilares'."\n\n\n",0);

$pdf->Cell(0,28,'Requested Services include:',0,1);
$pdf->Cell($pdf->GetStringWidth('•')+2,14,'•',0,0);
$pdf->MultiCell(0,14,'Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares',0);

$pdf->Cell($pdf->GetStringWidth('•')+2,14,'•',0,0);
$pdf->MultiCell(0,14,'Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares ',0);

$pdf->Cell($pdf->GetStringWidth('•')+2,14,'•',0,0);
$pdf->MultiCell(0,14,'Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares Ad retia sedebam erat in proximo non venabulum aut lancea, sed stilus et pugilares',0);

$pdf->MultiCell(0,16,"\n\n\nProject Summary",0,1);
$pdf->Cell(125,16,'Estimated Cost: ',1,0,'R',true);
$pdf->Cell(400,16,'Total: $3,456',1,1,'L',false);
$pdf->Cell(125,16,'Estimated Timeline: ',1,0,'R',true);
$pdf->Cell(400,16,'Twelve Days from approval',1,1,'L',false);
$pdf->Cell(125,16,'Payment Terms: ',1,0,'R',true);
$pdf->Cell(400,16,'50% of total due at project start. Remainder due at project completion',1,1,'L',false);

$pdf->MultiCell(0,16,"\n\nEstimate Analysis",0,1);
$pdf->SetFillColor(80,80,80);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(550,16,"Language 1",1,1,'L',true);
$pdf->SetFillColor(200,200,200);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(315,16,"Task",1,0,'C',true);
$pdf->Cell(80,16,"Quantity/Unit",1,0,'C',true);
$pdf->Cell(80,16,"Rate/Unit",1,0,'C',true);
$pdf->Cell(75,16,"Cost",1,1,'C',true);

$pdf->Cell(315,16,"Translate/Copy-Edit, New Text",1,0,'L',false);
$pdf->Cell(80,16,"119",1,0,'L',false);
$pdf->Cell(80,16,"0.32",1,0,'L',false);
$pdf->Cell(75,16,"$38.08",1,1,'R',false);

$pdf->Cell(315,16,"Review/copy-edit repetitions (100% Match)",1,0,'L',false);
$pdf->Cell(80,16,"200",1,0,'L',false);
$pdf->Cell(80,16,"0.27",1,0,'L',false);
$pdf->Cell(75,16,"$54.00",1,1,'R',false);

$pdf->Cell(315,16,"Review/copy-edit repetitions (99-85% Match)",1,0,'L',false);
$pdf->Cell(80,16,"4",1,0,'L',false);
$pdf->Cell(80,16,"0.32",1,0,'L',false);
$pdf->Cell(75,16,"$1.28",1,1,'R',false);

$pdf->Cell(475,16,"Total:",0,0,'R',false);
$pdf->Cell(75,16,"$357.75",1,1,'R',false);

$pdf->MultiCell(0,18,' ',0,0);

$pdf->SetFillColor(80,80,80);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(550,16,"Language 2",1,1,'L',true);
$pdf->SetFillColor(200,200,200);
$pdf->SetTextColor(0,0,0);
$pdf->Cell(315,16,"Task",1,0,'C',true);
$pdf->Cell(80,16,"Quantity/Unit",1,0,'C',true);
$pdf->Cell(80,16,"Rate/Unit",1,0,'C',true);
$pdf->Cell(75,16,"Cost",1,1,'C',true);

$pdf->Cell(315,16,"Translate/Copy-Edit, New Text",1,0,'L',false);
$pdf->Cell(80,16,"119",1,0,'L',false);
$pdf->Cell(80,16,"0.32",1,0,'L',false);
$pdf->Cell(75,16,"$38.08",1,1,'R',false);

$pdf->Cell(315,16,"Review/copy-edit repetitions (100% Match)",1,0,'L',false);
$pdf->Cell(80,16,"200",1,0,'L',false);
$pdf->Cell(80,16,"0.27",1,0,'L',false);
$pdf->Cell(75,16,"$54.00",1,1,'R',false);

$pdf->Cell(315,16,"Review/copy-edit repetitions (99-85% Match)",1,0,'L',false);
$pdf->Cell(80,16,"4",1,0,'L',false);
$pdf->Cell(80,16,"0.32",1,0,'L',false);
$pdf->Cell(75,16,"$1.28",1,1,'R',false);

$pdf->Cell(475,16,"Total:",0,0,'R',false);
$pdf->Cell(75,16,"$357.75",1,1,'R',false);


//project assumptions
$pdf->Cell(0,28,'Project Assumptions:',0,1);
$pdf->Write(16, "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus a nisi purus, id imperdiet libero. Proin justo libero, sollicitudin luctus elementum nec, malesuada in dolor. Vestibulum vel odio vitae turpis semper ultrices sit amet ac massa. Phasellus sapien risus, consectetur in laoreet a, consectetur a leo. Donec placerat odio nec metus luctus id hendrerit arcu tincidunt. Nulla id urna ipsum. Nulla eget elit nec orci imperdiet viverra. Morbi accumsan dapibus purus, scelerisque luctus est vestibulum eget. Morbi sollicitudin rutrum diam sit amet ornare. Quisque ultrices orci vitae sapien porttitor faucibus. Duis quam ante, malesuada vel sodales ut, blandit et felis. Donec id nibh eros, eu vehicula eros. Praesent tincidunt accumsan fermentum. Praesent in accumsan mauris.\n\nSed rhoncus aliquam quam nec pellentesque. Aenean sed dolor ligula. Sed quis interdum magna. Nulla mattis venenatis nunc in luctus. Vivamus ornare condimentum dapibus. Ut neque nulla, rhoncus nec hendrerit at, lacinia non purus. Nunc odio diam, venenatis sed aliquam ac, molestie eget nulla. Phasellus eleifend tempus sem, a mattis urna posuere at. Fusce consequat odio sit amet nibh luctus gravida. Aenean gravida adipiscing felis, ac pellentesque velit tincidunt sed. Aliquam eu nunc leo. Vestibulum vel nisi sed justo laoreet porta. Aliquam hendrerit libero vitae nisl aliquet ac tincidunt orci scelerisque. Etiam convallis vestibulum tortor at vulputate. Aliquam dui lacus, vulputate ut aliquam eget, rhoncus at mauris. Aenean euismod porttitor purus vitae condimentum. Mauris auctor tristique massa, et mattis mauris congue at. Mauris consequat, velit id venenatis eleifend, purus velit hendrerit metus, non tempor elit orci non lorem. Nullam sollicitudin, ligula ac viverra ultrices, mi risus sagittis mauris, quis dictum ligula quam sit amet magna. Pellentesque in pellentesque sem. ");
$pdf->Ln();

//T&C
$pdf->AddPage();
$pdf->SetFont('Helvetica','BU',12);
$pdf->Cell(0,14,'Estimate Terms & Conditions',0,1,'C');
$pdf->SetFont('Arial','',8);
$t_c = "The following terms and conditions apply to any of the localization, translation, engineering, formatting, and other services (collectively \"Service\") provided by Lingo Systems, LLC, an Oregon Limited Liability Company (\"Lingo Systems\"), to you and along with the accompanying Estimate, constitutes the entire Agreement between Lingo Systems and you and supersedes all prior oral or written understanding or statement with respect to the subject matter of this Agreement.\n\n1. Charges: Charges for the Service shall be as specified by Lingo Systems to you in writing in the Estimate. Estimates are based upon specifications and information originally submitted by you, and any change therein, including delivery requirements, automatically void the quotation (or necessitate a Change Order). For example, if the Estimate is based on sample pages, a sample document used as a model, or an incomplete version of the source file, or any file other than the actual one you want localized, the amount of the estimate and the deadline may be adjusted accordingly. Adjustments to the Estimate Letter may also be necessary if specifications are changed or added, or if work not covered in the original quotation is required, or if you failed to provide complete, written specifications for the assignment. Estimates are only valid for thirty days or as otherwise specified in writing.\n\n2. Orders: Orders authorized by you may only be canceled on terms that provide for payment for work commenced by Lingo Systems and necessary work-related obligations entered into pursuant to the order. Upon cancellation of any order prior to completion, Lingo Systems shall be reimbursed for all costs and expenses incurred with respect to the order prior to cancellation. Furthermore, if you change the original text, alter specifications, or add new specifications once you have authorized Lingo Systems to begin work, the changes, alterations, or additions may result in additional charges and adjustments of deadlines.\n\n3. Terminology: Unless you provide terminological reference material like glossaries or terminology lists, Lingo Systems will use its best judgment in the selection of terms pertinent to a given field. You will be charged for changes to such terms.\n\n4. Appearance: When the Service includes formatting, typesetting, page layout, or artwork, Lingo Systems will seek the closest match practicable between the appearance of the original and that of the finished product or will layout as you specified. Unless the Estimate specifies otherwise, Lingo Systems does not guarantee that the format, fonts, typefaces, point sizes, text density, artwork, colors, paper, and other elements of printed documents it chooses and those of the original will be identical. Translated documents are often longer or shorter than the original, and technical or other considerations may result in elements of appearance different from the original.\n\n5. Reviews: You have the right to review translated files. If you waive this right, you agree to accept the work project as is, except for gross negligence or translation errors that materially alter the meaning of the original text.\n\n6. Editing: Any editing or alteration of the finished product provided to you when such product is acceptable for the language involved or when style or other matter is left to the judgment of Lingo Systems will be charged to you, except that errors in accuracy will be corrected at no extra charge.\n\n7. Payment: 1) Payment for all Service provided by Lingo Systems will be due upon invoice at progress intervals and upon completion, as specified in the Estimate. 2) All errors, claims, or requests for adjustment must be presented within thirty days after the date of delivery or such work will be deemed to have been accepted. 3) Charges due and unpaid thirty (30) days after invoice shall bear interest from the date payment is due at the rate of one and one-half percent (1 1/2 %) per month (eighteen percent (18%) per annum).\n\n8. Disclaimer of Warranty: Except as expressly provided in the estimate letter, all service is provided on an “as is” and “with all faults” basis, and Lingo Systems makes no guarantee, warranty, or representation expressed or implied, regarding the condition, merchantability, or fitness of the service for any particular use or purpose. Your reliance on the service to incur an obligation or make a decision, among other things, is at your own risk.\n\n9. Limitation of Liability: Lingo Systems' charges for the Service provided under this Agreement are unrelated to the value of your business or your other pursuits or to the potential for indirect, incidental, consequential, reliance, special, or other damages in excess of those allowed by this Agreement. When you use the Service, you agree to accept charges calculated on that basis and agree that this allocation of risk of liability is fair, reasonable, and not unconscionable. You retain the rights to purchase insurance to cover any additional loss or liability. Accordingly, the entire liability of Lingo Systems to you is limited as set forth hereafter.\n\nA. Except when due to intentional or willful misconduct by Lingo Systems, the total liability of Lingo Systems and Lingo Systems' affiliates for loss or damage caused by defect or failure of the Service, or arising from the performance or nonperformance of any work under this Agreement, including (but not limited to) disclosure of information or untimely delivery, shall be limited to a credit or refund to you for services which are not provided or performed in accordance with this Agreement. For purposes of this Agreement, “Lingo Systems' affiliates” shall include Lingo Systems' officers, employees, agents, subcontractors, and suppliers. This limitation shall apply regardless of the form of action, whether in contract, tort, including negligence, strict liability, or otherwise.\n\nB. Neither Lingo Systems nor Lingo Systems' affiliates shall be liable in any event for any indirect, incidental, special, or consequential damages, arising directly or indirectly from any action or failure to act by Lingo Systems or any Lingo Systems' affiliates whether or not it had any knowledge, actual or constructive, that such damages might be incurred. This provision specifically includes, but is not limited to, damages resulting in loss of profits or income. Lingo Systems and Lingo Systems' affiliates shall not be responsible for damages due to causes beyond the reasonable control of Lingo Systems or attributable to any service, products, or actions of any person other than Lingo Systems, its employees, subcontractors, and agents. This limitation of liability shall apply regardless of the form of action, whether in contract, tort, including negligence, strict liability, or otherwise.\n\n10. Customer Property: Your property delivered to Lingo Systems for use in the production of work is received, used, stored, and returned to you upon completion of the work by Lingo Systems without any liability for loss or damage.\n\n11. Confidentiality: To the extent permitted by law, Lingo Systems will make reasonable efforts to ensure the confidentiality of your information provided to Lingo Systems pursuant to this Agreement. However, absolute confidentiality of your information provided to Lingo Systems is not guaranteed by Lingo Systems.\n\n12. Subcontracting: Lingo Systems may subcontract any or all of the work to be performed by it under this Agreement, but subject to the exclusions and limitations of liability provided under this Agreement, shall retain the responsibility for the work that is subcontracted.\n\n13. Indemnification: Lingo Systems and Lingo Systems' affiliates shall not be liable for the use or content of the translated and other materials, including, but not limited to, any infringement of copyrights or licenses, any false, misleading, or offensive statements, or statements which violate state or federal law. You agree that you will be solely liable with respect to the content of your translated and other materials, and its use, and will indemnify and hold Lingo Systems harmless for any costs or damages, including litigation costs and attorney fees, it incurs with respect to such content or use.\n\n14. Force Majeure: Neither Lingo Systems nor Lingo Systems affiliates shall be liable in any way for any loss, damage, delay of failure of performance resulting directly or indirectly from any cause beyond its reasonable control, including, but not limited to, an act of God, fire or other catastrophe, electrical, computer, or mechanical failure, work stoppage, or delays or failure, act of any carrier or agent, or any other cause beyond its control, whether or not similar to the foregoing. The preceding statements shall not excuse your obligation to pay charges for services provided pursuant to this Agreement.\n\n15. Severability: If any provision of this Agreement is held to be invalid, unenforceable, or void, then the meaning of such provision shall be construed so as to render it enforceable to the extent feasible. If no feasible interpretation would save such provision, it shall be severed from this Agreement with respect to the matter in question and the remainder of the Agreement shall continue in full force and effect. However, if such provision is considered an important element of the Agreement, the parties shall promptly negotiate a replacement.\n\n16. No Waiver: The failure of either party at any time to enforce any right or remedy available to it under this Agreement in a particular instance shall not be construed to be a waiver of a right or remedy in subsequent instances.\n\n17. Choice of Law: This Agreement shall be interpreted under the laws of the State of Oregon.\n\n18. Attorney Fees: In the event a suit or action is brought to enforce or interpret any of the provisions of this Agreement, the prevailing party shall be entitled to reasonable attorney fees in connection therewith. The determination of whom is the prevailing party and the amount of the reasonable attorney fees to be paid to the prevailing party shall be decided by the court or courts, including any appellate court, in which such matter is tried, heard, or decided.\n\n";
$pdf->Write(10,$t_c);


//update the document properties
$pdf->SetAuthor('Lingo Systems');
$pdf->SetCreator('Lingo Systems');
$pdf->SetSubject("Language Services Estimate for ".$GLOBALS['ClientName']);
$pdf->SetTitle("Language Services Estimate for ".$GLOBALS['ClientName']);

$pdf->Output();
?>
