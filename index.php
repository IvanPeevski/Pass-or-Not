<?php
require($_SERVER['DOCUMENT_ROOT'].'/actions/db_connect.php');
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
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/site.webmanifest">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#00aba9">
    <meta name="theme-color" content="#ffffff">
</head>

<body id="page-top">

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" style="opacity:95%" id="mainNav">
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
                <ul class="navbar-nav ml-auto justify-content-center">
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#about">????????????????????</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link js-scroll-trigger" href="#test">?????????????????? ???? ??????????????</a>
                    </li>
                </ul>
                <ul class="navbar-nav ml-auto justify-content-end">
                    <?php if(isset($_SESSION['username'])){
                    ?>
                    <li class="nav-item">
                        <div class="btn btn-dark"><a class="nav-link" href="/profile.php"
                                style="padding:0"><?php echo $_SESSION['username'] ?><img src="/images/person-white.svg"
                                    width="33" height="33"></a></div>
                    </li>
                    <li class="nav-item">
                        <div class="btn btn-dark"><a class="nav-link" href="/actions/user/logout.php" style="padding:0">??????????<img
                                    src="/images/exit-white.svg" width="33" height="33"></a></div>
                    </li>
                    <?php } else {
                    ?>
                    <li class="nav-item">
                        <button type="button" class="btn btn-dark" data-toggle="modal"
                            data-target="#loginModal">????????</button>
                    <li class="nav-item">
                        <button type="button" class="btn btn-dark" data-toggle="modal"
                            data-target="#registerModal">??????????????????????</button>
                    </li>
                    </li>
                    <?php
          } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div id="header" class="white-gradient margin-top">
        <div class="text-center">
            <img src="/images/pass_or_not_logo.svg" width="100%" style="max-width: 450px; min-width: 330px; padding: 30px;"/>
        </div>
    </div>

    <section id="about" class="green-gradient">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2>???? ???????? ????????????????</h2>
                    <p class="lead">?????????????????????? ?? ???????????????????? ???? ?????????????? ???????????? ???? ?? ???????? ????-????????o.
                        Pass or Not ?? ???????? ???????????????? ???? ?????????? ??? ???? ???????? ?? ???????????????? ?????????????? ???? ?????????????????????????? ??????????????. ??
                        ?????????????????? ???? ???????????? ???? ???????? ???????????? ???? ??????????????, ?????????????????? ???? ???????? ???? ?????????? ????????, ?? ????????????????????????
                        ???? ????
                        ???????????????? ???????????????? ???????? ???? ??????????????, ???????????????? ?? ????????????. ????????????????, ?????????????? ?? ???? ???????????????????? ?? Pass
                        or Not.</p>
                    <div>
                        <button type="button" class="btn btn-dark" data-toggle="modal"
                            data-target="#registerModal">?????????????????????? ????</button>
                    </div>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section id="test" class="red-gradient">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2>?????????????????? ???? ??????????????</h2>
                    <p class="lead">?????????? ???????? ?? ?????????????????????? ?? ???????????????? 8-???????????? ??????. ?????????????????? ?????????? ???? ?????????? ????????????????
                        ?? ???? ??????????????????????, ?? ???? ?????????? ?????? ????????????.</p>
                    <div>

                        <div class="input-group input-group-lg">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="input-pin">PIN</span>
                            </div>
                            <input type="text" id="pin" maxlength="8" class="form-control" aria-label="PIN"
                                aria-describedby="input-pin">
                        </div>
                        <div>
                        <div class="form-row button-wrap">
                            <input class="hidden radio-label stu-button" type="radio" name="type" id="stu" value="student" checked="checked"/>
                            <label class="button-label" for="stu">
                                <h1>????????????</h1>
                            </label>
                            <input class="hidden radio-label" type="radio" name="type" id="guest-button" value="guest" />
                            <label class="button-label" for="guest-button">
                                <h1>????????</h1>
                            </label>
                        </div>
                        <div class="form-row" id="fields">
                            <div class="form-group col-md-6">
                            <input type="text"placeholder="??????" class="form-control" id="stuName" value="<?php echo isset($_SESSION['fullName'])?$_SESSION['fullName']:''?>">
                            </div>
                            <div class="form-group col-md-4">
                            <input type="number" placeholder="????????" class="form-control" id="class" min="1" max="12" <?php if(isset($_SESSION['class'])){echo 'value="'.$_SESSION['class'].'"';}?>>
                            </div>
                            <div class="form-group col-md-2">
                            <select id="division" class="form-control">
                                <option <?php if(isset($_SESSION['division']) && $_SESSION['division']=='??'){echo 'selected';}?>>??</option>
                                <option <?php if(isset($_SESSION['division']) && $_SESSION['division']=='??'){echo 'selected';}?>>??</option>
                                <option <?php if(isset($_SESSION['division']) && $_SESSION['division']=='??'){echo 'selected';}?>>??</option>
                                <option <?php if(isset($_SESSION['division']) && $_SESSION['division']=='??'){echo 'selected';}?>>??</option>
                            </select>
                            </div>
                        </div>
                        </div>
                            <button class="btn btn-secondary" type="button"
                                onclick="window.location.href = `/quiz.php?testId=${document.getElementById('pin').value}&name=${document.getElementById('stuName').value}&class=${document.getElementById('class').value}&division=${document.getElementById('division').value}&guest=${document.getElementsByName('type')[1].checked}`">????????????</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    
    <footer class="bg-dark text-light justify-content-around align-items-center flex-wrap-reverse" 
    style="min-height:230px;font-size:25px;flex: 1;display:flex; border-right: 1px solid #fff;text-align:center;padding:30px 10px;">
            <div>
                ???????????? ??????????????:
                <div class="bg-light" style="border-radius:0.3rem; border:1px solid #ced4da; padding:4px 30px;margin-top:9px;">
                    <a href="https://vlevski.eu/"><img src="/images/vlevski_header.png" width="120px"/> ???? "?????????? ????????????" - ??????????</a>
                </div>
            </div>
            <div>
                ???????????????? ???? ?? ?????? - passornot.vlevski@gmail.com
                <form>
                    <input class="form-control form-control" style="margin:10px 0px;" type="text" name="contact_email" placeholder="?????????? ??????????">
                    <input class="form-control form-control" style="margin-bottom:5px;" type="text" name="contact_header" placeholder="????????????????">
                    <textarea rows="3" placeholder="??????????????????" style="width:100%;margin-bottom:5px;" name="contact_text"></textarea>
                    <button type="submit" name="contact_submit" class="btn btn-light btn-lg btn-block">??????????????</button>
                </form>
            </div>
    </footer>

    <div class="modal fade" id="lobbyModal" tabindex="-1" role="dialog" aria-labelledby="lobbyModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lobbyModalLabel">???????????????????? ????</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="input-effect">
                    <input type="text" class="form-field" id="join_lobby_pin" placeholder="PIN" />
                    <span class="focus-border">
                        <i></i>
                    </span>
                </div>
                <div class="input-effect">
                    <input type="text" class="form-field" id="join_lobby_name" placeholder="?????????????????????????? ??????" value="<?php echo $_SESSION['username']?>"/>
                    <span class="focus-border">
                        <i></i>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary">????????????</button>
            </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="createModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModalLabel">???????????? ????????</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?php 
            if(!isset($_SESSION['username'])){ echo '<b>???????? ???????????? ?????? ?????????? ????????????, ???? ???? ?????????????????? ????????</b>'; }
            else{
                ?>
                <b>???????????????? ????????:</b>
                <div id="select">
                    <?php
                    $username = $_SESSION['username'];
                    $query = "SELECT test.id, test.pin, test.name, test.content, test.settings, test.createdOn FROM test INNER JOIN user ON test.UserId=user.id WHERE username='$username' ORDER BY test.createdOn DESC";
                    $result = mysqli_query($db, $query);
                    while ($test = mysqli_fetch_assoc($result)) {
                        ?>
                        <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="<?php echo $test['pin']?>" name="test_select">
                        <label class="custom-control-label" for="<?php echo $test['pin']?>"><?php echo $test['name']?></label>
                        </div>    
                        <?php
                    }?>
                </divz>
            <?php }?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="ContestStart()">???????????????????</button>
            </div>
            </div>
        </div>
    </div>

    <?php if(!isset($_SESSION['logged'])){
        ?>
        <!-- Login Modal -->
        <div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="loginModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="loginModalCenterTitle">????????</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="input-effect">
                                <input type="text" class="form-field" id="log_username" placeholder="?????????????????????????? ??????" autocomplete="username" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="password" class="form-field" id="log_password" autocomplete="current-password" placeholder="????????????" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <span id="log_errorBox" style="color:red"></span>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="SendInfo('login')" class="btn btn-primary" name="login_user" >????????</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Register Modal -->
        <div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="registerModalCenterTitle"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="registerModalCenterTitle">??????????????????????</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <div class="input-effect">
                                        <input type="text" class="form-field" id="firstName" placeholder="??????" required />
                                        <span class="focus-border">
                                            <i></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-6">
                                    <div class="input-effect">
                                        <input type="text" class="form-field" id="Surname" placeholder="??????????????" required />
                                        <span class="focus-border">
                                            <i></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="input-effect">
                                <input type="text" class="form-field" id="reg_username" autocomplete="new-username" placeholder="?????????????????????????? ??????"
                                    required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="text" class="form-field" id="reg_email" placeholder="??????????" autocomplete="email" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="password" class="form-field" id="password_1" autocomplete="new-password" placeholder="????????????" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="input-effect">
                                <input type="password" class="form-field" id="password_2" autocomplete="new-password" placeholder="???????????????????? ????????????" required />
                                <span class="focus-border">
                                    <i></i>
                                </span>
                            </div>
                            <div class="button-wrap" id="selected-role">
                                <input class="hidden radio-label" type="radio" name="role" id="teacher-button" value="teacher" checked="checked" />
                                <label class="button-label" for="teacher-button">
                                    <h1>????????????</h1>
                                </label>
                                <input class="hidden radio-label stu-button" type="radio" name="role" id="stu-button" value="student"/>
                                <label class="button-label" for="stu-button">
                                    <h1>????????????</h1>
                                </label>
                            </div>
                            <div id="optional" class="form-row">
                                <div class="form-group col-md-8">
                                    <div class="input-effect">
                                        <input type="text" class="form-field" id="reg_class" min="1" max="12" placeholder="????????">
                                        <span class="focus-border">
                                            <i></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group col-md-4">
                                    <div class="input-effect">
                                        <select id="reg_division" class="form-field">
                                            <option selected>??</option>
                                            <option>??</option>
                                            <option>??</option>
                                            <option>??</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <span id="reg_errorBox" style="color:red"></span>
                        </form>    
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="SendInfo('register')" class="btn btn-primary">?????????????????????? ????</button>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

    
        <!-- Bootstrap --->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="/js/landing_page_script.js"></script>
    <script src="/js/login_register_script.js"></script>
</body>
</html>
<?php include($_SERVER['DOCUMENT_ROOT'].'\actions\db_close.php');?>