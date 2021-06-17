<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    $name = $_GET['name'];
    $class = $_GET['class'];
    $division = $_GET['division'];
    $pin = $_GET['testId'];
    if (isset($_GET['testId']) && (isset($_GET['name']) && $_GET['name']!='')&& (isset($_GET['class']) && $_GET['class']!='') && (isset($_GET['division']) && $_GET['division']!='')) {
        $query = "SELECT * FROM test INNER JOIN user ON test.user_id = user.id WHERE pin='$pin'";
        $results = mysqli_query($db, $query);
        $test = mysqli_fetch_assoc($results);
    }
    else{
        header("location: message.php?type=inv_data&testId=$pin&name=$name&class=$class&division=$division");
    }

    if(!$test || $test['unlocked'] != '1'){
        header("location: message.php?type=not_found&testId=$pin&name=$name&class=$class&division=$division");
    }
    else if($test['allow_anonymous'] && $_GET['guest']=='true'){
        header("location: message.php?type=no_annonymous&testId=$pin&name=$name&class=$class&division=$division");
    }
    else if($test['require_profile'] && !isset($_SESSION['username'])){
        header("location: message.php?type=auth&path=quiz&testId=$pin&name=$name&class=$class&division=$division");
    }
    
    $content = json_decode($test['content']);
    if($test['randomize_questions']=='1'){
        shuffle($content);
    }

    if($test['question_limit']!='0'){
        shuffle($content);
        $limit = min(count($content), (int)$test['question_limit']);
        $content = array_slice($content, 0, $limit);
    }
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo $test['test_name'] ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/quiz_styles.css" rel="stylesheet">
    <?php if($test['one_per_one']=='1'){?><link href="/css/response_styles.css" rel="stylesheet"><?php }?>
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/site.webmanifest">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
</head>

<body style="background-color: #f2f2f2;min-height: 100vh;" class="d-flex justify-content-center align-items-center">
    <?php 
        if($test['one_per_one']=='0'){
            ?>
                <form action="/actions/crud/form_save.php" method="post" id="quiz" enctype="multipart/form-data" onSubmit="return SubmitRequest()">
                    <nav class="navbar navbar-light bg-light fixed-top" style="border-bottom:solid 2px #ccc">
                        <div class="container" style='padding:0;'>
                            <ul class="nav navbar-nav pull-sm-left">
                                <li class="nav-item">
                                    <a class="navbar-brand js-scroll-trigger" href="/index.php">
                                        <img src="/images/icon.png" width="25px" height="25px">
                                        Pass or Not
                                    </a>
                                </li>
                            </ul>
                            <ul class="nav navbar-nav navbar-logo mx-auto">
                                <li class="nav-item">
                                    <div><img src="/images/hourglass_bottom-black.svg" width="34" height="34" /> 
                                    <b id="time" style="font-size:22px;">
                                        <?php 
                                            if ($test['time_limit'] != '0') {
                                                echo $test['time_limit'] . ':00';
                                            } else {
                                                echo '∞';
                                            } 
                                        ?>
                                    </b>
                                    </div>
                                </li>
                            </ul>
                            <ul class="nav navbar-nav pull-sm-right">
                                <li class="nav-item">
                                    <input type="submit" name="submit" id="submit" value="Изпрати" class="btn btn-primary" />
                                </li>
                            </ul>
                        </div>
                    </nav>
                    <div class="center" style="margin-top:45px;text-align: center;">
                        <h1><?php echo $test['test_name'] ?></h1>
                        <div style="color:red;">*- Задължителен въпрос</div>
                        <div>
                            <input type="hidden" name="test_pin" value="<?php echo $_GET['testId'] ?>">
                            <input type="hidden" name="name_field" value="<?php echo $_GET['name'] ?>">
                            <input type="hidden" name="class" value="<?php echo $_GET['class'] ?>">
                            <input type="hidden" name="division" value="<?php echo $_GET['division'] ?>">
                            <input type="hidden" name="guest" value="<?php echo $_GET['guest'] ?>">
                        </div>
                    </div>
                    <div id="questions" class="center" style="margin-top:4px;">
                        <?php
                        for ($i = 0; $i < count($content); $i++) {
                        ?>
                            <div class="questionBody <?php if($content[$i]->mandatory=='true'){echo 'required';}?>" id="question-<?php echo $content[$i]->id?>" >
                                <div class="questionTitle">
                                    <?php echo $content[$i]->title; ?> <?php if($content[$i]->mandatory=='true'){echo '<span style="color:red;">*</span>';}?>
                                </div>
                                <?php
                                if (isset($content[$i]->file)) {
                                    $file_id = $content[$i]->file;
                                    $query = "SELECT * FROM `file` WHERE `id`=$file_id";
                                    $results = mysqli_query($db, $query);
                                    $file = mysqli_fetch_assoc($results);
                                    if (strpos(mime_content_type($_SERVER['DOCUMENT_ROOT'].$file['href']), 'image') !== false) {
                                ?>
                                        <img src="<?php echo $file['href'] ?>" class="zoom-image" alt="<?php $file['name'] ?>">
                                    <?php
                                    } else {
                                    ?>
                                        <a href="<?php echo $file['href'] ?>" download><?php echo $file['name'] ?></a>
                                <?php
                                    }
                                }
                                ?>
                                <div class="footer">
                                    <div style="color: gray; font-size: 16px;">
                                            <?php echo $content[$i]->points ?> точки
                                    </div>
                                </div>
                                <div class="inputGroup">
                                    <?php
                                    if ($content[$i]->type == "radio" || $content[$i]->type == "checkbox") {
                                        $answers = $content[$i]->answers;
                                        if($test['randomize_answers']=='1'){
                                            shuffle($answers);
                                        }
                                        for ($a = 0; $a < count($answers); $a++) {
                                        ?>
                                            <div class="answerBody">
                                                <input type="<?php echo $content[$i]->type; ?>" class="answer question-<?php echo $content[$i]->id; ?>-answers" 
                                                name="question-<?php echo $content[$i]->id; ?>-answers<?php if (json_decode($test['content'])[$i]->type == "checkbox") {
                                                        echo '[]';
                                                    } ?>" id="question-<?php echo $content[$i]->id; ?>-answers-<?php echo $answers[$a]->id; ?>" value="<?php echo $answers[$a]->id; ?>" style="visibility:hidden"/>
                                                <label for="question-<?php echo $content[$i]->id; ?>-answers-<?php echo $answers[$a]->id; ?>" class="option"><?php echo $answers[$a]->text ?>

                                                    <?php
                                                    if (isset($answers[$a]->file)) {
                                                        $file_id = $answers[$a]->file;
                                                        $query = "SELECT * FROM `file` WHERE`id`=$file_id";
                                                        $results = mysqli_query($db, $query);
                                                        $file = mysqli_fetch_assoc($results);
                                                        if(strpos(mime_content_type($_SERVER['DOCUMENT_ROOT'].$file['href']), 'image') !== false) {
                                                    ?>
                                                            <img src="<?php echo $file['href'] ?>" class="zoom-image" alt="<?php $file['name'] ?>">
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <a href="<?php echo $file['href'] ?>" download><?php echo $file['name'] ?></a>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </label>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    <?php
                                    } else if ($content[$i]->type == "text") {
                                    ?> <textarea onkeyup="auto_grow(this)" class="question question-<?php echo $content[$i]->id; ?>-answers" placeholder="Отговор" 
                                    name="question-<?php echo $i; ?>-answers" style="margin:0" id="question-<?php echo $content[$i]->id; ?>-answers-<?php echo $answers[$a]->id; ?>"></textarea>
                                    <?php
                                    } else if ($content[$i]->type == "file") {
                                    ?>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input question-<?php echo $content[$i]->id; ?>-answers" name="question-<?php echo $i; ?>-answers" 
                                            id="question-<?php echo $content[$i]->id; ?>-answer" 
                                            onchange="this.parentNode.children[1].innerText=this.files[0].name">
                                            <label class="custom-file-label" for="question-<?php echo $content[$i]->id; ?>-answer">Не е избран файл</label>
                                        </div>
                                        <input style="display:none" name="question-<?php echo $content[$i]->id; ?>-answers">
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </form>
            <?php
        }
        else{
            ?>
            <div class="box">
                <div class="modal-header">
                    <span style="margin:auto;">
                    <img src="images/hourglass_bottom-black.svg" width="34" height="34" />
                    <b id="time">
                        <?php 
                            if ($test['time_limit'] != 0) {
                                echo $test['time_limit'] . ':00';
                            } else {
                                echo '∞';
                            } 
                        ?>
                    </b>
                    </span>
                </div>
                <div class="box-body" style="text-align:left; padding-left: 10px; padding-right: 10px;padding-top:10px;">
                    <div style="border-bottom: 2px solid gray">
                        <h2><?php echo $test['test_name'] ?></h2>
                    </div>
                    <div style="color:gray; font-size: 20px;"><?php echo $test['tags']?></div>
                    <div class="info">
                        <p style="color:red">След като изпратите отговор на въпрос, не можете да се върнете.<br/><b>Решавайте внимателно!</b></p>
                        <p style="color:gray; font-size: 20px;">
                        Създаден от <?php echo $test['first_name'].' '.$test['surname']?><br/>
                        Продължителност:
                        <?php 
                            if ($test['time_limit'] != '0') {
                                echo $test['time_limit'] . ':00';
                            } else {
                                echo '∞';
                            } 
                        ?> мин.<br/>
                        Брой въпроси: <?php echo count($content)?><br/>
                        Формула за оценяване: 
                        <?php 
                        if($test['grading']=='f1'){echo '=2 +( 4 х R:М)';}
                        else{echo '=(R x 6):M';}
                        ?>
                        <br/>
                        <span style="font-size:16px;">R-получени точки; М- максимални точки</span>
                        </p>
                    </div>
                </div>
                <div class="progress" style="height: 7px;">
                        <div class="progress-bar" id="progressbar" role="progressbar" style="width:0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="<?php echo count($content)?>"></div>
                </div>
                <div class="modal-footer container">
                    <div class="mr-auto"><b>Въпрос <span id="question_count">0</span>/<span id="max_question"><?php echo count($content)?></span></b></div>
                    <div><input type="button" name="submit" id="submit" onclick="Begin()" value="Започни" class="btn btn-primary" /></div>
                </div>
            </div>
            <?php
        }?>
    <div id="imgModal" class="imgModal" onclick="this.style.display='none'" style="z-index:2;">
        <span class="close" onclick="document.getElementByID('imgModal').style.display='none'">&times;</span>
        <img class="imgModal-content" id="img">
        <div id="caption"></div>
    </div>
    <!-- Bootstrap --->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <?php if($test['one_per_one']=='0'){?><script src="/js/multi_page_test_script.js"></script><?php }
    else{
        ?><script src="/js/single_page_test_scripts.js"></script>
        <script src="/js/chart_draw_script.js"></script>
        <?php 
    }?>
</body>

</html>
<?php include($_SERVER['DOCUMENT_ROOT'].'\actions\db_close.php');?>