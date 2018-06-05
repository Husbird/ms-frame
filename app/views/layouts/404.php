<?php
header("HTTP/1.0 404 Not Found");
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?php include_once "inc_block/head.php";?>
</head>

<body id="body_ms">

<?php
//скрипты информеров счётчиков посещений
//include_once("inc_block/counter_code.php");
?>

<!---------------------------------------------------------------------- header ---------------------------------------------------------------->
<header id="header">
    <span id="headerTitle"><?php echo Core::app()->config->app_name;?></span>
    <!--<img src="/assets/media/images/main/header.jpg" class="img-responsive" style="width:100%;"/>-->
</header>
<!-- .-------------------------------------------------------------------- header END ---------------------------------------------------------->


<!------------------------------------------------------------------------- wrapper ---------------------------------------------------->
<div class="wrapper">





    <!-------------------------------------------------------------- Основной контент страницы ---------------------------------------------------->
    <main>
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-2 col-md-2 sm-hidden xs-hidden" style=""><!-- левый отступ -->
                </div>

                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" id="mainContentMS">
                    
                <?php 
                    //главное меню
                    include_once "inc_block/main_menu.php";
                ?>

                    <div style="text-align: right; margin: 2px;"><!-- приветствие -->
                        <p><?php if(Core::$userData['name']){
                                //$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения $user_role
                                //готовим картинку
                                //$img_path = "assets/media/images/user/".Core::$userData['id']."/ava.jpg";//путь к изображению
                                //проверяем наличие файла изображения
                                //if (!file_exists($img_path)) {
                                //	$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
                                //}
                                //$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 80); //ресайз изображения
                                //$h_view = $ava_size_massiv[0]; //полученная высота
                                //$w_view = $ava_size_massiv[1]; //полученная длинна
                                echo 'Здравствуйте '.Core::$userData['name'].'! <span class="glyphicon glyphicon-user"></span>';
                                echo '<ul class="nav navbar-nav navbar-right">';
                                echo '<li><a href="/Exit" rel="nofollow"><button class="btn btn-danger btn-sm">Выйти</button></a></li>';
                                echo '</ul>';
                                //echo "<p><a href='/Settings'><img src='/$img_path' height='$h_view' width='$w_view' class='img-circle'/></a></p>";
                            }else{
                                //$imgProcess = new MsIMGProcess;//для использования метода ресайза изображения
                                echo 'Добро пожаловать, Гость! <span class="glyphicon glyphicon-user"></span>';
                                echo '<div>';
                                echo '<a href="/Registration" rel="nofollow" class="myRegistrButton">Регистрация</a>';
                                echo '<a href="/Login" rel="nofollow" class="myLoginButton">Вход</a>';
                                echo '</div>';
                                //$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
                                //$ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 80); //ресайз изображения
                                //$h_view = $ava_size_massiv[0]; //полученная высота
                                //$w_view = $ava_size_massiv[1]; //полученная длинна
                                //echo "<p><img src='/$img_path' height='$h_view' width='$w_view' class='img-circle'/></p>";
                            } ?>
                        </p>
                    </div><!-- приветствие END-->

                    <div class="row"><!-- content row -->

                        <!-- основной контент средний блок -->
                        <div id="middleContent">
                            <div align="center">
                                <img src="/assets/images/404.jpg"  style=""/>
                                <br/><br/>
                                <p><b>ОШИБКА 404</b></p>
                            </div>
                        </div>
                        <!-- основной контент средний блок END -->

                    </div><!-- content row END -->
                    <?php
                    //подключаем вывод протокола работы приложения (если включен режим отладки)
                    //include_once('framework/components/massages/sysLog.php');
                    ?>
                </div>

                <div class="col-lg-2 col-md-2 sm-hidden xs-hidden"><!-- правый отступ -->
                </div>

            </div><!-- row -->
        </div><!-- container-fluid -->
    </main>
    <!----------------------------------------------------------- Основной контент страницы END------------------------------------------------->












    <!--------------------------------------------------------------------- Футер --------------------------------------------------------------->
    <footer class="footer" id="footer">
        <div class="container-fluid">
            <div class="row">

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding-top: 3%;">
                    <?php
                    //информеры счётчиков посещений
                    //include_once("inc_block/counter_informer.php");
                    ?>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    <p class="text-center" style="margin-top: 3%;"><small>
                            Copyright &copy; <?php echo date('Y'); ?> by MSS.<br/>
                            All Rights Reserved.<br/>
                            <?php echo Core::app()->config->app_name."<br>";
                            ?>
                        </small></p>
                </div>

                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                </div>

            </div><!-- row -->
        </div><!-- container-fluid -->
    </footer>
    <!-------------------------------------------------------------------- Футер END--------------------------------------------------------------->

</div><!-- ----------------------------------------------------------Wrapper END ----------------------------------------------------------- -->

<!-- <div class="clearfix"></div>  ОБРАЗЕЦ -->
</body>
</html>