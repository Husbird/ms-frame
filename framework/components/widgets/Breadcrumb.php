<?php
/**
* Виджет: хлебные крошки*/
class Breadcrumb
{

 
    function __construct(){
        //$this->mysqli = MsDBConnect::getInstance()->getMysqli();

    }
    
    //формируем массив с элементами списка ссылок. Передаём $pageName - заголовок страницы; 
    // $cumbLevel - номер по счёту в строке виджета (является максимальным количеством выводимых уровней (ссылок в виджете))
    //по умолчанию под номером 1 всегда "Главная" с сылкой "/"
    public function setLink($pageName,$cumbLevel){
        $link = $_GET['route'];
        $key = $cumbLevel - 1;//меняем с учётом нулевого элемента
        //var_dump($cumbLevel);die;
        //var_dump($link);
        $_SESSION['cumbs'][0] = "<li><a href='/' rel='nofollow'>Главная</a> <span class='divider'>/</span></li>"; 
        //если перешли на главную страницу (с заголовком 'Главная страница'), сбрасываем все ссылки и ставим только ссылку на главную стр
        if($pageName == 'Главная страница'){
            unset($_SESSION['cumbs']);
            $_SESSION['cumbs'][0] = "<li><a href='/' rel='nofollow'>Главная</a> <span class='divider'>/</span></li>"; 
        }else{
            $_SESSION['cumbs'][$key] = "<li><a href='/$link' rel='nofollow'>$pageName</a> <span class='divider'>/</span></li>";    
        }
        //считаем сколько ссылок в массиве
        $countElements = count($_SESSION['cumbs']);
        if($countElements > $cumbLevel){
            //избавляемся от лишних элементов
            $cutSome = array_slice($_SESSION['cumbs'], 0,$cumbLevel,true);
            //перезагружаем содержимое сессии $_SESSION['cumbs']
            unset($_SESSION['cumbs']);
            $_SESSION['cumbs'] = $cutSome;
        }
        //var_dump($_SESSION['cumbs']);
    }
    
    public function run(){

       echo '<div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <ul class="breadcrumb">';

                            foreach($_SESSION['cumbs'] as $key => $value){
                                echo $value;
                            }

       echo '            </ul>
                    </div>
                </div>
           </div>';

    } 
}
?>