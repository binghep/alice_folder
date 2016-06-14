<?php
require('chinese.php');

$pdf=new PDF_Chinese();
$pdf->AddBig5Font();
$pdf->AddPage();
$pdf->SetFont('Big5','',20);
// $data2 = mb_convert_encoding("你好nihao","GBK","UTF-8");
$nihao="你好nihao";
$a=iconv("UTF-8","BIG-5", $nihao);
$pdf->Write(10,$a);
$pdf->Output();
?>
