<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
if(!isset($_SESSION['username'])){
    header("location: /index.php");
}
if(isset($_GET['id'])){
  $id=$_GET['testId'];
  $query = "SELECT * FROM test INNER JOIN user ON test.user_id=user.id WHERE test.id='$id'";
  $results = mysqli_query($db, $query);
  $result = mysqli_fetch_assoc($results);
  $username = $_SESSION['username'];
  if($result['username']!=$username){
    header("location: /index.php");
  }
}
?>
<!DOCTYPE html>
<html lang="bg">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Тестов Редактор</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/site.webmanifest">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/settings_styles.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/button_styles.css">
    <link rel="stylesheet" type="text/css" media="screen" href="/css/test_editor_styles.css">
</head>

<body>
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
                        <div class="btn btn-light"><a class="nav-link" href="profile.php"
                                style="padding:0"><?php echo $_SESSION['username'] ?><img src="images/person-black.svg"
                                    width="33" height="33"></a></div>
                    </li>
                    <li class="nav-item">
                        <div class="btn btn-light"><a class="nav-link" href="logout.php" style="padding:0">Изход<img
                                    src="images/exit-black.svg" width="33" height="33"></a></div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid" style="margin-top:65px;padding:0">
        <div id="heading"><textarea id="title" placeholder="Заглавие"></textarea></div>
        <div id="form">
            <div class="d-flex justify-content-between" style=" width:100%;">
                <div class="d-flex flex-wrap" id="question-category-container">
                    <span class="question-category-list" id="default-category" onclick="ToggleCategory(this)">Всички въпроси</span>
                </div>
                <div style="border-bottom: 3px solid #555;">
                    <div>Брой въпроси: <span id="question-count">0</span></div>
                    <div>Брой точки: <span  id="total-points">0</span></div>
                </div>
            </div>
            <div id="questions" style="width: 100%;"></div>
            <div class="d-flex justify-content-between">
                <button type="button" class="add"
                    onclick="OpenQuestionEditor()"><span>Добави Въпрос</span>
                </button>
                <button type="button" class="add add-green" data-toggle="tooltip" data-placement="bottom"
                    title="Използвайте въпрос от вече създадените Ви тестове"
                    onclick="OpenQuestionImport()"><span>Импортиране на Въпрос</span>
                </button>
            </div>
        </div>
    </div>
    </div>

    <!-- Question Editor-->
    <div class="modal fade" id="QuestionEditor" tabindex="-1" role="dialog" aria-labelledby="QuestionEditor" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ModalLabel">Добавяне на въпрос</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <textarea onkeyup="auto_grow(this)" class="question" placeholder="Въпрос"
                            id="question-title">
                        </textarea>
                        <div class="btn-list">
                        <button id="filemenu-0" class="btn btn-outline-secondary" type="button" data-toggle="collapse"
                            data-target="#fileattach-0" aria-expanded="false" aria-controls="fileattach-0">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-image" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M14.002 2h-12a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm-12-1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12z"/>
                                <path d="M10.648 7.646a.5.5 0 0 1 .577-.093L15.002 9.5V14h-14v-2l2.646-2.354a.5.5 0 0 1 .63-.062l2.66 1.773 3.71-3.71z"/>
                                <path fill-rule="evenodd" d="M4.502 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/>
                            </svg>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" onclick="InsertBold()" >
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-type-bold" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8.21 13c2.106 0 3.412-1.087 3.412-2.823 0-1.306-.984-2.283-2.324-2.386v-.055a2.176 2.176 0 0 0 1.852-2.14c0-1.51-1.162-2.46-3.014-2.46H3.843V13H8.21zM5.908 4.674h1.696c.963 0 1.517.451 1.517 1.244 0 .834-.629 1.32-1.73 1.32H5.908V4.673zm0 6.788V8.598h1.73c1.217 0 1.88.492 1.88 1.415 0 .943-.643 1.449-1.832 1.449H5.907z"/>
                            </svg>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" onclick="InsertItalic()">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-type-italic" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M7.991 11.674L9.53 4.455c.123-.595.246-.71 1.347-.807l.11-.52H7.211l-.11.52c1.06.096 1.128.212 1.005.807L6.57 11.674c-.123.595-.246.71-1.346.806l-.11.52h3.774l.11-.52c-1.06-.095-1.129-.211-1.006-.806z"/>
                            </svg>
                        </button>
                        <button class="btn btn-outline-secondary" type="button" onclick="InsertUnderline()">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-type-underline" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5.313 3.136h-1.23V9.54c0 2.105 1.47 3.623 3.917 3.623s3.917-1.518 3.917-3.623V3.136h-1.23v6.323c0 1.49-.978 2.57-2.687 2.57-1.709 0-2.687-1.08-2.687-2.57V3.136z"/>
                                <path fill-rule="evenodd" d="M12.5 15h-9v-1h9v1z"/>
                            </svg>
                        </button>
                    </div>
                    </div>
                    <div class="collapse" id="fileattach-0">
                        <div class="card card-body">
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="inputFile-0"
                                    onchange="LoadFile(this)">
                                <label class="custom-file-label" for="inputFile-0" id="inputFileName-0">Няма избран файл</label>
                            </div>
                            <img id="file-0" width="130px" height="130px">
                            <button type="button" id="btn-0" class="btn btn-warning"
                                onclick="RemoveFile(this.id.substr(this.id.indexOf('-')+1))">Премахни
                                файл</button>
                        </div>
                    </div>
                    <div class="category" style="margin-bottom:5px;">Видове отговор</div>
                    <div class="btn-group flex-wrap btn-group-toggle" data-toggle="buttons">
                        <label id="radio-choice" class="btn btn-outline-secondary choice-btn" onclick="DisplayRadioButtons()">
                            <input type="radio" class="option" name="question-type" value="radio">
                            <img class="choice-btn-icon" src="images/radio_button_checked.svg"/>
                            Единичен избор
                        </label>
                        <label id="checkbox-choice" class="btn btn-outline-secondary choice-btn" onclick="DisplayCheckBox()">
                            <input type="radio" class="option" name="question-type" value="checkbox">
                            <img class="choice-btn-icon" src="images/check_box.svg"/>
                            Множество избори
                        </label>
                        <label id="text-choice" class="btn btn-outline-secondary choice-btn" onclick="DisplayTextInput()">
                            <input type="radio" class="option" name="question-type" value="text">
                            <img class="choice-btn-icon" src="images/text_fields.svg"/>
                            Свободен отговор
                        </label>
                        <label id="file-choice" class="btn btn-outline-secondary choice-btn" onclick="DisplayFileInput()">
                            <input type="radio" class="option" name="question-type" value="file">
                            <img class="choice-btn-icon" src="images/attach_file.svg"/>
                            Прикачване на файл
                        </label>
                    </div>
                    <div>
                        <div id="text-settings" style="display:none; margin-bottom: 15px; border-bottom:2px solid black">
                            <div class="category">Настройки</div>
                            <div class="settings custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="case-sensitive">
                                <label class="custom-control-label" for="case-sensitive">Има разлика между главни и малки букви (Case-Sensitive)</label>
                            </div>
                            <div class="settings">
                                <label>Максимален брой символи:</label>
                                <input type="number" id="character-limit" value="0" min="0" oninput="validity.valid||(value='0');" class="number-sm">
                                <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                                title="Оставете 0, ако не желеате лимит на символите">
                                ?
                                </button>
                            </div>
                            <div class="settings">
                                <label>Брой редове при принтиране:</label>
                                <input type="number" id="print-lines" value="3" min="1" oninput="validity.valid||(value='0');" class="number-sm">
                            </div>
                        </div>
                        <div id="answers"></div>
                        <div id="buttons" class="d-flex justify-content-between">
                            <button  type="button" id="answerButton" class="btn btn-primary" onclick="AddAnswer(selectedType)" style="display:none">
                                <span>Добави Отговор</span>
                            </button>

                            <button id="settingsbtn" class="btn btn-primary" type="button" data-toggle="collapse" data-target="#question-settings" aria-expanded="false" aria-controls="settings">
                                Допълнителни настройки
                            </button>
                        </div>
                        <div class="collapse" id="question-settings">
                            <div class="card card-body">
                                <div>
                                    Брой точки:<input type="number" id="points" value="1" min="0" class="number-sm"
                                        oninput="validity.valid||(value='0');">
                                </div>
                                <div>
                                    Време за отговор(в секунди)
                                    <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                                     title="Времето за отгoвор се прилага единствено при организирането на състезание">
                                        ?
                                    </button>
                                    :
                                    <input type="number" id="question_time" value="30" min="10" class="number-sm"
                                        oninput="validity.valid||(value='0');">
                                </div>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="mandatory" checked>
                                    <label class="custom-control-label" for="mandatory">Задължителен въпрос</label>
                                </div>
                                <div style="margin-top:6px;">
                                    <b>Обяснение</b> <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                                            title="Обяснението ще се покаже, когато попълващият вече е предал своя тест и преглежда отговорите си">
                                            ?
                                    </button>
                                    <div style="margin-top:6px;"><input type="text" id="question_info" placeholder="Обяснение" style="width:80%"/></div>
                                </div>
                                <div style="margin-top:6px;">
                                    <b>Категория</b> <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                                            title="Подреждането на въпроси в категории позволява лесно структуриране на теста">
                                            ?
                                    </button>
                                    <div style="margin-top:6px;"><input type="text" id="question_category" placeholder="Категория" style="width:80%"/></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" id='cancel' class="btn btn-secondary" data-dismiss="modal">Отказ</button>
                    <button type="button" id="import-btn" class="btn btn-primary">Приложи</button>
                </div>
            </div>
        </div>
    </div>

     <!-- Settings Modal-->
    <div class="modal fade" id="SettingsModal" tabindex="-1" role="dialog" aria-labelledby="SettingsModal"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Настройки</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div>
                        <div class="category">Основни Настройки</div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="unlocked" checked>
                            <label class="custom-control-label" for="unlocked">Отключен(Отворен за попълване)</label>
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                            title="Когато тестът е затворен, той няма да може да бъде отварян дори при правилно въведен PIN код">
                            ?
                            </button>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="public">
                            <label class="custom-control-label" for="public">Публичен</label>
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                            title="Тестът ви ще бъде достъпен за преглеждане от всички потребители на сайта, дори да нямат необходимия PIN код">
                            ?
                            </button>
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                            title="Не включвайте тази настройка, ако ще използвате теста за изпитване!">
                            !
                            </button>
                        </div>
                        <div class="settings">
                            Формула за оценяване:(R-получени точки, М-всички точки)
                            <select id="grading">
                                <option value="f1"
                                    <?php if(isset($_GET['id'])){if(json_decode($result['settings'])->grading=='f1'){echo 'selected';}}else{echo 'selected';}?>>
                                    =2 +( 4 х R:М)</option>
                                <option value="f2"
                                    <?php if(isset($_GET['id'])){if(json_decode($result['settings'])->grading=='f2'){echo 'selected';}}?>>
                                    =(R x 6):M</option>
                            </select>
                        </div>
                        <div class="category">Настройки при решаване</div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="one_per_one">
                            <label class="custom-control-label" for="one_per_one">Показване по един въпрос на страница</label>
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="right"
                            title="Попълващия ще може да продължи към следващия въпрос, само когато е попълнил текущия. При вече изпратен отговор на въпрос, попълващия не може да го поправи">
                            ?
                            </button>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="randomize_questions" checked>
                            <label class="custom-control-label" for="randomize_questions">Разбъркване на реда на въпросите</label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="randomize_answers" checked>
                            <label class="custom-control-label" for="randomize_answers">Разбъркване на реда на отговорите</label>
                        </div>
                        <div class="settings">
                            <label>Извадка от въпроси:</label>
                            <input type="number" id="question_limit" value="0" min="0" oninput="validity.valid||(value='0');" class="number-sm">
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="right"
                            title="Всеки попълващ ще получи случайна извадка, съставена от определен брой въпроси. Оставете 0 ако желаете да използвате всички въпроси">
                            ?
                            </button>
                        </div>
                        <div class="settings">
                            <label>Лимит на време(в минути):</label>
                            <input type="number" id="time_limit" value="0" min="0" oninput="validity.valid||(value='0');" class="number-sm">
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="right"
                            title="Оставете 0, ако не желате да има лимит на време">
                            ?
                            </button>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="individual_time">
                            <label class="custom-control-label" for="individual_time">Индивидуално време за въпроси</label>
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="right"
                            title="При включено 'въпрос на страница', използвай индивидуалното време за отговор на въпрос">
                            ?
                            </button>
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                            title="Тази настройка не е налична в момента">
                            !
                            </button>
                        </div>
                        <div class="category">Ограничения и права</div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="require_profile">
                            <label class="custom-control-label" for="require_profile">Изисква се влизане в профил    
                            </label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="allow_anonymous">
                            <label class="custom-control-label" for="allow_anonymous">Разрешава се анонимно попълване
                            </label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="limit_response" checked>
                            <label class="custom-control-label" for="limit_response">Всеки потребител може да изпрати само по един тест 
                            </label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="check_points" checked>
                            <label class="custom-control-label" for="check_points">При изпращане попълващия може да провери своята оценка</label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="check_answers" checked>
                            <label class="custom-control-label" for="check_answers">При изпращане попълващия може да провери правилните отговори</label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="limit_check" checked>
                            <label class="custom-control-label" for="limit_check">При изпращане попълващия може да провери правилните отговори само веднъж
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="top"
                            title="Тази настройка намалява шансовете учениците да споделят отговорите помежду си">
                            ?
                            </button>
                            </label>
                        </div>
                        <div class="settings">
                            <label>Тестът може да се отвори от екип:</label>
                            <input type="text" id="team_limit">
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="right"
                            title="Оставете празно, ако не желате да има лимит на екип">
                            ?
                            </button>
                        </div>
                    </div>
                    <div class="category">Бележки</div>
                    <div style="color:gray; text-align: center;">Бележките предоставят лесен начин за групиране, филтриране и търсене на тестове.<br>Пример: "Биология", "Класно"</div>
                    <div class="tag-container">
                        <input/>  
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="StructureModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="StructureModal" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="StructureModalLabel">Структура</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                <div class="alert alert-secondary" role="alert">
                    Използвайте менюто Структура, за да създадете тест според вашите нужди!
                </div>
                <div>
                    <ol id="structure-list">
                        <li>Желая да използвам <a href="#">Въпрос</a> / <a href="#">Категория</a></li>
                    </ol>
                </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="QuestionImport" tabindex="-1" role="dialog" aria-labelledby="QuestionImport" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Импортиране на въпрос</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="import-nav">
                    
                    
                </div>
                <div id="test-choose">
                    <b>Изберете тест, чиито въпроси искате да използвате:</b>
                    <div id="test-list" class="btn-group-toggle" data-toggle="buttons"></div>
                </div>
                <div id="question-choose">
                    <b>Изберете въпросите, които искате да използвате:</b>
                    <div id="question-list" class="btn-group-toggle" data-toggle="buttons"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id="btn-import" onclick="LoadTestList()">Продължи</button>
            </div>
            </div>
        </div>
    </div>

    <div aria-live="polite" class="toast-container">
        <div class="toast hide" autohide="true" data-delay=10000>
            <div class="toast-header">
                <img src="images/favicon-16x16.png" class="rounded mr-2" alt="PassorNot">
                <strong class="mr-auto">Pass or not</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">

            </div>
        </div>
    </div>

    <div class="sidenav">
        <span data-toggle="modal" data-target="#SettingsModal">
            <button type="button" class="btn btn-lg btn-light btn-sidenav" style="margin:3px" data-toggle="tooltip" data-placement="bottom" title="Настройки">
                <img src="images/settings.svg">
            </button>
        </span>
        <span data-toggle="modal" data-target="#StructureModal">
            <button type="button" class="btn btn-lg btn-success btn-sidenav" style="margin:3px" data-toggle="tooltip"
                data-placement="bottom" title="Структура">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z"/>
            </svg>
            </button>
        </span>

        <button type="button" class="btn btn-lg btn-warning btn-sidenav" onclick="Clear()" style="margin:3px" data-toggle="tooltip"
            data-placement="bottom" title="Изчисти съдържание"><img src="images/delete-black.svg"></button>
        <button type="button" class="btn btn-lg btn-primary btn-sidenav" onclick="Save()" style="margin:3px" data-toggle="tooltip"
            data-placement="bottom" title="Запази"><img src="images/save.svg"></button>
        <button type="button" class="btn btn-lg btn-light btn-sidenav" onclick="Save(); window.location.href='/profile.php?v=created-tests'"
            style="margin:3px" data-toggle="tooltip" data-placement="bottom"
            title="Запази и се върни към профилна страница"><img style="width:20px;"
                    src="images/person-black.svg">
        </button>
    </div>
    <!-- JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="/js/test_editor_scripts.js"></script>
    <script src="/js/tags_scripts.js"></script>
</body>
</html>
<?php include($_SERVER['DOCUMENT_ROOT'].'\actions\db_close.php');?>