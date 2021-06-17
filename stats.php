<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/grading.php');
    if(isset($_GET['id'])){
        $id=$_GET['id'];
        $query = "SELECT * FROM test INNER JOIN user ON test.user_id=user.id WHERE test.id='$id'";
        $results = mysqli_query($db, $query);
        $test = mysqli_fetch_assoc($results);
        $username = $_SESSION['username'];
        if($test['username']!=$username){
            header("location: profile.php");
        }
    }
    else{
        header("location: profile.php");
    }
    $test_content = json_decode($test['content']);
    
    $responses_query = "SELECT * FROM response WHERE test_id='$id'";
    $result = mysqli_query($db, $responses_query);
    $responses = array();
    $grades = array("6"=>0, "5"=>0, "4"=>0, "3"=>0, "2"=>0);
    $total_grade = 0;
    $undecided_responses = 0;
    while( $response = mysqli_fetch_assoc($result)){
        array_push($responses, $response);
        $content = json_decode($response['content']);
        $graded = grade_test($content, $test_content, $test['grading']);
        $total_grade += $graded->grade;
        if($graded->undecided>0){$undecided_responses++;}

        if($graded->grade<3){$grades["2"]++;}
        else if($graded->grade<3.5){$grades["3"]++;}
        else if($graded->grade<4.5){$grades["4"]++;}
        else if($graded->grade<5.5){$grades["5"]++;}
        else if($graded->grade<=6){$grades["6"]++;}
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $test['test_name']. ' | Статистика'?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/response_styles.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/site.webmanifest">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
            var data = google.visualization.arrayToDataTable([
          ['Оценка', 'Брой'],
          ['Отличен',<?php echo $grades['6']?>],
          ['Мн. Добър',<?php echo $grades['5']?>],
          ['Добър',  <?php echo $grades['4']?>],
          ['Среден', <?php echo $grades['3']?>],
          ['Слаб',    <?php echo $grades['2']?>]
        ]);

        var options = {
          title: 'Оценки'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart'));

        chart.draw(data, options);
        }
    </script>
</head>

<body style="background-color: #f2f2f2;">
  <!-- Navigation -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand js-scroll-trigger" href="index.php">
              <img src="/images/icon.png" width="25px" height="25px">
              Pass or Not
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive"
                aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <div class="btn btn-light"><a class="nav-link" href="profile.php"
                                style="padding:0"><?php echo $_SESSION['username'] ?><img src="images/person-black.svg"
                                    width="33" height="33"></a></div>
                    </li>
                    <li class="nav-item">
                        <div class="btn btn-light"><a class="nav-link" href="" style="padding:0">Обнови<img
                                    src="images/refresh.svg" width="33" height="33"></a></div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="center">
      <h1 style="text-align: center;"><?php echo $test['test_name']?></h1>
      <div id="piechart" style="height:400px;"></div>
      <div class="d-flex justify-content-around flex-wrap" style="font-size:18px">
        <div>Изпратени резултати: <strong><?php echo count($responses)?></strong></div>
        <div style="font-size:21px"><strong>СРЕДЕН УСПЕХ: <?php echo number_format((float)$total_grade/count($responses), 2, '.', '');?></strong></div>
        <div>Недооценени резултати: <strong style="color:orange"><?php echo $undecided_responses?></strong></div>
      </div>
      
    </div>
    <div id="questions" class="center" style="margin-top:4px;">
    
        <?php 
        for($i=0; $i<count($test_content); $i++){
            $question = $test_content[$i];
            ?>
            <div class="question-body">
              <div class="question-title">
                <?php
                    echo "<h3>$question->title</h3>";
                    if(isset($question->file)){
                        $index=$question->file;
                        $file_query = "SELECT * FROM `file` WHERE `id`=$index";
                        $result = mysqli_query($db, $file_query);
                        $file = mysqli_fetch_assoc($result);
                        if(substr($file['href'], strrpos($file['href'], '.')+1)=='jpg' || substr($file['href'], strrpos($file['href'], '.')+1)=='png' || substr($file['href'], strrpos($file['href'], '.')+1)=='jpeg'){
                          ?>
                          <img src="<?php echo $file['href']?>" class="image" alt="<?php $file['name']?>">
                          <?php
                        }
                        else{
                          ?>
                          <a href="<?php echo $file['href']?>" download><?php echo $file['name']?></a>
                          <?php
                        }
                    }
                ?>
              </div>
              <?php 
                if($question->type=="radio" || $question->type=="checkbox"){
                  $question_answers = $question->answers;
                  ?>
                  <div style="margin-top:10px; border-bottom: solid gray 5px;">
                    <table class="table table-hover table-bordered " style="text-align: center;">
                        <thead>
                            <tr>
                                <th scope="col" style="width:30px;">Избирамеост</th>
                                <th scope="col">Отговори</th>
                            </tr>
                        </thead>  
                        <tbody>
                            <?php
                            $responses_answered = array_filter($responses, function($obj) use ($question){
                                $response_content = json_decode($obj["content"]);
                                for($iteration=0; $iteration<count($response_content); $iteration++){
                                    $response_answer = $response_content[$iteration];
                                    if(isset($response_answer->id) && $response_answer->id == $question->id){
                                        return true;
                                    }
                                    else if(!isset($response_answer->id) && $iteration == $question->id && $response_answer!=null){
                                        return true;
                                    }

                                }
                            });
                            for($a=0; $a<count($question_answers);$a++){
                              ?>
                              <tr>
                                <td>
                                  <?php
                                    $picked_answer = array_filter($responses_answered, function($obj) use ($question, $a){
                                      $response_content= json_decode($obj['content']);
                                      $item = null;
                                      foreach($response_content as $resp) {
                                          if ($resp->id == $question->id) {
                                              $item = $resp;
                                              break;
                                          }
                                      }
                                      if($question->type=="radio"){
                                        if(isset($item->value) && $item->value == $a){
                                          return true;
                                        }
                                        else if(!isset($item->value) && $item == $a){
                                            return true;
                                        }
                                        else{
                                            return false;
                                        }
                                      }
                                      else if($question->type=="checkbox"){
                                        if(isset($item->value) && in_array($a, $item->value)){
                                          return true;
                                        }
                                        else if(!isset($item->value) && in_array($a, $item)){
                                            return true;
                                        }
                                        else{
                                            return false;
                                        }
                                      }
                                    });
                                    $picked_rate = count($picked_answer)/count($responses_answered)*100;
                                    if(is_nan($picked_rate)){$picked_rate=0;}
                                    echo round($picked_rate,2).' %'
                                  ?>
                                </td>               
                                <td class="<?php if($question_answers[$a]->correct=='true'){echo 'answer-correct';}?>">
                                  <?php 
                                    echo $question_answers[$a]->text;
                                    if (isset($question_answers[$a]->file)) {
                                      $file_id = $question_answers[$a]->file;
                                      $query = "SELECT * FROM `file` `id`=$file_id";
                                      $results = mysqli_query($db, $query);
                                      $sent_file = mysqli_fetch_assoc($results);
                                        if (mime_content_type($sent_file['href']) == 'image') {
                                          ?>
                                            <img src="<?php echo $sent_file['href'] ?>" class="zoom-image" alt="<?php $sent_file['name'] ?>">
                                          <?php
                                        } else {
                                          ?>
                                            <a href="<?php echo $sent_file['href'] ?>" download><?php echo $sent_file['name'] ?></a>
                                          <?php
                                        }
                                    }
                                  ?> 
                                </td>
                              </tr>
                              <?php 
                            }?>
                        </tbody>
                    </table>
                    <?php
                      $correct_responses = array_filter($responses_answered, function($obj) use ($question){
                          $response_content = json_decode($obj['content']);
                          $item = null;
                          foreach($response_content as $resp) {
                              if ($resp->id == $question->id) {
                                  $item = $resp;
                                  break;
                              }
                          }
                          return grade_question($item, $question)->correct;
                      });
                      $success_rate = count($correct_responses)/count($responses_answered)*100;
                      if(is_nan($success_rate)){$success_rate=0;}
                    ?>
                    <div class="d-flex justify-content-between flex-wrap" style="font-size: 20px; font-weight: bolder">
                      <div>Успеваемост: <?php echo round($success_rate,2)?>%</div>
                      <a type="button" class="btn btn-outline-info"
                       data-toggle="collapse" href="#collapse-<?php echo $question->id?>" role="button" aria-expanded="false" aria-controls="collapse<?php echo $question->title?>?>">
                       ПРЕГЛЕДАЙ ВСИЧКИ РЕЗУЛТАТИ ↓
                      </a >
                    </div>
                    <div class="collapse" id="collapse-<?php echo $question->id?>">
                      <div class="d-flex justify-content-around flex-wrap"  style="background-color:white; border: 2  px solid black;">
                        <div>
                          <h3>Правилно отговорили:</h3>
                          <ul>
                            <?php
                              foreach($correct_responses as $response){
                                $name = $response['name'];
                                $pin = $response['pin'];
                                echo "<li>
                                $name
                                <a class='btn btn-sm btn-outline-dark' href='viewscore.php?viewscore=$pin' target='_blank'><img src='images/visit.svg'/>Прегладай</a>
                                </li>";
                              }
                            ?>
                          </ul>
                        </div>
                        <div>
                          <h3>Грешно отговорили:</h3>
                          <ul>
                            <?php
                              $wrong_responses = array_filter($responses_answered, function($obj) use ($correct_responses){
                                $bool = true;
                                foreach($correct_responses as $correct_resp) {
                                  if($obj['pin'] == $correct_resp['pin']){
                                    $bool = false;
                                  }
                                }
                                return $bool;
                              });
                              foreach($wrong_responses as $response){
                                $name = $response['name'];
                                $pin = $response['pin'];
                                echo "<li>
                                $name
                                <a class='btn btn-sm btn-outline-secondary' href='viewscore.php?viewscore=$pin' target='_blank'><img src='images/visit.svg'/>Прегладай</a>
                                </li>";
                              }
                            ?>
                          </ul>
                        </div>
                      </div>
                    </div>
                </div>
                  <?php
                }
                else if($question->type=="text"){
                  ?> <textarea onkeyup="auto_grow(this)" class="question" readonly
                        name="question-<?php echo $i;?>-answers"
                        id="question-<?php echo $i;?>-answers-0">
                      </textarea>
                  <?php
                } 
                else if($question->type=="file"){
                  echo '...';
                }
              ?>
            </div>
            <?php
        } ?>
    </div>
    <!-- Bootstrap --->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <?php include($_SERVER['DOCUMENT_ROOT'].'\actions\db_close.php');?>
</body>

</html>