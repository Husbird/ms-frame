<?php
//обработка ссылок на видео (youtube, VK)
class VLinkParser
{
    public $videoPlayer; //сгенерированный (голый) код плеера, готовый к выводу на странице
    public $linkInfo; //полученная информация о ссылке

    function __construct($link = false, $run = false) {
        
        if(!$link){
            echo '<br/>
            <div class="alert alert-warning" role="alert">
                Видео пока нет =(...
            </div>
            <br/>';
            return false;
        }
        $link = stripslashes($link); //убираем слеши экранирования
        $len = strlen($link); //кол-во букв (байт) в строке
        
        //если прямая ссылка на видео youtube +
        if(stristr($link, 'youtu.be')){
            //echo $link;
            $cutLink  = substr($link, 17);
            $this->linkInfo = "<br>ссылка ютуб - прямая - $len символов: рез - <b>$cutLink</b><hr>";
            $this->videoPlayer = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$cutLink.'" frameborder="0" allowfullscreen></iframe>';
        //если фрейм ютуба +
        }elseif(stristr($link, 'src="https://www.youtube.com/')){
            //echo $link;
            $this->linkInfo = "<br>фрейм ютуба - $len символов.<hr>";
            $this->videoPlayer = $link;
        //если ссылка обычная из адресной строки youtube +
        }elseif(stristr($link, 'www.youtube.com/watch?')){
            //echo $link;
            $cutLink  = substr($link, 32);
            $this->linkInfo = "<br>ссылка ютуб - общая - $len символов: рез - <b>$cutLink</b><hr>";
            $this->videoPlayer = '<iframe width="560" height="315" src="https://www.youtube.com/embed/'.$cutLink.'" frameborder="0" allowfullscreen></iframe>';
        //фрейм из VK    
        }elseif(stristr($link, 'iframe src="//vk.com')){
            $this->linkInfo = "<br>ссылка VK - фрейм - $len символов.<hr>";
            $this->videoPlayer = $link;
        }else{
            $this->linkInfo = "ссылка не распознана =( $len символов";
            echo '<br/>
            <div class="alert alert-danger" role="alert">
                <b>Ошибка!</b> Загрузить видео не удалось =(...
            </div>
            <br/>';
            return false;
        }
        //если $run=true - выводим видео
        if($run){
            $this->videoRun();
            return true;
        }
        return true;
    }
    
    //вывод видео
    public function videoRun(){
        echo $this->videoPlayer;
    }
}
//обработка ссылок на видео (youtube, VK)
//https://www.youtube.com/watch?v=LXTsdBolZus (43)
//https://www.youtube.com/watch?v=94SAJxgFX-8 (43)
//https://youtu.be/axZCVTtfo68 (27)
//https://youtu.be/6GSxUoxZHIQ (27)
?>