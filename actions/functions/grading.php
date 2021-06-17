<?php
  function grade_question($answer, $question){
    $object = new stdClass();
    $object->correct = false;
    $object->points = 0;
    $object->undecided = 0;
    if((isset($answer->id) && (isset($answer->value) || isset($answer->file) || isset($answer->text))) || !isset($answer->id)){
      if($question->type=='radio'){
        if(isset($answer->id)){
          if($question->answers[(int)$answer->value]->correct=='true' && !is_null((int)$answer->value)){
            $object->correct = true;
            $object->points+= (int)$question->points;
          }
        }
        else{
          if($question->answers[(int)$answer]->correct=='true' && !is_null((int)$answer)){
            $object->correct = true;
            $object->points+= (int)$question->points;
          }
        }
      }
      else if($question->type=='checkbox'){
        $bool=true;
        $object->correct_arr = [];
        if(isset($answer->id)){
          foreach($question->answers as $question_answer) {
            if($question_answer->correct=='true'){
              if(is_null($answer->value)){
                $bool=false;
              }
              else if(!in_array((int)$question_answer->id, $answer->value)){
                $bool=false;
              }
              else{
                array_push($object->correct_arr, $question_answer->id);
              }
            }
            else{
              if(in_array((int)$question_answer->id, $answer->value)){
                $bool=false;
              }
            }
          }
        }
        else{
          foreach($question->answers as $question_answer) {
            if($question_answer->correct=='true'){
              if(is_null($answer)){
                $bool=false;
              }
              else if(!in_array(strval($question_answer->id), $answer)){
                $bool=false;
              }
              else{
                array_push($object->correct_arr, $question_answer->id);
              }
            }
            else{
              if($answer==null){
                $bool=false;
              }
              else if(in_array(strval($question_answer->id), $answer)){
                $bool=false;
              }
            }
          }
        }
        if($bool){
          $object->points+= (int)$question->points;
        }
        $object->correct=$bool;
      }
      else if($question->type=='text'){
        if($answer->graded==true){
          if($answer->points>=$question->points){
            $object->correct = true;
          }
          $object->points+=(int)$answer->points;
          $object->graded = true;
        }
        else{
          foreach($question->answers as $question_answer){
              if(($question->case_sensitive=='false' && strtolower($answer->text) == strtolower($question_answer->text)) || $answer->text == $question_answer->text){
                $object->correct = true;
                $object->graded = true;
              }
              else{
                $object->graded = false;
              }
          }
          if($object->correct == true){
            $object->points+=(int)$question_answer->points;
          }
          else{
            $object->undecided+=(int)$question->points;
            $object->correct = false;
          }
        }
      }
      else{
        if($answer->graded==true){
          $object->points+=(int)$answer->points;
        }
        else{
          $object->undecided+=(int)$question->points;
        }
        $object->graded = $answer->graded;
      } 
    }

    return $object;
  }
  function grade_test($answer_sheet, $test, $formula, $state=''){
    $points = 0;
    $maxpoints=0;
    $undecided=0;

    foreach($answer_sheet as $key=>$answer){
      if(isset($answer->id)){
        $index = $answer->id;
      }
      else{
        $index = $key;
      }
      $question = $test[$index];
      $maxpoints += (int)$question->points;
      $graded_answer = grade_question($answer, $question);
      $points += $graded_answer->points;
      if(isset($graded_answer->graded) && $graded_answer->graded==false){
        $undecided += $graded_answer->undecided;
      }
    }

    $grade=0;
    $grade_text='';
    if($maxpoints==0){$grade_text='Липсват точки';}
    else{
      if($formula=='f1'){$grade=round(($points/$maxpoints)*4+2, 2);}
      else if($formula=='f2'){$grade=round(($points/$maxpoints)*6, 2);}
      
      if($grade<2){$grade=2;}

      if($state=='active'){$grade_text="Решава в момента";}
      else if($undecided>0){$grade_text='Недооценен';}
      else if($grade<3){$grade_text='Слаб';}
      else if($grade<3.5){$grade_text='Среден';}
      else if($grade<4.5){$grade_text='Добър';}
      else if($grade<5.5){$grade_text='Мн. добър';}
      else if($grade<=6){$grade_text='Отличен';}
    }

    $object = new stdClass();
    $object->grade = $grade;
    $object->grade_text = $grade_text;
    $object->points = $points;
    $object->maxpoints = $maxpoints;
    $object->undecided = $undecided;
    return $object;
  }
?>