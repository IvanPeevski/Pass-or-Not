
<?php
    $type=$_GET['type'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Pass or Not</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link href="/css/main.css" rel="stylesheet">
    <link href="/css/login_register_styles.css" rel="stylesheet">
    <link href="/css/message_styles.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/site.webmanifest">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
    <script>
        const urlParams = new URLSearchParams(window.location.search)
    </script>
</head>

<body style="background-color: #f2f2f2;min-height: 100vh;" class="d-flex justify-content-center align-items-center">
    <div class="box" >
        <div style="margin-top:30px;">
            <img src="images/icon.png" style="max-width: 250px;" />
        </div>
        <?php 
            if($type=="auth"){
                ?>
                    <div id="message">
                        Моля влезте във вашия профил, за да достъпите тази страница
                    </div>
                    <div id="profile_fields" class="fields">
                        <div class="d-flex justifiy-content-center flex-wrap">
                            <a href="javascript:void(0)" class="tab" onclick="openForm('login')">Вход</a>
                            <a href="javascript:void(0)" class="tab" onclick="openForm('register')">Регистрация</a>
                        </div>
                        <form id="register" style="display:none;">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-effect">
                                        <input type="text" class="form-field" id="firstName" placeholder="Име" required />
                                        <span class="focus-border">
                                            <i></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-effect">
                                        <input type="text" class="form-field" id="Surname" placeholder="Фамилия" required />
                                        <span class="focus-border">
                                            <i></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-effect">
                                <input type="text" class="form-field" id="reg_username" autocomplete="new-username" placeholder="Потребителско име"
                                    required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="text" class="form-field" id="reg_email" placeholder="Имейл" autocomplete="email" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="password" class="form-field" id="password_1" autocomplete="new-password" placeholder="Парола" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="password" class="form-field" id="password_2" autocomplete="new-password" placeholder="Потвърдете Парола" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="button-wrap" id="selected-role">
                                <input class="hidden radio-label" type="radio" name="role" id="teacher-button" value="teacher" checked="checked" />
                                <label class="button-label" for="teacher-button">
                                    <h1>Учител</h1>
                                </label>
                                <input class="hidden radio-label stu-button" type="radio" name="role" id="stu-button" value="student"/>
                                <label class="button-label" for="stu-button">
                                    <h1>Ученик</h1>
                                </label>
                            </div>
                            <div id="optional" class="form-row">
                                <div class="form-group col-md-8">
                                    <div class="input-effect">
                                        <input type="text" class="form-field" id="reg_class" min="1" max="12" placeholder="Клас">
                                        <span class="focus-border">
                                            <i></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="input-effect">
                                        <select id="reg_division" class="form-field">
                                            <option selected>А</option>
                                            <option>Б</option>
                                            <option>В</option>
                                            <option>Г</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <span id="reg_errorBox" style="color:red"></span>
                            <button type="button"   onclick="SendInfo('register')" id="submit" class="btn btn-secondary btn-lg btn-block">Регистрирай се</button>
                        </form>  
                        <form id="login">
                            <div class="input-effect">
                                <input type="text" class="form-field" id="log_username" placeholder="Потребителско име" autocomplete="username" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="password" class="form-field" id="log_password" autocomplete="current-password" placeholder="Парола" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <span id="log_errorBox" style="color:red"></span>
                            <button type="button"   onclick="SendInfo('login')" id="submit" class="btn btn-secondary btn-lg btn-block">Вход</button>
                        </form>
                    </div>
                <?php } 
            else if($type=="inv_data" || $type=="not_found" || $type=="no_annonymous"){
                if($type=="inv_data"){
                    ?>
                    <div id="message">
                        Моля въведете всички необходими данни
                    </div>
                    <?php
                }
                else if($type=="not_found"){
                    ?>
                    <div id="message">
                        Тестът, който търсите, не съществува или не е достъпен. Моля опитайте отново
                    </div>
                    <?php
                }
                else if($type=="no_annonymous"){
                    ?>
                    <div id="message">
                        Този тест не е достъпен за анонимно попълване. Моля въведете всички данни
                    </div>
                    <?php
                }
                ?>
                <div class="fields">
                    <div class="input-group input-group-lg">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="input-pin">PIN</span>
                        </div>
                        <input type="text" id="pin" maxlength="8" class="form-control" aria-label="PIN" value="<?php echo $_GET['testId'] ?>" aria-describedby="input-pin">
                    </div>
                    <div>
                        <div class="form-row" style="margin-top:8px;">
                            <div class="form-group col-md-6">
                                <input type="text" placeholder="Име" class="form-control" id="stuName" value="<?php echo $_GET['name'] ?>">
                            </div>
                            <div class="form-group col-md-4">
                                <input type="number" placeholder="Клас" class="form-control" id="class" min="1" max="12" value="<?php echo $_GET['class'] ?>">
                            </div>
                            <div class="form-group col-md-2">
                                <select id="division" class="form-control">
                                    <option <?php if($_GET['division']=='А'){echo 'selected';}?>>А</option>
                                    <option <?php if($_GET['division']=='Б'){echo 'selected';}?>>Б</option>
                                    <option <?php if($_GET['division']=='В'){echo 'selected';}?>>В</option>
                                    <option <?php if($_GET['division']=='Г'){echo 'selected';}?>>Г</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-secondary btn-lg btn-block" type="button" 
                    onclick="window.location.href = `quiz.php?testId=${document.getElementById('pin').value}&name=${document.getElementById('stuName').value}&class=${document.getElementById('class').value}&division=${document.getElementById('division').value}`">Готово</button>
                </div>
                <?php }
        ?>
    </div>
    <!--JavaScript-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="/js/login_register_script.js"></script>
</body>
</html>