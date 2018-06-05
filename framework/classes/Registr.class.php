<?php
//Регистрация пользователей
class Registr
{
    public $sitePath = null; //текущий адрес сайта по умолчанию
    public $id; //id авторизованного пользователя
    public $name = false;
    public $lastname = false;
    public $patronymic = false;
    public $email = false;
    public $pass = false;
    
    private $pdo = null;

    //полученный id строки добавленного пользователя (для autoAuth)
    private $inserted_user_id;
    
    function __construct($params = false) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] Начинаю регистрацию пользователя ...");
            //parent:: __construct(); info@
        $this->sitePath = Core::app()->config->site_path;//инициализация текущего адреса сайта - настраивается в файле конфигурации!!!!
        //var_dump($this->sitePath);die;
            $this->pdo = Core::app()->DBase; //получаем метку соединения
          // если получен массив (из формы регистрации):
        if (is_array($params)) {
            //var_dump($this->link);
            $this->name = Core::app()->checkStr($params['name']);
            $this->patronymic = Core::app()->checkStr($params['patronymic']);
            $this->lastname = Core::app()->checkStr($params['lastname']);
            $email = strtolower(Core::app()->checkStr($params['email']));

            //проверка наличия в базе принимаемого email
            $cheсkDubble = $this->cheсkDubbleEmail($email);
            if (!$cheсkDubble) {
                //возвращаем на страницу регистрации
                header("location:/Registration");
                exit();
            }

            //echo $email;exit();
            $pass = Core::app()->checkStr($params['pass']);
            $hash = self::hashGen($email); //генерируем случайную строку для отправки на имэил
            //пишем проверочный hash  и остальные данные в куку
            //unset($_COOKIE['hash']);
            setcookie("hash", $hash, time()+3600*24*2);
            setcookie("name", $this->name, time()+3600*24*2);
            setcookie("patronymic", $this->patronymic, time()+3600*24*2);
            setcookie("lastname", $this->lastname, time()+3600*24*2);
            //setcookie("birthday", $birthday, time()+3600*24*2);
            setcookie("email", $email, time()+3600*24*2);
            setcookie("bible", $pass, time()+3600*24*2);
            $sendCheckMail = self::sendCheckMail($email, $this->name, $hash);//отправляем письмо с инструкциями

            if ($sendCheckMail) {
                //сохраняем аву во временную папку:
                $structure = './assets/media/images/user/temp';// Желаемая структура папок

                if (is_dir('assets/media/images/user/temp')) {
                    $save_path = "assets/media/images/user/temp/".$hash.".jpg";
                    $MsIMGProcess = new IMGProcess;
                    $MsIMGProcess->cut_and_save_img_mss(200,5,$save_path);

                    Core::app()->setLog(__METHOD__."[".__LINE__."] Ава пользователя сохранена путь: ".$save_path);
                } else {
                    if (!mkdir($structure, 0777, true)) {
                        Core::app()->setLog(__METHOD__."[".__LINE__."] Не удалось записать аву пользователя во временную папку ");
                    } else { //если удалось создать папку пишем аву во временную папку
                        $save_path = "assets/media/images/user/temp/".$hash.".jpg";
                        $MsIMGProcess = new IMGProcess;
                        $MsIMGProcess->cut_and_save_img_mss(200,5,$save_path);

                        Core::app()->setLog(__METHOD__."[".__LINE__."] 
                            email: (".$email.", Ава пользователя сохранена, путь: (".$save_path.")");
                    }
                }
                header("location:$this->sitePath/checkYourMail");
                exit();
            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    Ошибка отправки письма подтверждения регистрации! (die)");
                die('Ошибка обработки данных...');
            }
        //если получен НЕ массив (а строка Хеш из письма активации)
        } elseif (!is_array($params)) {
            $checkHash = self::checkHash($params);//сверяем полученный хеш
            //если хеш успешно проверен:
            if($checkHash == true){
               // var_dump($checkHash); die();
               $saveUserData = self::saveUser();//пишем данные пользователя в БД
               //если данные пользователя успешно записаны в БД:
               if($saveUserData == true){
                    $autoAuth = self::autoAuthUser();//авторизируем нового пользователя
                    //если пользователь успешно авторизован:
                    if($autoAuth == true){
                        $sendMail = self::sendRegDataMail($this->email, $this->pass, $this->name, $this->lastname);//отправляем рег.данные пользователю
                        if($sendMail){
                            header("location:$this->sitePath/congratulations");//отправляем на главную страницу
                            exit();
                        }
                    } else {
                        Core::app()->setLog(__METHOD__."[".__LINE__."] 
                            Ошибка авторизации, возможно у вас выключены \"cooke\" в браузере... (die)");

                        die('Error: Ошибка авторизации, возможно у вас выключены "cooke" в браузере...');
                    }
               }
            } else {
                header("location:$this->sitePath/regError");//отправляем на главную страницу regError
                exit();
            }
            //var_dump($checkHash); die();
        }
    }
    
    public function checkHash($hash) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] Сверяю полученный хеш...)");
        if (!$hash) {
            Core::app()->setLog(__METHOD__."[".__LINE__."] Хеш не получен!... (die)");
            die('Ошибка проверки данных пользователя');
        }
        //var_dump($hash);
        //echo "<br>";
        //var_dump($_COOKIE['hash']);die;
	   //echo '<br>Хеш получен:'.$hash;die;
        $hashFromMail = Core::app()->checkStr($hash);
        $cookieHash = Core::app()->checkStr($_COOKIE['hash']);

        if ($hashFromMail == $cookieHash) {
            Core::app()->setLog(__METHOD__."[".__LINE__."] Хеш - совпал! (die)");
            return true;
        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] Хеш НЕ совпал! (return false)", "e");
            return false;
        }
    }
    
    private function saveUser() {
        Core::app()->setLog(__METHOD__."[".__LINE__."] Обрабатываю полученные данные из формы...");
        //получаем из $_COOKIE данные и подготавливаем их к записи в БД
        $this->name = Core::app()->checkStr($_COOKIE['name']);
        $this->lastname = Core::app()->checkStr($_COOKIE['lastname']);
        $this->patronymic = Core::app()->checkStr($_COOKIE['patronymic']);
        $email = Core::app()->checkStr($_COOKIE['email']);

        $this->pass = Core::app()->checkStr($_COOKIE['bible']);
        $passMd5 = md5(md5($this->pass));//шифруем пароль для записи в БД

        $email_check = self::checkEmail($email);
        if ($email_check == true) {
            $this->email = $email;
        } else {
            Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] 
                некорректный имэил: (".Core::app()->checkStr($email).")", "e");

            $this->email = 'некорректный имэил: '.Core::app()->checkStr($email);
        }
        //$age = self::clearInt($age);
        $date_reg = time();//дата записи
        //$ip_reg = self::GetRealIp();//$ipTrue = Core::app()->getRealIp();
        $ip_reg = Core::app()->getRealIp();
        $adm_mss = 0;//уровень прав пользователя

        //работает !!!!

        Core::app()->setLog(__METHOD__."[".__LINE__."] Начинаю запись данных пользователя в БД ...");

        /* создаем подготавливаемый запрос */
        //$sql = "INSERT INTO user (pass, name, patronymic, lastname, email, date_reg, ip_reg, adm_mss) VALUES(?, ?, ?, ?, ?, ?, ?, ?)";
        $sql = "INSERT INTO user (pass, name, patronymic, lastname, email, date_reg, ip_reg, adm_mss) 
                    VALUES (:pass, :name, :patronymic, :lastname, :email, :date_reg, :ip_reg, :adm_mss)";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(
                ':pass' => $passMd5, ':name' => $this->name, ':patronymic' => $this->patronymic,
                ':lastname' => $this->lastname, ':email' => $this->email, ':date_reg' => $date_reg,
                ':ip_reg' => $ip_reg, ':adm_mss' => $adm_mss
            ));

            $last_insered_id = $this->pdo->lastInsertId();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

            if ($last_insered_id > 0) {
                $this->inserted_user_id = $last_insered_id;

                Core::app()->setLog(__METHOD__."[".__LINE__."] запрос: ".$sql." прошел успешно...");

                //копируем аву из временной папки в папку пользователя
                $MsFileProcess = new FileProcess;
                $MsFileProcess->rename_one_file("assets/media/images/user/temp/".$_COOKIE['hash'].".jpg",
                    "assets/media/images/user/".$last_insered_id, "assets/media/images/user/".$last_insered_id."/ava.jpg");

                //удаляем куки: ОБЯЗАТЕЛЬНО!
                Core::app()->setLog(__METHOD__."[".__LINE__."] Удаляю cookie...");
                setcookie("hash", "",time()-100,"/");
                setcookie("name", "",time()-100,"/");
                setcookie("lastname", "",time()-100,"/");
                setcookie("patronymic", "",time()-100,"/");
                setcookie("email", "",time()-100,"/");
                setcookie("bible", "",time()-100,"/");

                return true;
            }

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
            Core::app()->setLog(__METHOD__."[".__LINE__."] Данные не занесены в БД!");
            exit("Ошибка регистрации...");
        }
    }
    
    //автоматическая авторизация пользователя (использовать только сразу после регистрации (saveUser))
    private function autoAuthUser() {
        Core::app()->setLog(__METHOD__."[".__LINE__."] Запускаю автоматическую авторизацию пользователя...");

        //Обновляю хеш и ip пользователя в БД
        Core::app()->setLog(__METHOD__."[".__LINE__."] Обновляю Хеш и ip пользователя в БД...");

        $hash = md5(self::generateCode(10));
        $ip = Core::app()->getRealIp();

        //$sql = "UPDATE user SET hash = '$hash', ip = '$ip' WHERE id = '$id'";
        $sql = "UPDATE user SET hash = :hash, ip = :ip WHERE id = :id";
        Core::app()->setLog(__METHOD__."[".__LINE__."] Выполняю sql запрос: (".$sql.")");

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute( array(":hash" => $hash, ":ip" => $ip, ":id" => $this->inserted_user_id) );
            //var_dump($stmt) = object(PDOStatement)#15 (1) { ["queryString"]=> string(53) "UPDATE user SET hash =
            // :hash, ip = :ip WHERE id = :id" }
        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                Ошибка обновления данных пользователя: (".$e->getMessage().")");

            return false;
        }

        Core::app()->setLog(__METHOD__."[".__LINE__."] 
            Обновление Хеш и ip прошло успешно. Ставлю метку авторизации в сессию, и cookie на 2 часа...");

        $_SESSION['auth'] = true; //ставим метку в сессии о успешной авторизации

        setcookie("id", $id, time()+3600*2,"/");//пишем в cooke id пользователя
        setcookie("hash", $hash, time()+3600*2,"/");//пишем в cooke новый хеш
        setcookie("ip", $ip, time()+3600*2,"/");//пишем в cooke текущий ip пользователя

        return true;
    }
    
    private function hashGen($string) {
        $salt = rand(1000, 1000000);
        $word = $salt.$string;
        // получение хэша
        $hash = md5($word);
        return $hash;//md5($hash);
    }
    
    //проверка имэила на синтаксис
    private function checkEmail($email) {
        //создаём массив ошибок
        $error = array();
        if (isset ($email)) {
            $email = strip_tags(trim($email));
            if ($email != "") {
                $regV = '/^[a-zA-Z0-9\-\_\.]{1,25}\@[a-zA-Z0-9\-\_]{2,15}\.[a-zA-Z0-9]{2,4}$/';
                $rez = preg_match($regV, $email);
                if (!$rez) {
                    $error[] = "<span style='color: red'>некорректный E-mail (не будет сохранён)</span>";
                    if (strlen($email) > 46) $error[] = "Больше 46 символов";
                }
            } else {
                $error[] = "<span style='color: red'>E-mail не введён!</span>";
            }
            if (count($error) == 0) {
                return true;
            } else {
                return false;
            }
        }
    }




    //проверка введённого в форме регистрации email на наличие в БД 
    //(если дубликат найден cheсkDubbleEmail возвращает false, если нет то true)
    private function cheсkDubbleEmail($email = false) {
        Core::app()->setLog("проверяем на наличие в БД переданного email: (".$email.")...");

        $email = strtolower(Core::app()->checkStr($email));
        //$sql = "SELECT id FROM user WHERE email = ?";
        $sql = "SELECT id FROM user WHERE email = :email";

        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(array(':email' => $email));
            $data = $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

            $id = ($data[0]) ? data[0] : false;

        } catch(PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
            return false;
        }

        if ($id) {
            Core::app()->setLog("пользователь с таким Email уже зарегистрирован!");

            //пишем в сессию сообщение об ошибке и другие данные для передачи в форму регистрации
            $_SESSION['registration_error'] = '<div class="alert alert-danger" role="alert"><b>Ошибка!</b> 
                                                    Пользователь с Email (<b>'.$email.'</b>) уже зарегистрирован!
                                               </div>';
            $_SESSION['registration_form_data']['email'] = $email;
            $_SESSION['registration_form_data']['reg_form_status_email'] = 'has-error'; //выделяем поле с ошибкой

            //пишем в массив данные для возврата в форму регистрации
            $_SESSION['registration_form_data']['name'] = $this->name;
            $_SESSION['registration_form_data']['reg_form_status_name'] = 'has-success';//выделяем поле как успешное

            $_SESSION['registration_form_data']['patronymic'] = $this->patronymic;
            $_SESSION['registration_form_data']['reg_form_status_patronymic'] = 'has-success';//выделяем поле как успешное

            $_SESSION['registration_form_data']['lastname'] = $this->lastname;
            $_SESSION['registration_form_data']['reg_form_status_lastname'] = 'has-success';//выделяем поле как успешное

            $_SESSION['registration_form_data']['reg_form_status_pass'] = 'has-success';//выделяем поле как успешное

             return false;
        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] Ok... введённый Email (".$email.") используется впервые...");
            return true;
        }
    }
    
    private function sendCheckMail($email, $name, $hash) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    Отправляю письмо с инструкцией по регистрации...");
        //Отправляем пользователю ссылку для регистрации
        if (!$this->sitePath) {
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    не указан адрес сайта! ... die");

            die("Ошибка при отправке почты...");
        }
        $fakeHash = md5($hash);
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $name);
        /* получатели */
        $to  = "user <".$email.">," ; //обратите внимание на запятую
        $to .= "ms <ms-projects@mail.ru>";


        /* тема\subject */
        $subject = "Регистрация на сайте $this->sitePath";

        /* сообщение */
        $message = "
        <html>
        <head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
        </head>
        <body>
        <table>
        <center>
        <h4>Здравствуйте ".$name." !</h4> <h5>Вы регистрируетесь на сайте $this->sitePath ...</h5>
        </center>
        <tr>
            <td><b>Для завершения Вашей регистрации, <br>перейдите пожалуйста по следующей ссылке:</b>
            <span style='color:#333'><a href='".$this->sitePath."/activate/".$hash."'>".$this->sitePath."/activate/".$fakeHash."</a></span>
            </td>
         </tr>
         <tr>
            <td>
                <i><span style='color:green'>телефоны для справок:</span></i><br>
                <i>+38 (066)357-99-57</i><br>
                <i>+38 (073)450-87-82</i><br>
                
            </td>
        </tr>
         <tr>
            <td><i><span style='margin-left:300px'>С уважением, администрация $this->sitePath</i></td>
         </tr>
         <tr>
            <td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
         </tr>
        </table>
        </body>
        </html>
        ";

        /* Для отправки HTML-почты вы можете установить шапку Content-type. */
        $headers  = "MIME-Version: 1.0 \r\n";
        //$headers .= "Content-type: text/html; charset=windows-1251\r\n";
        $headers .= "Content-type: text/html; charset = utf-8 \r\n";

        /* дополнительные шапки */
        $headers .= "From: ".$this->sitePath." \r\n";
        /*$headers .= "Cc: birthdayarchive@example.com\r\n";
        $headers .= "Bcc: birthdaycheck@example.com\r\n";*/

        // Для отправки HTML-письма должен быть установлен заголовок Content-type
        /* и теперь отправим из */ //mail('*@gmail.com', 'Messages from your site', $message, "Content-type:text/html; charset = utf-8");
        $sendMail = mail($to, $subject, $message, $headers);
        if ($sendMail) {
            return true;
        } else {
            $errorMessage = error_get_last()['message'];
            var_dump($errorMessage);
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    Не удалось отправить письмо пользователю! ... die ");

            die(__METHOD__." Ошибка при отправке сообщения");
        }
    }

    //отправка пользователю регистрационных данных
    public function sendRegDataMail($email, $pass, $name,$lastname) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] Отправляю письмо с регистрационными данными...");
        $sitePath = $this->sitePath;
        /* получатели */
        //смена кодировки с utf8 на 1251
        //$name = iconv('UTF-8', 'windows-1251', $name);
        //$lastname = iconv('UTF-8', 'windows-1251', $lastname);
        
		$to  = "user <".trim($email).">," ; //обратите внимание на запятую
		$to .= "ms <ms-projects@mail.ru>";
		
		
		/* тема\subject */
		$subject = "info@$this->sitePath";
		
		/* сообщение */
		$message = "
		<html>
		<head>
            <meta charset='windows-1251' />
            <meta http-equiv='Content-Type' content='text/html; windows-1251' />
		</head>
		<body>
		<table>
		<center>
		<h4>Уважаемый ".$name." ".$lastname." !</h4> <h5>Вы успешно зарегистрировались на сайте $this->sitePath</h5>
		</center>
		<tr>
			<td><b>Ваши регистрационные данные:</b><br>
            E-mail: <b>".$email."</b><br>
            Пароль: <b>".$pass."</b><br>
            <p>Рекомендуем сохранить эти данные в надёжном месте, и не передавать 3-м лицам.<br>
            <b><span style='color:red'>ПОМНИТЕ!</span></b> Администрация сайта никогда не будет спрашивать ваши регистрационные данные!</p>
            
            <center><span style='color:#333'><a href='".$sitePath."' title='Перейти на сайт'>Перейти на сайт!</a></span></center>
			</td>
		 </tr>
		 <tr>
			<td>
				<i><span style='color:green'>телефоны для справок:</span></i><br>
				<i>+38 (066)357-99-57</i><br>
				<i>+38 (073)450-87-82</i><br>
			</td>
		</tr>
		 <tr>
			<td><i><span style='margin-left:300px'>С уважением администрация $this->sitePath</i></td>
		 </tr>
		 <tr>
			<td><span style='color:red'>P.S. если это письмо попало к вам по ошибке - просто удалите его</span></td>
		 </tr>
		</table>
		</body>
		</html>
		";
		
		/* Для отправки HTML-почты вы можете установить шапку Content-type. */
		$headers  = "MIME-Version: 1.0 \r\n";
		$headers .= "Content-type: text/html; charset = utf-8 \r\n";
		
		/* дополнительные шапки */
		$headers .= "From: Prosportpit.com \r\n";
		/*$headers .= "Cc: birthdayarchive@example.com\r\n";
		$headers .= "Bcc: birthdaycheck@example.com\r\n";*/
		
		/* и теперь отправим из */
		mail($to, $subject, $message, $headers);
        if (mail) {
            return true;
        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
                Не удалось отправить письмо c регистрационными данными пользователю! (die)");
            die("Ошибка при отправке сообщения");
        }
    }
	
	
    public function logOut() {
        setcookie('id', '', time()-60*60*24*30, '/'); 
		setcookie('hash', '', time()-60*60*24*30, '/');

        Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] отработал автовыход!", "n");
    }

	//генерирование случайного числа
	public function generateCode($length=6) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;
        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        return $code;
   }
}
?>