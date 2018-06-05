<?php
//Переработано+++
// убрать в clearInt - переназаначить на Core
class Authoriz {

    public $sitePath = '/Login'; //адрес страницы авторизации

    private $pdo = null;

    function __construct($params = false) {
    //parent:: __construct();

        $this->pdo = Core::app()->DBase; //получаем метку соединения
      // если получен массив (из формы регистрации):
        if ( is_array($params) ) {
             Core::app()->setLog(__METHOD__ ."[".__LINE__."] Начинаю авторизацию...");

            //получаем и проверяем e-mail
            $email = strtolower(Core::app()->checkStr($params['email']));
            $checkEmail = self::checkEmail($email);

            if ($checkEmail != true) {//проверка имэйла такой же функцией как и при регистрации
                header("location:$this->sitePath");//отправляем на главную страницу regError
                exit();
            }
            //получаем и проверяем пароль
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] получаю и проверяю пароль...");
            $pass = Core::app()->checkStr($params['pass']);
            $pass = md5(md5($pass));

            //готовим новый хеш
            $hash = md5(self::generateCode(10));

             ///получаем текущий ip
            $ip = Core::app()->getRealIp();
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] получен текущий ip пользователя: ".$ip);
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] запрашиваю данные пользователя... ");

                $sql = "SELECT * FROM user WHERE pass = :pass AND email = :email";
                //var_dump($sql);die;
                //анализ запроса и подготовка к исполнению
                try {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR); //PDO::PARAM_STR
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR); //PDO::PARAM_STR
                    $stmt->execute();
                    $data = $stmt->fetchAll(PDO::FETCH_COLUMN, 'id');

                    $_SESSION['db_query_num']++;
                    $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

                    $id = $data[0];
                } catch(PDOException $e) {
                    $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
                    return false;
                }

                //если проверка логина и пароля пройдена успешно (пользователь найден):
                if ($id > 0) {
                    Core::app()->setLog(__METHOD__ ."[".__LINE__."] обновляю данные пользователя
                        в БД...");

                    $sql = "UPDATE user SET hash = :hash, ip = :ip WHERE id = :id";//подготовка запроса
                    try {
                        $stmt = $this->pdo->prepare($sql);
                        //$stmt->bindParam(':hash', $hash, PDO::PARAM_STR);
                        //$stmt->bindParam(':ip', $ip, PDO::PARAM_STR);
                        //$stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute(array(':hash' => $hash, ':ip' => $ip, ':id' => $id));

                        $_SESSION['db_query_num']++;
                        $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

                    } catch (PDOException $e) {
                        $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
                        return false;
                    }
                    /* закрываем запрос */
                    //$stmt->close();            // ВНИМАНИЕ!!! Закрывать обязательно!!!
                    if ($stmt) {
                        Core::app()->setLog("данные пользователя успешно обновлены...");
                        Core::app()->setLog("в БД записан ip: ".$ip." (пишу его в cookie ...)");
                        Core::app()->setLog("в БД записан хеш: ".$hash." (пишу его в cookie ...)");
                        Core::app()->setLog("Cтавлю метку об авторизации в сессию : true");

                        $_SESSION['auth'] = true; //ставим метку в сессии о успешной авторизации

                        Core::app()->setLog("ip уже в сессии: (\$_SESSION[ip_current]): ".$_SESSION['ip_current']." 
                            (пишу его в cookie...)");

                        //если данные обновлены успешно и получена отметка 72 ставим куки на 3 дня
                        if ($params['optionsRadios'] == 72) {
                            Core::app()->setLog(__METHOD__ ."[".__LINE__."]
                             пользователь прошел авторизацию, его данные обновлены, ставлю cookie на 72 часа");

                             //ставим куки на 72 часа
                            setcookie("id", $id, time()+3600*24*3);
                            setcookie("hash", $hash, time()+3600*24*3);
                            setcookie("ip", $ip, time()+3600*24*3);

                        //если выбрали чужой компьютер ставим куки на 2 часа
                        } elseif ($params['optionsRadios'] == 2) {
                            Core::app()->setLog(__METHOD__ ."[".__LINE__."]
                             пользователь прошел авторизацию, его данные обновлены, ставлю cookie на 2 часа");
                            //если чекбокс не отмечен ставим куки на 24 часа
                            setcookie("id", $id, time()+3600*2);
                            setcookie("hash", $hash, time()+3600*2);
                            setcookie("ip", $ip, time()+3600*2);

                        
                         //если выбрали чужой компьютер ставим куки на 14 дней
                        } elseif ($params['optionsRadios'] == "2weeks") {
                            Core::app()->setLog(__METHOD__ ."[".__LINE__."]
                             пользователь прошел авторизацию, его данные обновлены, ставлю cookie на 14 дней");
                            //ставим куки на 72 часа
                            setcookie("id", $id, time()+3600*24*14);
                            setcookie("hash", $hash, time()+3600*24*14);
                            setcookie("ip", $ip, time()+3600*24*14);

                        } else {
                            Core::app()->setLog(__METHOD__ ."[".__LINE__."]
                                Ошибка передачи данных из формы входа (чекбокс)!");
                        }
                        Core::app()->setLog(__METHOD__ ."[".__LINE__."] Отправляю на главную (location:/Welcome)!");

                        header("location:/Welcome");//отправляем на главную страницу (встречаем)
                        exit();
                    } else {
                        Core::app()->setLog(__METHOD__ ."[".__LINE__."] Данные пользователя обновить не удалось!");
                    }

                //если данные НЕ обновлены успешно:
                 //если пользователь не найден в БД: (id <=0 )
                } else {
                    Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                        неверный e-mail или пароль (пользователь не найден в БД!) 
                        Пишу сообщение в  \$_SESSION['authErr']");

                    $_SESSION['authErr'] = '<div class="alert alert-danger" role="alert">
                                                <b>Ошибка!</b> неверный e-mail или пароль
                                            </div>';
                     //отправляем на главную страницу regError:
                     header("location:$this->sitePath");
                     exit();
                }
       } else {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] Ошибка приёма данных в процессе авторизации!");
            header("location:$this->sitePath");
            exit();
       }
    }
    
    //проверка имэила
    private function checkEmail($email) {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                    проверяю синтаксис email... (".$email.")");
        //создаём массив ошибок
        $error = array();
        if (isset ($email)) {
                $email = strip_tags(trim($email));
                if ($email != "") {
                    $regV = '/^[a-zA-Z0-9\-\_\.]{1,25}\@[a-zA-Z0-9\-\_]{2,15}\.[a-zA-Z0-9]{2,4}$/';
                    $rez = preg_match($regV, $email);
                    if (!$rez) {
                        Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                                некорректный E-mail (не прошел регулярку)");
                        $error[] = "<font color='#00CC00' size='-2'>некорректный E-mail (не будет сохранён)</font>";
                        if (strlen($email) > 46) {
                            $error[] = "Больше 46 символов";
                            Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                                E-mail больше 46 символов!");
                        }
                    }
                } else {
                    $error[] = "<font color='#00CC00' size='-2'>E-mail не введён!</font>";
                    Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                    E-mail не введён!");
                }
            }

        if (count($error) == 0) {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                email успешно прошел проверку!");
            return true;
        } else {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                Неверный синтаксис e-mail!");

            $_SESSION['authErr'] = '<div class="alert alert-danger" role="alert">
                                        Ошибка! Неверный синтаксис e-mail: <b>'.$email.'</b>
                                    </div>';
            return false;
        }
    }

    //генерирование случайного числа
    public function generateCode($length=6) {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
            готовлю новый хеш...");

        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
        $code = "";
        $clen = strlen($chars) - 1;

        while (strlen($code) < $length) {
            $code .= $chars[mt_rand(0,$clen)];
        }
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
            хеш готов: (".$code.") ... ok");
        return $code;
    }
}
?>