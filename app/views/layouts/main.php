<!DOCTYPE html>
<html lang="ru">
<head>
<?php 
include_once "inc_block/head.php";
?>
</head>
<body id="body_ms">
<?php 
include_once "inc_block/counters_init.php";
?>
<header id="header">
<h1 style="display:none">Веб-разработка, руководства для чайников!</h1>
<span id="headerTitle"><?php echo Core::app()->config->app_name;?></span>
<!--<img src="/assets/media/images/main/header.jpg" class="img-responsive" style="width:100%;"/>-->
</header>

<div class="wrapper">
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="greeting">
    <section>
    <!-- <h2 style="display:none">Login\logout\registration</h2> -->
    <div><?php if (Core::$userData['name']) {
            echo 'Здравствуйте '.Core::$userData['name'].'! <span class="glyphicon glyphicon-user"></span>';
            //echo '<ul class="nav navbar-nav navbar-right">';
            echo '<a href="/Exit" rel="nofollow"><button class="btn btn-danger btn-sm">Выйти</button></a>';
            //echo '</ul>';
        } else {
            echo 'Добро пожаловать, Гость! <span class="glyphicon glyphicon-user"></span>';
            echo '<div>';
            echo '<a href="/Registration" id="registration_ms" rel="nofollow" class="myRegistrButton" onclick="return false;">Регистрация</a>';
            echo '<a href="/Login" id="login_ms" rel="nofollow" class="myLoginButton" onclick="return false;">Вход</a>';
            echo '</div>';
        } ?>
    </div>
    </section>
</div><!-- greeting END -->

<div class="container-fluid">
<div class="row">

<div class="col-lg-1 col-md-1 hidden-sm hidden-xs" style=""><!-- левый отступ -->
</div>

<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12" id="mainContentMS">
    <?php 
    //главное меню
    include_once "inc_block/main_menu.php";
    //подключаем виджет хлебные крошки
    //echo '<br/><br/><br/><br/>';
    //$breadcumb = new Breadcrumb;
    //$breadcumb->run();
    ?>

    <div class="row"><!--content row-->
       <!--основной контент средний блок-->
        <div id="middleContent">
            <?php 
            echo $this->content;
            ?>
        </div>
       <!--основной контент средний блок END-->
    </div><!--content row END-->
<?php
//include_once("inc_block/ukr_host_banner.php"); // баннер хостинга
//подключаем вывод протокола работы приложения (если включен режим отладки)
//include_once('framework/components/massages/sysLog.php');
?>
</div>

<div class="col-lg-1 col-md-1 hidden-sm hidden-xs"><!--правый отступ-->
</div>

</div><!--main row-->
</div><!--main container-fluid-->

<footer class="footer" id="footer">
<div class="container-fluid">
<div class="row">
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="padding-top: 3%;">
        <?php
        //информеры счётчиков посещений
        include_once("inc_block/counter_informer.php");
        ?>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"> 
        <p class="text-center" style="margin-top: 3%;"><small>
            Copyright &copy; <?php echo date('Y'); ?> by MSS.<br/>
            All Rights Reserved.<br/>
            <?php echo Core::app()->config->app_name."<br>";?>
        </small></p>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
        <p class="text-center" style="margin-top: 3%;"><small>Копирование и размещение материалов на других сайтах допускается при условии установки активной гиперссылки на источник - страницу с этой публикацией на<br/> <?php echo Core::app()->config->app_name;?></small></p>
    </div>
</div><!--row -->
</div><!--container-fluid-->
</footer>
</div> <!--Wrapper END-->
</body>
</html>
 <!--<div class="clearfix"></div> ОБРАЗЕЦ--> 