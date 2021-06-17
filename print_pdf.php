<?php
  require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/fpdf/tfpdf.php');
  require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/html2pdf.php');
  $test_id = $_GET['id'];
  $query = "SELECT * FROM test INNER JOIN user ON test.user_id=user.id WHERE test.id='$test_id'";
  $results = mysqli_query($db, $query);
  $test = mysqli_fetch_assoc($results);
  $username = $_SESSION['username'];
  if($test['username']!=$username){
      header("location: profile.php");
  }
  $test_content = json_decode($test['content']);

  $pdf = new PDF_HTML();
  $pdf->SetAutoPageBreak(true,18);
  $pdf->SetTitle($test['test_name'].'.pdf', true);
  $pdf->AddPage();
  $pdf->AddFont('DejaVu','','DejaVuSerif.ttf',true);
  $pdf->SetFont('DejaVu','',16);
  $pdf->Cell(0,30,$test['test_name'], 0,1,'C');
  $pdf->SetFont('DejaVu','',14);
  $pdf->Cell(0,5,"Име:................................................................... Клас........ №:........", 0, 1, 'C');
  $pdf->Line(0, 50, $pdf->GetPageWidth(), 50);
  $points = 0;
  $test_content = array_filter($test_content, function($question){
    if($question->type!='file'){
      return true;
    }
    else{
      return false;
    }
  });
  foreach ($test_content as $question) {
      $points += (int) $question->points;
  }
  $pdf->SetTextColor(100, 100, 100);
  if($test['time_limit']!='0'){
    $pdf->Cell(0,25, "Време за решаване: ".$test['time_limit']."мин. -  Брой Точки: ".$points, 0, 0, 'C');
  }
  else{
    $pdf->Cell(0,25, "Брой Точки: ".$points, 0, 0, 'C');
  }
  $pdf->SetTextColor(0, 0, 0);
  $pdf->SetFont('DejaVu','',11);
  $pdf->Ln(30);
  for($i=1; $i<=count($test_content); $i++){
    $question = $test_content[$i-1];
    $pdf->Write(6, $i.'. '.$question->title);
    $pdf->Ln(5);
    $pdf->SetTextColor(100, 100, 100);
    $pdf->Write(11, $question->points.' т.');
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Ln(13);
    if($question->type=="radio"){
      for($a=1; $a<=count($question->answers); $a++){
        $pdf->Cell(8);
        $pdf->WriteHTML('<img src="'.$_SERVER['DOCUMENT_ROOT'].'/images/radio_button_unchecked.png" width="14"/>     '.$question->answers[$a-1]->text);
        $pdf->Ln(10);
      }
    }
    else if($question->type=="checkbox"){
      for($a=1; $a<=count($question->answers); $a++){
        $pdf->Cell(8);
        $pdf->WriteHTML('<img src="'.$_SERVER['DOCUMENT_ROOT'].'/images/check_box_unchecked.png" width="14"/>   '.$question->answers[$a-1]->text);
        $pdf->Ln(10);
      }
    }
    else if($question->type="text"){
      $line='';
      for($a=0; $a<456; $a++){
        $line.='.';
      }
      $pdf->Write(10, $line);
    }
  }
  $pdf->Output('I', $test['test_name'].'.pdf', true);
?>