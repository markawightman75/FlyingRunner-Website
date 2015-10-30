<?php
//============================================================+
// File name   : example_001.php
// Begin       : 2008-03-04
// Last Update : 2013-05-14
//
// Description : Example 001 for TCPDF class
//               Default Header and Footer
//
// Author: Nicola Asuni
//
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com LTD
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: Default Header and Footer
 * @author Nicola Asuni
 * @since 2008-03-04
 */
function create_pacing_band($splits) {
// Include the main TCPDF library (search for installation path).
//require_once('tcpdf\tcpdf_include.php');
require_once('tcpdf\tcpdf.php');
require_once('tcpdf\tcpdf_config_alt.php');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');
// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 001', PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->SetHeaderData('', '', 'Flying Runner marathon pacing band', "Why not treat yourself to one of our beautiful gifts when you finish your marathon?", array(0,0,0), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}
// ---------------------------------------------------------

// set default font subsetting mode
//$pdf->setFontSubsetting(true);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set font
$pdf->SetFont('helvetica', 'B', 10); //Helvetica bold

// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
//$pdf->SetFont('dejavusans', '', 10, '', true);

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

// set text shadow effect
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

// Set some content to print
/* $html = <<<EOD
<h1>Welcome to <a href="http://www.tcpdf.org" style="text-decoration:none;background-color:#CC0000;color:black;">&nbsp;<span style="color:black;">TC</span><span style="color:white;">PDF</span>&nbsp;</a>!</h1>
<i>This is the first example of TCPDF library.</i>
<p>This text is printed using the <i>writeHTMLCell()</i> method but you can also use: <i>Multicell(), writeHTML(), Write(), Cell() and Text()</i>.</p>
<p>Please check the source code documentation and other examples for further information.</p>
<p style="color:#CC0000;">TO IMPROVE AND EXPAND TCPDF I NEED YOUR SUPPORT, PLEASE <a href="http://sourceforge.net/donate/index.php?group_id=128076">MAKE A DONATION!</a></p>
EOD; */

$html .= '<style>'.file_get_contents('pacing-band.css').'</style>';
$html .= "<table>";
$html .= "<tr class=\"headerbackground\"><th>Mile</th><th>Split</th><th>Elapsed</th></tr>";
for ($m = 1; $m <= 13; $m++) {
	$split = $splits['mile' . $m];
	if ($m % 2 == 1)
	{
		$html .= "<tr class=\"redbackground\" style=\"background-color: red;\"><td>" .  $m . "</td><td>" . $split . "</td><td></td></tr>";
	}
	else
	{
		$html .= "<tr class=\"whitebackground\"><td>" .  $m . "</td><td>" . $split . "</td><td></td></tr>";
	}
} 
$html .= "<tr><td>HALF</td><td></td><td>" . $splits['halfway'] . "</td></tr>";
	
for ($m = 14; $m <= 26; $m++) {
	$split = $splits['mile' . $m];
	$html .= "<tr><td>" .  $m . "</td><td>" . $split . "</td><td></td></tr>";
} 
$html .= "<tr><td>26.2</td><td></td><td>" . $splits['halfway'] . "</td></tr>";
$html .= "<table>";

// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);


// Colors, line width and bold font
		$pdf->SetFillColor(235, 235, 235);
		$pdf->SetDrawColor(190, 190, 190);
		$pdf->SetTextColor(80,80,80);
		$pdf->SetLineWidth(0.1);
		$pdf->SetFont('', '');
		// Header
		// column titles
		$pdf->SetFontSize(10);
		$header = array('', 'Pace', 'Time');
		$w = array(5, 10, 15); //Column widths
		$num_headers = count($header);
		for($i = 0; $i < $num_headers; ++$i) {
			if ($i == 0) $border = 'LT';
			if ($i == 1) $border = 'T';
			if ($i == 2) $border = 'RT';
			$pdf->Cell($w[$i], 5, $header[$i], $border, 0, 'C', 1);
			//$pdf->Cell($w[$i], 7, $header[$i], array('LTRB' => array('width' => 0.1,'color' => array(200, 200, 200))), 0, 'C', 1);
		}

		//Rotated cell down right hand side
		/* $pdf->SetFontSize(8);
		$pdf->SetFont('', '');
		$pdf->SetTextColor(90,90,90);
		$pdf->StartTransform();
			$pdf->Rotate(-90);
			$pdf->Translate(0,-6);
			$pdf->Cell(75, 6, "Why not celebrate your achievement with a beautiful gift from The Flying Runner?", 1, 0, 'L', $fill);
			//$pdf->Ln();
		$pdf->StopTransform();*/
		$pdf->Ln();
 
		// Color and font restoration
		$pdf->SetFont('', 'B');
		$pdf->SetFillColor(224, 235, 255);
		$pdf->SetDrawColor(190, 190, 190);
		$pdf->SetTextColor(0);
		//$pdf->SetFont('');
		$pdf->SetFontSize(10);
		// Data
		$fill = 0;
		$elapsed_time_secs = 0;
		for ($m = 1; $m <= 13; $m++) {
			if ($m%2 == 0) {
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(164, 100, 151); //Flying Runner purple
				$fill=1;
			}
			else {
				$pdf->SetTextColor(0);
				//$pdf->SetFillColor(255,255,255);
				$fill = 0;
			}
			$split = $splits['mile' . $m];
			$elapsed_time_secs += $split;
			$split_mm_ss = format_hh_mm_ss($split);
			$elapsed_time_mm_ss = format_hh_mm_ss($elapsed_time_secs);
			
			$pdf->SetFontSize(10);
			$pdf->Cell($w[0], 5.5, $m, 'L', 0, 'L', $fill);
			$pdf->Cell($w[1], 5.5, $split_mm_ss, 0, 0, 'C', $fill);
			//$pdf->SetFontSize(8);
			$pdf->Cell($w[2], 5.5, $elapsed_time_mm_ss, 'R', 0, 'R', $fill);			
			$pdf->Ln();
			//$fill=!$fill;
		}

		$pdf->SetFillColor(20,132,117); //Dark green
		$pdf->SetTextColor(255,255,255);
		$pdf->Cell($w[0] + $w[1], 5, 'Halfway', 'L', 0, 'C', 1);
		$pdf->Cell($w[2], 5, $splits['halfway'], 'R', 0, 'C', 1);
		$pdf->Ln();

		$pdf->SetFillColor(224, 235, 255);
		
		for ($m = 14; $m <= 26; $m++) {
			if ($m%2 == 1) {
				$pdf->SetTextColor(255,255,255);
				$pdf->SetFillColor(164, 100, 151); //Flying Runner purple
				$fill = 1;
			}
			else {
				$pdf->SetTextColor(0);
				//$pdf->SetFillColor(255,255,255);
				$fill = 0;
			}
			$split = $splits['mile' . $m];
			$elapsed_time_secs += $split;
			$split_mm_ss = format_hh_mm_ss($split);
			$elapsed_time_mm_ss = format_hh_mm_ss($elapsed_time_secs);
			
			$pdf->Cell($w[0], 5.5, $m, 'L', 0, 'C', $fill);
			$pdf->Cell($w[1], 5.5, $split_mm_ss, 0, 0, 'C', $fill);
			$pdf->Cell($w[2], 5.5, $elapsed_time_mm_ss, 'R', 0, 'R', $fill);			
			$pdf->Ln();
			//$fill=!$fill;
		}

		$pdf->SetFillColor(235, 235, 235);
		$pdf->Cell($w[0] + $w[1], 5, 'Finish', 'LB', 0, 'C', 1);
		$pdf->Cell($w[2], 5, $splits['finish'], 'RB', 0, 'C', 1);
		$pdf->Ln();
		
		$pdf->SetFont('', ''); //Undo bold
		$pdf->SetFontSize(8);
		$pdf->Cell(array_sum($w),80, 'Cut to size', 'LRB', 0, 'C', 0, '', 0, false, 'T', 'B'); //Vertical align text at bottom
		$pdf->Ln();
		//$pdf->Cell(array_sum($w), 0, '', 'T');






//ob_start();
// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('marathon-pacing-band.pdf', 'D');
//ob_end_flush();
}

function format_hh_mm_ss($t,$f=':') // t = seconds, f = separator 
{
	if ($t >= 3600) {
		return sprintf("%02d%s%02d%s%02d", floor($t/3600), $f, ($t/60)%60, $f, $t%60);
	}
	else {
		return sprintf("%01d%s%02d", ($t/60)%60, $f, $t%60);	
	}
}

//============================================================+
// END OF FILE
//============================================================+
?>