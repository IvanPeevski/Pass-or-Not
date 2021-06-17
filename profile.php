<?php
    require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
    if (!isset($_SESSION['username'])) {
        header("location: message.php?type=auth&path=profile");
    }
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo $_SESSION['username'] ?> | Профил</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/button_styles.css" rel="stylesheet">
    <link href="/css/dataTables_styles.css" rel="stylesheet">
    <link href="/css/settings_styles.css" rel="stylesheet">
    <link href="/css/profile_styles.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/site.webmanifest">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">

    </script>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 mr-0 px-3" href="/">
        <img src="/images/icon.png" width="25px" height="25px"> Pass or Not</a>
        <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <form class="mx-2 my-auto d-inline w-100">
            <div class="input-group">
                <input type="text" class="form-control form-control-dark border-right-0" id="nav-search" placeholder="Търсене" aria-label="Търсене">
                <span class="input-group-append">
                    <button class="btn btn-dark btn-search border-left-0" type="button" onclick="displaySearchView(document.getElementById('nav-search').value)">
                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
                    <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                    </svg>
                    </button>
                </span>
            </div>
        </form>
        <ul class="navbar-nav px-3">
            <li class="nav-item text-nowrap">
            <a class="nav-link" href="/actions/user/logout.php">Изход</a>
            </li>
        </ul>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="sidebar-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" id="overview-link" href="#" onclick="displayTab(this)">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-house" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2 13.5V7h1v6.5a.5.5 0 0 0 .5.5h9a.5.5 0 0 0 .5-.5V7h1v6.5a1.5 1.5 0 0 1-1.5 1.5h-9A1.5 1.5 0 0 1 2 13.5zm11-11V6l-2-2V2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5z"/>
                            <path fill-rule="evenodd" d="M7.293 1.5a1 1 0 0 1 1.414 0l6.647 6.646a.5.5 0 0 1-.708.708L8 2.207 1.354 8.854a.5.5 0 1 1-.708-.708L7.293 1.5z"/>
                            </svg>
                            Начало
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="created-tests-link" href="#" onclick="displayTab(this)">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5L13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175l-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325z"/>
                            </svg>
                            Създадени Тестове
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" id="responses-link" onclick="displayTab(this)">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-list" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M2.5 11.5A.5.5 0 0 1 3 11h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 7h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4A.5.5 0 0 1 3 3h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                            </svg>
                            Изпратени Отговори
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="search-link" href="#" onclick="displayTab(this)">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
                            <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                            </svg>
                            Търсене
                            </a>
                        </li>
                    </ul>

                    <h6 class="sidebar-heading flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
                    <span>Вашите екипи</span>
                    <a class="flex align-items-center text-muted" id="create-team-link" href="#" aria-label="Create new team" onclick="displayTab(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus-circle"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                    </a>
                    </h6>
                    <ul class="nav flex-column mb-2" id="teams-list">
                    </ul>
                    <div style="position: absolute; bottom: 0px; height:45px;width:100%">
                        <button onclick="window.location.href = '/create.php'" class="btn btn-block btn-light" style="text-align:left;">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-plus" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                            </svg>
                            Създаване на Тест
                        </button>
                    </div>
                </div>
            </nav>
            <main class="col-md-9 ml-sm-auto col-lg-10 px-md-4" role="main">
                <div class="overview screen flex flex-column align-items-left" style="display:none">
                    <div class="heading" style="margin-bottom:15px;padding-top:20px"><h1>Добре дошли, <?php echo $_SESSION['fullName']?>!</h1></div>
                    <div id="student-view" style="display:none;" >
                        <div class="flex justify-content-around align-items-center" style="text-align:center; min-height:400px">
                            <div style="font-size:23px;">
                                <span style="font-size:29px;font-weight:bold">ИНФОРМАЦИЯ</span>
                                <p>
                                    Изпратени <b>4</b> теста<br>
                                    <b style="color:orange">0</b> от тях са недооценени<br>
                                </p>
                                <span style="font-size:29px;font-weight:bold">УСПЕХ</span>
                                <p>
                                    Успехът ти е <b>Мн. добър(5.34)</b>
                                </p>
                                <div>
                                    <b>Последно изпратен тест:</b>
                                    <div>
                                        Класно по ИТ 12 клас 
                                        <a class="btn btn-sm btn-light" href="#"><img src="/images/visit.svg"/> Прегледай</a>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div id="piechart"></div>
                            </div>
                        </div>
                        <div class="join-form heading" style="min-width:80%">
                            <h2 class="heading">Попълване на тестове</h2>
                            <div class="input-group input-group-lg">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" id="input-pin">PIN</span>
                                </div>
                                <input type="text" id="pin" maxlength="8" class="form-control" aria-label="PIN"
                                    aria-describedby="input-pin">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-dark btn-lg">Отвори тест</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="teacher-view">
                        <div class="flex justify-content-around align-items-center flex-wrap" style="min-height:385px">
                            <div style="text-align:center;font-size:23px">
                                <span style="font-size:29px;font-weight:bold">ИНФОРМАЦИЯ</span>
                                <p>
                                    Имате <b id="tests-count">-</b> създадени теста<br>
                                </p>
                                <span style="font-size:29px;font-weight:bold">РЕЗУЛТАТИ</span>
                                <p>
                                    <b id="total-responses">-</b> изпратени резултата<br>
                                    <b id="undecided-count" style="color:orange">-</b> от тях са недооценени<br>
                                    <b id="active-count" style="color:green">-</b> попълващи в момента
                                </p>
                            </div>
                            <div>
                                <div style="font-size:29px;font-weight:530; text-align:center">Последно Активни Тестове</div>
                                <table class="table" id="active-tests-table">
                                    <thead class="thead-dark">
                                        <tr>
                                        <th scope="col">Тест</th>
                                        <th scope="col">Последно Изпратен Резултат</th>
                                        <th scope="col">Преглед</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="heading"><h2>Недооценени Резултати</h2></div>
                        <table class="table table-hover table-bordered" id="undecided-responses">
                            <thead>
                                <tr>
                                <th scope="col">Ученик</th>
                                <th scope="col">Клас</th>
                                <th scope="col">Тест</th>
                                <th scope="col">Оцени</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="created-tests screen" style="display:none">
                    <div class="heading" style="margin-bottom:20px;padding-top:20px; width:100%">
                        <button onclick="window.location.href='/create.php'" class="btn btn-success btn-lg btn-block" style="margin-bottom:15px;">Създай нов тест+</button>
                        <h1>Създадени Тестове</h1>
                    </div>
                    <div id="tests">
                            <div class="table-responsive">
                                <table  id="test-table" class="table table-hover table-striped table-bordered" style="width:100%">
                                    <thead class="">
                                        <tr>
                                            <th scope="col">PIN</th>
                                            <th scope="col">Заглавие</th>
                                            <th scope="col">Бележки</th>
                                            <th scope="col">Отключен</th>
                                            <th scope="col">Въпроси</th>
                                            <th scope="col">Точки</th>
                                            <th scope="col">Функции</th>
                                            <th scope="col">Променен</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                </div>
                <div class="responses screen" style="display:none">
                    <h1 style="margin-top:25px;" class="heading">Успех</h1>
                    <div class="flex justify-content-around flex-wrap" style="text-align:center">
                        <div><h4>Медиана на оценка<br><b id="median"></b></h4></div>
                        <div><h2><b>СРЕДЕН УСПЕХ<br><span id="mean"></span></b></h2></div>
                        <div><h4>Мода на оценка<br><b id="mode"></b></h4></div>
                    </div>
                    <div id="curved_chart_stu"></div>
                    <h1 style="margin-top:15px;" class="heading">Изпратени отговори</h1>
                    <table class="table table-hover" id="profile-responses-table">
                        <thead>
                            <th>Тест PIN</th>
                            <th>Тест</th>
                            <th>Оценка</th>
                            <th>Точки</th>
                            <th>Изпратен</th>
                            <th>Преглед</th>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div class="search screen" style="display:none">
                    <div class="heading" style="margin-top:75px;text-align:center">
                        <h1>Търсене на тестове</h1>
                        <span style="color:gray; font-size:24px;">Надградете знанията си с тестове, създадени от други потребители на сайта</span>
                    </div>
                    <div style="margin-top:15px;">
                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="inputGroup-sizing-lg">
                                    <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
                                    <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                                    </svg>
                                </span>
                            </div>
                            <input type="text" class="form-control" id="search-field" aria-label="Large" aria-describedby="inputGroup-sizing-sm">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="displaySearchView(document.getElementById('search-field').value)">Търсене</button>
                            </div>
                        </div>
                    </div>
                    <div style="margin-top:15px;" id="search-results">
                        
                    </div>
                </div>
                <div class="create-team screen" style="display:none">
                    <h1 style="margin-top:25px;" class="heading">Създаване на екип</h1>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="create-team-field" placeholder="Въведете име на екип" aria-label="Create Team">
                        <div class="input-group-append">
                            <button class="btn btn-lg btn-success" type="button" onclick="CreateTeam()"><img src="/images/check.svg" width="30"></button>
                        </div>
                    </div>
                </div>
                <div class="view-team screen" style="display:none">
                    <h1 style="margin-top:25px;" class="heading" id="team-name"></h1>
                    <h4 style="color:gray; font-weight:bold">Създаден от <span id="team-creator"></span></h4>
                    <h2 class="heading">Участници:</h2>
                    <div id="team-members" style="margin-bottom:35px;"></div>
                    <h2 class="heading">Добави участник:</h2>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="add-member-field" placeholder="Въведете потребителксо име на участник" aria-label="Create Team">
                        <div class="input-group-append">
                            <button class="btn btn-lg btn-success" type="button" onclick="AddMember()"><img src="/images/check.svg" width="30"></button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="SettingsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Настройки
                    <button type="button" class="btn btn-sm btn-outline-secondary refresh-settings" onclick="GetSettings(this.id)">
                        <img src="/images/refresh.svg"/>
                    </button>
                    </h5>
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
                            <input type="checkbox" class="custom-control-input" id="randomize_questions">
                            <label class="custom-control-label" for="randomize_questions">Разбъркване на реда на въпросите</label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="randomize_answers">
                            <label class="custom-control-label" for="randomize_answers">Разбъркване на реда на отговорите</label>
                        </div>
                        <div class="settings">
                            <label>Извадка от въпроси:</label>
                            <input type="number" id="question_limit" value="0" min="0" oninput="validity.valid||(value='0');">
                            <button type="button" class="btn btn-secondary btn-tooltip" data-toggle="tooltip" data-placement="right"
                            title="Всеки попълващ ще получи случайна извадка, съставена от определен брой въпроси. Оставете 0 ако желаете да използвате всички въпроси">
                            ?
                            </button>
                        </div>
                        <div class="settings">
                            <label>Лимит на време(в минути):</label>
                            <input type="number" id="time_limit" value="0" min="0" oninput="validity.valid||(value='0');">
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
                            <input type="checkbox" class="custom-control-input" id="limit_response">
                            <label class="custom-control-label" for="limit_response">Всеки потребител може да изпрати само по един тест 
                            </label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="check_points">
                            <label class="custom-control-label" for="check_points">При изпращане попълващия може да провери своята оценка</label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="check_answers">
                            <label class="custom-control-label" for="check_answers">При изпращане попълващия може да провери правилните отговори</label>
                        </div>
                        <div class="settings custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="limit_check">
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
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Затвори</button>
                    <button type="button" class="btn btn-primary save-settings" onclick="SetSettings(this.id)">Запази
                        промените</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="responseModal" tabindex="-1" role="dialog"
        aria-labelledby="responseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="responseModalLabel">Изпратени резултати
                    <button type="button" class="btn btn-sm btn-outline-secondary reload" onclick="GetResponses(this.id)">
                        <img src="/images/refresh.svg"/>
                    </button>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table id="responseTable" class="table table-striped" style="width:100%">
                        <thead>
                            <tr>
                                <th scope="col">Име</th>
                                <th scope="col">Клас</th>
                                <th scope="col">Оценка</th>
                                <th scope="col">Успех</th>
                                <th scope="col">Точки</th>
                                <th scope="col">Изпратен</th>
                                <th scope="col">Състояние</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody id="responseTableContent">
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer" style="text-align: center; padding-top:0;">
                    <div style="background-color:#4aa1ff; color:white; width:100%; margin:auto;">
                        <h3> СРЕДЕН УСПЕХ: <span id="response-average"></span><h3>
                    <div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>  
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/plug-ins/1.10.20/sorting/datetime-moment.js"></script>    
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script> 
    <script src="/js/profile_scripts.js"></script>
    <script src="/js/tags_scripts.js"></script> 
</body>
</html>
<?php include($_SERVER['DOCUMENT_ROOT'].'\actions\db_close.php');?>