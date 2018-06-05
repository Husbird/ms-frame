<?php
//переработано+
/**
 * @author Biblos
 * @copyright 2014
#########################################
#	CAPTCHA						        #
#	фаил - класс:                       #
#    Completely Automated Public Turing # 
#    test to tell Computers and         # 
#    Humans Apart			            #
#	MsCaptcha.class.php  		    	#
#										#
#	DESIGNED BY M & S 					#
#	03.12.2016                          #
#######################################*/

class Captcha
{
	//настройки:

    //сообщение о бане по ip (вместо кнопки формы)
    //public $banMassage = 'ВНИМАНИЕ: Ваш IP-адрес заблокирован, сообщите администрации сайта';
    //сообщение об отсутствии комментариев
    public $noCommentsMassage = '
            <div class="alert alert-success" role="alert">
                <p>Не верно введён код с картинки</p>
            </div>
        ';
    //режим отладки:
    //public $debugMode = false; //true - включён; false - выключен
    

    public $mysqli; //метка соединения
    
    //для getSekretImg() свойства по умолчанию
    public $width = 200;//Ширина изображения
    public $height = 90;//Высота изображения
    public $font_size = 19;//Размер шрифта
    public $let_amount = 5;//Количество символов, которые нужно набрать
    public $fon_let_amount = 30;//Количество символов на фоне
    public $font = "assets/fonts/europe_normal.ttf";
    public $cod;//код с картинки
    public $captcha_img_name = false;
    

    function __construct(){
        $this->mysqli = Core::app()->DBase; //получаем метку соединения
    }

    public function getSekretImg(){
        $width = $this->width;//Ширина изображения
        $height = $this->height;//Высота изображения
        $font_size = $this->font_size;//Размер шрифта
        $let_amount = $this->let_amount;//Количество символов, которые нужно набрать
        $fon_let_amount = $this->fon_let_amount;//Количество символов на фоне
        $font = $this->font;//Путь к шрифту (относительно корня сайта)

        $letters = array("2","b","e","4","s","h","7","t","a","z"); //набор символов
        $colors = array("90","110","130","150","170","190","210","90","110","150"); //цвета

        $src = imagecreatetruecolor($width,$height);    //создаем изображение       
        $fon = imagecolorallocate($src,255,255,255);    //создаем фон
        imagefill($src,0,0,$fon);                       //заливаем изображение фоном

        for($i=0;$i < $fon_let_amount;$i++) {         //добавляем на фон буковки
            $color = imagecolorallocatealpha($src,rand(0,255),rand(0,255),rand(0,255),100);//случайный цвет
            $letter = $letters[rand(0,sizeof($letters)-1)];//случайный символ                           
            $size = rand($font_size-2,$font_size+2);//случайный размер                                           
            imagettftext($src,$size,rand(0,45),
            rand($width*0.1,$width-$width*0.1),
            rand($height*0.2,$height),$color,$font,$letter);
        }
        //то же самое для основных букв
        for($i=0;$i < $let_amount;$i++)  {
            $color = imagecolorallocatealpha($src,$colors[rand(0,sizeof($colors)-1)],
            $colors[rand(0,sizeof($colors)-1)],
            $colors[rand(0,sizeof($colors)-1)],rand(20,40));
            $letter = $letters[rand(0,sizeof($letters)-1)];
            $size = rand($font_size*2-2,$font_size*2+2);
            $x = ($i+1)*($font_size + 15) + rand(1,5);//даем каждому символу случайное смещение ($font_size + 15) - расстояние между символами
            $y = (($height*2)/3) + rand(1,10);                           
            $cod[] = $letter;//запоминаем код
            imagettftext($src,$size,rand(0,15),$x,$y,$color,$font,$letter);
        }

        $this->captcha_img_name = rand(1, 10000);

        if ($_SESSION['captcha_img_name'] > 0) {
            $this->secPicDelete();
        }

        $_SESSION['captcha_img_name'] = $this->captcha_img_name;

    imagegif($src,'assets/temp/captcha/pic_'.$this->captcha_img_name.'.gif');//пишем картинку в папку
    //imagegif($src,'assets/pic_'.$_COOKIE["PHPSESSID"].'.gif');//пишем картинку в папку
    $cod = implode("",$cod);  //переводим код в строку
    $this->cod = strtolower($cod);
    $_SESSION['captcha_secpic'] = $cod; //пишем в сессию код картинки
    //return $cod = implode("",$cod);  //переводим код в строку
    //echo 'getSekretImg: $letter: '; var_dump($letter);
    //echo 'getSekretImg: '; var_dump($this->cod);
     //echo 'getSekretImg $_SESSION: '; var_dump($_SESSION['captcha_secpic']);
    }

    //показать капчу
    public function showCaptcha() {
        echo <<<HERE
        <div class="form-group" id="captchaBlock">
            <label for="cod"></label>
            <p style="background-color:white;  max-width:45%; margin: 0 auto;">Введите код с картинки:</p>
HERE;
        $this->getSekretImg();
        echo <<<HERE
            <img src="/assets/temp/captcha/pic_{$_SESSION['captcha_img_name']}.gif" id="captchaImg" />
            
            <p><button type="button" onclick="reloadCaptchaImg()" class="btn btn-default btn-sm" style="background-color:;">Обновить картинку</button></p>
            <script type='text/javascript'>
                //удаляем картинку с сервера
                // с задержкой, чтобы браузер успел загрузить картинку
                function captchaDeleteImg() {
                    var RequestСaptchaDeleteImg = new AjaxReq();
                    console.log("captchaDeleteImg: Отправляю запрос на удаление картинки из ВЬЮХИ");
                    RequestСaptchaDeleteImg.requestGet("/ajax/captchaPictureDelete", true);
                }
                setTimeout(captchaDeleteImg, 2000);
            </script>
            
            
            <div style="max-width: 20%; margin-left:41%;">
                <input type="text" name="cod" size="2" required="required" placeholder="код" class="form-control" />
            </div>
            <p style='color:red'><b>{$_SESSION['captchaCheckErrorMassage']}</b></p>
        </div>
HERE;
     unset($_SESSION['captchaCheckErrorMassage']);
    }
    
    //проверка правильности ввода кода с картинки
    public function captchaCodCheck($cod) {
        $cod = strtolower(trim(strip_tags($cod)));
        //var_dump($cod); var_dump($_SESSION['captcha_secpic']);die;
        if ($cod === $_SESSION['captcha_secpic']) {
            unset($_SESSION['captcha_secpic']);
            return true;
        } else {
            unset($_SESSION['captcha_secpic']);
            return false;
        }
        
    }
    //удаляем картинку из директории
    public function secPicDelete() {
        if (file_exists('assets/temp/captcha/pic_'.$_SESSION['captcha_img_name'].'.gif')) {
            if (unlink('assets/temp/captcha/pic_'.$_SESSION['captcha_img_name'].'.gif')) {
                $_SESSION['captcha_img_name'] = false;
                return true;
            } else {
                //$_SESSION['captcha_img_name'] = false;
                return false;
            }
        } else {
            return true;
            //$_SESSION['captcha_img_name'] = false;
            //echo __METHOD__." Нет такой картинки!";
        }
    }
}
?>