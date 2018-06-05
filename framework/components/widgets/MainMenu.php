<?php
/**
* Виджет: главное меню*/
class MainMenu
{
    public $elements = array();
 
    function __construct(){
        //$this->mysqli = MsDBConnect::getInstance()->getMysqli();

    }

    public function setLink($pageName,$cumbLevel){
        
        //var_dump($_SESSION['cumbs']);
    }
    
    public function run(){
        
    }
}
?>
<!-- ПРИМЕР БЛОКА:
<div class="container-fluid">
                <div class="row">
                    <div class="navbar navbar-inverse">
                        <div class="container-fluid">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#responsive-menu">
                                    <span class="sr-only"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <a class="navbar-brand"><img src="/assets/images/mss_logo.png" width="50" height="50"/></a>
                            </div>
                            <div class="collapse navbar-collapse" id="responsive-menu">
                                <ul class="nav navbar-nav">
                                    <li><a href="/home/site/v/1">Главная</a></li>
                                    <li><a href="/novosti/site/v/2">Новости</a></li>
                                    <li><a href="/skidki/site/v/3">Скидки</a></li>
                                    <li><a href="/dostavka/site/v/4">Доставка</a></li>
                                    <li><a href="/about/site/v/5">О нас</a></li>
                                    <li class="dropdown">
                                        <a href="/about/site/v/6" class="dropdown-toggle" data-toggle="dropdown">Категории<b class="caret"></b></a>
                                        <ul class="dropdown-menu">
                                            <li><a href="/about/site/v/6">Пункт 1</a></li>
                                            <li><a href="/dostavka/site/v/4">Пункт 2</a></li>
                                            <li><a href="/dostavka/site/v/4">Пункт 3</a></li>
                                            <li><a href="/dostavka/site/v/4">Пункт 4</a></li>
                                            <li class="divider"></li>
                                            <li><a href="/dostavka/site/v/4">Пункт 5</a></li>
                                        </ul>
                                    </li>
                                    <?php
                                        /**
 *                                          if(MSS::$user_role == 'Guest'){
 *                                             echo '<li><a href="/Login">Вход</a></li>';
 *                                         }else{
 *                                             echo '<li><a href="#">Личный кабинет</a></li>';
 *                                         }
 */
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
-->