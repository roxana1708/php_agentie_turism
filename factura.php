 <?php
/*call the FPDF library*/

require('fpdf.php');

/*A4 width : 219mm*/

function make_invoice($id, $rez_date, $id_rez, $serv_type, $desc, $nr_pers, $price_per_pers) {
	//echo "aaa";
$pdf = new FPDF('P','mm','A4');

//function make_fpdf() {
	$pdf->AddPage();
	/*output the result*/

	/*set font to arial, bold, 14pt*/
	$pdf->SetFont('Arial','B',20);

	/*Cell(width , height , text , border , end line , [align] )*/

	$pdf->Cell(71 ,10,'',0,0);
	$pdf->Cell(59 ,5,'Invoice',0,0);
	$pdf->Cell(59 ,10,'',0,1);

	$pdf->SetFont('Courier','B',15);
	$pdf->Cell(71 ,5,'therokiproject',0,0);
	$pdf->Cell(59 ,5,'',0,0);
	$pdf->SetFont('Arial','',15);
	$pdf->Cell(59 ,5,'Details',0,1);

	$pdf->SetFont('Arial','',10);

	$pdf->Cell(130 ,5,'Bucharest',0,0);
	$pdf->Cell(25 ,5,'Customer ID:',0,0);
	$pdf->Cell(34 ,5,$id,0,1);

	$pdf->Cell(130 ,5,'Bucharest, 010121',0,0);
	$pdf->Cell(30 ,5,'Reservation Date:',0,0);
	$pdf->Cell(34 ,5,$rez_date,0,1);
	 
	$pdf->Cell(130 ,5,'',0,0);
	$pdf->Cell(30 ,5,'Reservation No:',0,0);
	$pdf->Cell(34 ,5,$id_rez,0,1);


	$pdf->Cell(50 ,10,'',0,1);

	$pdf->SetFont('Arial','B',10);
	/*Heading Of the table*/
	$pdf->Cell(50 ,6,'Service Type',1,0,'C');
	$pdf->Cell(80 ,6,'Description',1,0,'C');
	$pdf->Cell(23 ,6,'No. people',1,0,'C');
	//$pdf->Cell(30 ,6,'Unit Price',1,0,'C');
	//$pdf->Cell(20 ,6,'Sales Tax',1,0,'C');
	$pdf->Cell(25 ,6,'Price/Pers',1,1,'C');/*end of line*/
	/*Heading Of the table end*/


	$pdf->SetFont('Arial','',10);
	
	    //for ($i = 0; $i <= 10; $i++) {
			$pdf->Cell(50 ,6,$serv_type,1,'C');
			$pdf->Cell(80 ,6,$desc,1,0);
			$pdf->Cell(23 ,6,$nr_pers,1,0,'C');
			//$pdf->Cell(30 ,6,'15000.00',1,0,'R');
			//$pdf->Cell(20 ,6,'100.00',1,0,'R');
			$pdf->Cell(25 ,6,$price_per_pers,1,1,'R');
		//}
		

	$pdf->Cell(118 ,6,'',0,0);
	$pdf->Cell(25 ,6,'Subtotal',0,0);
	$pdf->Cell(30 ,6,$nr_pers*$price_per_pers,1,1,'R');


	$pdf->Output('D', "factura.pdf");
}

	//make_fpdf();
//m(1, 1, 1);
	//make_invoice(1,1,1,1,1,1,1);
?>
