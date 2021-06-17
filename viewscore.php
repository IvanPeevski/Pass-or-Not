<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
require($_SERVER['DOCUMENT_ROOT'].'/actions/functions/grading.php');
if(isset($_GET['viewscore'])){
  $pin= $_GET['viewscore'];
  $query = "SELECT * FROM response WHERE pin='$pin'";
  $response = mysqli_fetch_assoc(mysqli_query($db, $query));
  $testId = $response['test_id'];
  $query = "SELECT * FROM test INNER JOIN user ON test.user_id=user.id WHERE test.id='$testId'";
  $results = mysqli_query($db, $query);
  $test= mysqli_fetch_assoc($results);

  $limit_answers = false;
  if($test['limit_check']=='1'){
    if($_SESSION['username']==$test['username']){
      $limit_answers = false;
    }
    else{
      if($response['visited']!='1'){
        $limit_answers = false;
        $query = "UPDATE `response` SET visited='1' WHERE pin='$pin'";
        mysqli_query($db, $query);
      }
      else{
        $limit_answers = true;
      }
      
    }
  }
  $response_content = json_decode($response['content']);
  $test_content = json_decode($test['content']);
  $grade_info = grade_test($response_content, $test_content, $test['grading']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $test['test_name'].' - '. $response['name']?></title>
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
    <script>
      const max_points = <?php echo $grade_info->maxpoints ?>;
      const points = <?php echo $grade_info->points ?>;
      const undecided = <?php echo $grade_info->undecided ?>;
      const grade_text = '<?php echo $grade_info->grade_text ?>';
      const grade = '<?php echo $grade_info->grade ?>';
      const pin =  '<?php echo $pin?>';
      window.onload = function() {
        Array.from(document.getElementsByClassName('image')).forEach(img => img.addEventListener('click',
            function() {
                document.getElementById("img").src = this.src;
                document.getElementById("imgModal").style.display = "block";
                document.getElementById("caption").innerHTML = this.alt
            }))
            drawChart(150, 150, 125, 25, max_points, points, undecided, grade_text, grade);
      };
      function gradeQuestion(el){
        $.ajax({
          type: 'POST', 
          url: '/actions/crud/form_update.php',
          data: {
            comment: el.parentNode.querySelector('div textarea').value,
            points: el.parentNode.querySelector('div input').value, 
            id: el.id, 
            pin: pin}, 
          success:function(){
            location.reload();
          }
        })
      }
    </script>
</head>

<body style="background-color: #f2f2f2;">
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
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
                        <div class="btn btn-light"><a class="nav-link" href="" style="padding:0">Обнови<img
                                    src="images/refresh.svg" width="33" height="33"></a></div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <?php 
  if(isset($_GET['viewscore'])){
    if($response){
      ?>
        <div class="center" style="margin-top:45px;text-align: center;">
            <h1 style="margin-bottom:25px;"><?php echo $test['test_name']?></h1>
            <h3 class="text" style="width:100%"><?php echo $response['name'].', '.$response['class'].$response['division']?></h3>
        </div>
        <?php if($test['check_points']=='1'){
          ?>
          <div class="center score-container">
            <div class="score-text">
              <p>Вашата оценка е <strong><?php echo "$grade_info->grade_text($grade_info->grade)"?></strong></p>
              <p>
                Получени точки: 
                <strong><span style="color:green"><?php echo $grade_info->points?></span> / <?php echo $grade_info->maxpoints?></strong><br>
                <strong><span style="color:orange"><?php echo $grade_info->undecided ? $grade_info->undecided : 0 ?></span> недооценени</strong>
              </p>
              <p><?php if($response['state']=='halted'){echo 'Прекратен';}else{echo 'Изпратен';}?> в <strong><?php echo date("H:i d/m/y", strtotime($response['sent_on']))?></strong></p>
              <p>Формула за оценяване:<b>
                <?php 
                if($test['grading']=='f1'){echo '=2 +( 4 х R:М)';}
                else{echo '=(R x 6):M';}
                ?>
                </b>
                <br/>
                <span style="font-size:17px;">R-получени точки; М- максимални точки</span>
              </p>
            </div>
            <div><canvas id="canvas" width=325 height=325></canvas></div>
          </div>
          <?php
        } ?>
        <?php if($test['check_answers']=='1' && $limit_answers==false){
          ?> 
          <div id="questions" class="center" style="margin-top:4px;">
            <?php
              foreach($response_content as $key=>$answer){
                if(isset($answer->id)){
                  $index = $answer->id;
                }
                else{
                  $index = $key;
                }
                $question = $test_content[$index];
                ?>
                <div style="margin-top:10px;" class="d-flex">
                  <div style="width:100%">
                    <div class="question-title d-flex">
                      <?php 
                        echo '<div class="flex-grow-1"><h3>'.$question->title.'</h3></div>';
                      ?>
                    </div>
                    <?php 
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
                    <div style="margin-top:10px; border-bottom: solid gray 3px;">
                      <?php
                      if($question->type=="radio" || $question->type=="checkbox"){
                        $question_answers = $question->answers;
                        ?>
                          <table class="table table-hover" style="text-align: center;">
                              <thead>
                                  <tr>
                                      <th scope="col" style="width:30px;">Вашите отговори</th>
                                      <th scope="col">Правилни отговори</th>
                                  </tr>
                              </thead>  
                              <tbody>
                                  <?php 
                                  for($a=0; $a<count($question_answers);$a++){
                                    ?>
                                    <tr>
                                      <td class="
                                      <?php 
                                        if($question->type=="radio" && 
                                        ((property_exists($answer, 'value') && (int)$answer->value == $a && $answer->value!=null) || (!property_exists($answer, 'value') && (int)$answer==$a && $answer!=null))){
                                          if(grade_question($answer, $question)->correct){
                                            echo 'answer-correct';
                                          }else{
                                            echo 'answer-false';
                                          }
                                        }
                                        else if($question->type=="checkbox" && in_array($question_answers[$a]->id, $answer->value)){
                                          if(in_array($question_answers[$a]->id, grade_question($answer, $question)->correct_arr)){
                                            echo 'answer-correct';
                                          }else{
                                            echo 'answer-false';
                                          }
                                        }
                                      ?>">
                                        <?php 
                                          if($question->type=="radio" && ((property_exists($answer, 'value') && (int)$answer->value == $a && $answer->value!=null) || (!property_exists($answer, 'value') && (int)$answer==$a && $answer!=null))){
                                            if(grade_question($answer, $question)->correct){
                                              ?>
                                              <img src="images/check.svg" width="30"/>
                                              <?php
                                            }
                                            else{
                                              ?>
                                              <img src="images/cross.svg" width="30"/>
                                              <?php
                                            }
                                          }
                                          else if($question->type=="checkbox" && in_array($question_answers[$a]->id, $answer->value)){
                                            if(in_array($question_answers[$a]->id, grade_question($answer, $question)->correct_arr)){
                                              ?>
                                              <img src="images/check.svg" width="30"/>
                                              <?php
                                            }
                                            else{
                                              ?>
                                              <img src="images/cross.svg" width="30"/>
                                              <?php
                                            }
                                          }
                                        ?>
                                      </td>               
                                      <td class="<?php if($question_answers[$a]->correct=='true'){echo 'answer-correct';}?>">
                                        <?php 
                                          echo $question_answers[$a]->text;
                                          
                                          if (isset($question_answers[$a]->file)) {
                                            $file_id = $question_answers[$a]->file;
                                            $query = "SELECT * FROM `file` WHERE `id`=$file_id";
                                            $results = mysqli_query($db, $query);
                                            $sent_file = mysqli_fetch_assoc($results);
                                              if (strpos(mime_content_type($_SERVER['DOCUMENT_ROOT'].$sent_file['href']), 'image') !== false) {
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
                                  <tr>
                                      
                                  </tr>
                              </tbody>
                          </table>
                        <?php
                      }
                      else if($question->type=="text"){
                        ?>
                        <div class="d-flex justify-content-around flex-wrap" style=" padding-left:5px; font-size:20px;">
                          <?php
                          if(isset($answer->comment)){
                            ?>
                            <div>Коментар от учителя: <?php echo $answer->comment?></div>
                            <?php
                          }
                          ?>
                          <div><b>Отговор на ученика:</b><div style="text-indent:10px"><?php echo $answer->text?></div></div>
                          <div><b>Правилни отговори:</b><div><?php echo implode('; ',array_map(function($obj){return $obj->text;},$question->answers))?></div></div>
                        </div>
                        <?php
                      } 
                      else if($question->type=="file"){
                        if($answer->file!=null){
                          $fileId=$answer->file;
                          $file_query="SELECT * FROM `file` WHERE id='$fileId'";
                          $sent_file = mysqli_fetch_assoc(mysqli_query($db, $file_query));
                          if(substr(mime_content_type($_SERVER['DOCUMENT_ROOT'].$sent_file['href']),0,5) == 'image'){
                            ?>
                            <img src="<?php echo $sent_file['href']?>" class="zoom-image" alt="<?php $sent_file['name']?>">
                            <?php
                          }
                          else{
                            ?>
                            <a href="<?php echo $sent_file['href']?>" download><?php echo $sent_file['name']?></a>
                            <?php
                          }
                        }
                        else{
                          ?>
                          <h4>Липсва прикачен файл</h4>
                          <?php
                        }
                      }
                      if(isset($question->info) && $question->info!='' && $question->info!='undefined'){
                        ?>
                        <a type="button" class="btn btn-outline-info"
                          data-toggle="collapse" href="#collapse-<?php echo $question->id?>" role="button" aria-expanded="false" aria-controls="collapse<?php echo $question->title?>?>">
                          Прегледай обяснение
                        </a >
                        <div class="collapse" id="collapse-<?php echo $question->id?>" 
                        style="text-align:center; font-size:20px; font-weight:bold; border: 1px solid gray; border-bottom:none;">
                            <?php echo $question->info;?>
                        </div>  
                        <?php
                      }
                      ?>
                    </div>
                  </div>
                  <div>
                   <?php
                   if(($question->type=="text"||$question->type=="file") && $_SESSION['username']==$test['username']){
                    ?>
                    <h3>Оценяване</h3>
                    <div class="d-flex justify-content-around" style="color: gray;">
                      <div>
                        <textarea placeholder="Оставете коментар..." rows="3"><?php if(isset($answer->comment)){echo $answer->comment;}?></textarea>
                      </div>
                      <div>
                          <input type="number" value="<?php echo $answer->points?>" min="0"
                            oninput="validity.valid||(value='0');" style="width:50px;"/> 
                            / <?php echo $question->points?> точки
                      </div>
                      <button type="button" class="btn btn-sm btn-primary" style="margin:3px" data-toggle="tooltip"
                          data-placement="bottom" title="Оцени" id="<?php echo $question->id?>"
                          onclick="gradeQuestion(this)">
                          <img src="images/save.svg">
                      </button>
                    </div>
                    <?php
                    }
                   ?> 
                  </div>
                </div>
                <?php
              }
            ?>
          </div>
          <?php 
        } ?>
      <?php
    }
  }
?>
  <div id="imgModal" class="imgModal" onclick="this.style.display='none'" style="z-index:2;">
      <span class="close" onclick="document.getElementByID('imgModal').style.display='none'">&times;</span>
      <img class="imgModal-content" id="img">
      <div id="caption"></div>
  </div>
  <!-- JavaScript --->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
      integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
  </script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
  </script>
  <script src="/js/chart_draw_script.js"></script>
</body>
</html>
<?php include($_SERVER['DOCUMENT_ROOT'].'\actions\db_close.php');?>