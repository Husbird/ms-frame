<?php
//ООП +++
class ParserPost {

    //результат работы парсера
    public $action;//действие
    public $model_name;//имя модели
    private $mysqli = null; //метка соединения с БД
    public $params = array(); //массив полученных параметров из запроса POST
    
    public $result = array();//общий результат работы парсера (массив данных)
    
    function __construct($post = false) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] запускаю парсер запроса POST ...");

        if (!$post) {
            // Читаем данные, переданные в POST (AJAXом)
            $ajaxPost = file_get_contents('php://input');
            if ($ajaxPost) {
                require_once("framework/classes/AjaxParser.php");
                new AjaxParser($ajaxPost);

                Core::app()->setLog(__METHOD__."[".__LINE__."] запрос передан классу AjaxParser ...");
                die();
            } else {
                Core::app()->LogWriter->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'> Запрос пуст!</span>");
                die('Запрос пуст!');
            }
        }

        $this->mysqli = $this->mysqli = Core::app()->DBase; //получаем метку соединения c БД
        //ОБРАБОТКА ЗАПРОСА
        Core::app()->setLog(__METHOD__."[".__LINE__."] обработка POST запроса ...");

        $this->params = $this->filterPost($post); //входящий запрос обрабатываем фильтром
        
        //получаем action
        $this->getAction($this->params); //присваиваем свойству action значение (выбераем последний элемент массива)    
        //var_dump($this->action);die;

        Core::app()->setLog(__METHOD__."[".__LINE__."] 
            <span style='color:green'>определено действие: ".($this->action)."</span>");
        //var_dump($this->action);die();
        switch ($this->action) {
            //если отправлено из формы регистрации
            case "registration":
                //проверка правильности ввода кода с картинки
                $MsCaptcha = new Captcha;
                $captchaCodCheck = $MsCaptcha->captchaCodCheck($this->params['cod']);
                if (!$captchaCodCheck) {
                    $_SESSION['captchaCheckErrorMassage'] = "Попробуйте ввести код ещё раз!";
                    $_SESSION['captchaMainErrorMassage'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>Неверно введён код с картинки</p>
                                        <p>Мы обновили код, попробуйте ввести его ещё раз</p>
                                    </div>
                                    <br/>
                                </div>
                    ";
                    $_SESSION['registration_form_data']['name'] = $this->params['name'];
                    $_SESSION['registration_form_data']['patronymic'] = $this->params['patronymic'];
                    $_SESSION['registration_form_data']['lastname'] = $this->params['lastname'];
                    
                    header('location:/Registration');
                    exit();
                }

                new Registr($this->params);//регистрируем пользователя (пишем данные в БД и авторизуем)
            break;

            //если из формы входа
            case "log_in":
            
            $authorization = new Authoriz($this->params);//авторизуем пользователя (пишем данные в БД и авторизуем)
            //var_dump($authorization); pass_restore
            break;
            
            //если ввели Email для восстановления пароля
            case "pass_restore":

            Core::app()->setLog(__METHOD__."[".__LINE__."] попытка восстановления пароля ... ".$this->action);
            $passRestore = new PassRestore();//регистрируем пользователя (пишем данные в БД и авторизуем)
            $passRestore->checkAndSend($this->params);
            //var_dump($authorization); pass_restore
            break;
            
            //если ввели пользователь сохраняет изменения в личном кабинете
            case "user_settings_update":
            //проверка на соответствие прав
            if ( !Core::app()->accessCheck('Admin,Суперчеловек ;),SuperUser,Moderator,User') ) {
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };
            //проверяем есть ли директория для сохранения аватарки. Если нет создаём её.
            $FileProcess = new FileProcess;
            $check_and_create_dir = $FileProcess->check_and_create_dir("assets/media/images/{$this->params['table_name']}/{$this->params['id']}");
            if ($check_and_create_dir) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] директория для записи аватарки - существует... ".$this->action);
            } else {
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>Ошибка записи аватарки пользователя. 
                Директория отсутствует или ошибка при её создании. ".$this->action."</span>");
            }
            //путь для сохранения аватарки
            //var_dump($this->params);die;
            $save_path = "assets/media/images/".$this->params['table_name']."/".$this->params['id']."/ava.jpg";
            $IMGProcess = new IMGProcess;
            $IMGProcess->cut_and_save_img_mss(200,1,$save_path);
            header('location:'.$this->params["back_url"].'');
            exit();
            break;
            
            //приём данных из формы обратной связи
            case "dispatch_massage":
            
            //данные, введённые пользователем для вставки в поля формы в случае ошибки
            $_SESSION['contactFormClient_name'] = trim($this->params['client_name']);
            $_SESSION['contactFormEmail'] = trim($this->params['email']);
            $_SESSION['contactFormUser_massage'] = trim($this->params['user_massage']);

            //проверка правильности ввода кода с картинки
            $Captcha = new Captcha;
            $captchaCodCheck = $Captcha->captchaCodCheck($this->params['cod']);
            if (!$captchaCodCheck) {
                $_SESSION['captchaCheckErrorMassage'] = "Попробуйте ввести код ещё раз!";
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>Неверно введён код с картинки</p>
                                        <p>Мы обновили код, попробуйте ввести его ещё раз</p>
                                    </div>
                                    <br/>
                                </div>
            ";
                header('location:/contacts');
                exit();
            }

            Core::app()->setLog(__METHOD__."[".__LINE__."] попытка отправки сообщения с сайта... ".$this->action);
            $SendMail = new SendMail;
            $timeProcess = new TimeProcess;
            $sitePath = Core::app()->config->site_path;
            $email_developer = Core::app()->config->email_developer;
            $email_admin = Core::app()->config->email_admin;
            $massage_date = $timeProcess->dateFromTimestamp( time() );
            $array = array("developer" => $email_developer, "admin" => $email_admin); //кому отправляем
                //var_dump($array);die;
            $subject = "Сообщение пользователя сайта ".$sitePath." от ".$massage_date.""; //тема сообщения
            $text = "
                <html>
            		<head>
                        <meta charset='windows-1251' />
                        <meta http-equiv='Content-Type' content='text/html; charset=windows-1251' />
            		</head>
                    <body>
                		<table>
                		<center>
                		<h4>Здравствуйте!</h4>
                		</center>
                		<tr>
                			<td>
                                <p>".$massage_date." с сайта ".$sitePath.", поступило сообщение <br>от пользователя: <b>".$this->params['client_name']."</b></p>
                                <p>E-mail пользователя: <b>".$this->params['email']."</b></p>
                                <p>Текст сообщения:</p>
                                <p><i>".$this->params['user_massage']."</i></p>
                                <span style='color:#333'><a href='".$sitePath."'>Перейти на сайт</a></span>
                			</td>
                		 </tr>
                         
                		 <tr>
                			<td>
                                <i><span style='margin-left:300px'>Удачи!</span></i><br>
                                <i><span style='margin-left:300px'>С уважением, $sitePath</span></i>
                                <i><span style='margin-left:300px'>г.Донецк</span></i>
                            </td>
                		 </tr>
                		 <tr>
                			<td><span style='color:red'> P.S. Письмо отправлено автоматически <br> если это письмо попало к вам по ошибке - просто удалите его</span></td>
                		 </tr>
                		</table>
            		</body>
          		</html>
            "; //полный текст сообщения (с тегами)
            
            //если пользователь отправляет пустое сообщение
            if (trim($this->params['user_massage']) == "") {
                $_SESSION['massageCheckErrorMassage'] = "Нельзя отправлять пустое сообщение!";
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>Ошибка!</p>
                                        <p>Вы пытаетесь отправить пустое сообщение!</p>
                                    </div>
                                    <br/>
                                </div>
            ";
                header('location:/contacts');
                exit();
            }
            //проверяем на корректность имэил:
            $emailCheck = $SendMail->emailCheck($this->params['email']);
            if ($emailCheck === true) {
                //var_dump($array);die;
                $x = $SendMail->sendToMail($array, $from = false, $subject, $text);//отправка письма
            } else {

                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>ошибка: введён некорректный email 
                    (".$this->params['email'].")! ".$this->action."</span>");

                $_SESSION['emailCheckErrorMassage'] = "Проверьте правильность введённого Вами адреса электронной почты <i>{$this->params['email']}</i>";
                header('location:/contacts');
                exit();
            }
            //формируем сообщение об отправке пользователю
            if ($x) {
                //данные, введённые пользователем для вставки в поля формы в случае ошибки
                unset($_SESSION['contactFormClient_name']);
                unset($_SESSION['contactFormEmail']);
                unset($_SESSION['contactFormUser_massage']);
                
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-success' role='alert'>
                                        <p><span class='glyphicon glyphicon-ok-circle'></span> Ваше сообщение успешно отправлено!</p>
                                        <p>Наш ответ будет выслан на указанный Вами e-mail:</p>
                                        <p><b>{$this->params['email']}</b>.</p>
                                        <p>С уважением, администрация $sitePath!</p>
                                    </div>
                                    <br/>
                                </div>
            ";
            } else {
                $_SESSION['sendMailReport'] = "
                                <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center; font-size:12px;'>
                                    <br/>
                                    <div class='alert alert-danger' role='alert'>
                                        <p>К сожалению письмо отправить не удалось!</p>
                                        <p>Мы уже работаем над исправлением данной ошибки.</p>
                                        <p>Извините за неудобства.</p>
                                        <p>С уважением, администрация $sitePath!</p>
                                    </div>
                                    <br/>
                                </div>
            ";
            Core::app()->setLog(__METHOD__."[".__LINE__."] 
            <span id='debugErrMsg'>Пользователю не удалось отправить сообщение с сайта 
                (".$this->params['email'].")!</span>");
            }
            
            header('location:/contacts');
            exit();
            break;
            
            
            //если добавили комментарий
            case "gbook_add_comment":

            Core::app()->setLog(__METHOD__."[".__LINE__."] добавляю комментарий ... ");
            $gBook = new Gbook();//подключаем модуль гостевой книги
            $gBook->catchFormData($this->params);
            //$passRestore->checkAndSend($this->params);
            //var_dump($authorization); pass_restore
            break;
            
            //универсальное обновление данных в БД
            case "update":
            Core::app()->setLog(__METHOD__."[".__LINE__."] попытка обновления данных... ");

            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            if ( !Core::app()->accessCheck('Admin,Суперчеловек ;),Moderator') ) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'>Не достаточно прав для выполнения операции \"update\"!</span>");

                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };

            //универсальное свойство обновления данных
            $update = Core::app()->DBProcess->universalUpdateDB($this->params['table_name'],$this->params['id'],$this->params);

            //обновляем картинку
            $FileProcess = new FileProcess;
            //ВНИМАНИЕ !!! Не забываем в $FileProcess->save_uploaded_files добавлять case для новой таблицы !!!!!!!!!!!
            $upload_files = $FileProcess->save_uploaded_files($this->params['table_name'], $this->params['id']);
            //возврат на исходную страницу
            header('location:'.$this->params["back_url"].'');
            exit();
            
            //если добавили
            case "add":
            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            Core::app()->setLog(__METHOD__."[".__LINE__."] попытка добавления данных ... ");

            if ( !Core::app()->accessCheck('Admin,Суперчеловек ;),Moderator') ) {
                header('location:/AccessDenied'); //если отказано в доступе - отправляем на страницу с сообщением
                exit();
            };
            //универсальное свойство добавления данных
            //$add = 40; // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
            $add = Core::app()->DBProcess->universalInsertDB($this->params['table_name'],$this->params);

            if ($add) {
                $FileProcess = new FileProcess;
                //не забываем в $FileProcess->save_uploaded_files добавлять case для новой таблицы !!!!!!!!!!!!!!!
                $upload_files = $FileProcess->save_uploaded_files($this->params['table_name'], $add);
                //возврат на исходную страницу
                header('location:'.$this->params["back_url"].'');
                exit();
            }
            Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>ошибка добавления данных!</span>");
            header('location:'.$this->params["back_url"].'');
            exit();
            break;
            
            //если удаляют
            case "del":
            
            //для пользователей, имеющих права переданные в accessCheck - доступ будет открыт, для остальных закрыт!
            Core::app()->setLog(__METHOD__."[".__LINE__."] попытка удаления данных ...");

            if (!Core::app()->accessCheck('Admin')) {
                Core::app()->setLog(__METHOD__."[".__LINE__."] 
                    <span id='debugErrMsg'> Не достаточно прав для выполнения текущей операции</span>");
                header('location:/AccessDenied'); //отправляем на страницу с сообщением
                exit();
            };
            //var_dump($this->params['id']);die;
            if ((int)$this->params['id'] > 0) {
                //получаем и готовим переданные пути удаления файлов
                if ($this->params['file_path']) {
                    $file_path = base64_decode ($this->params['file_path']);
                    $file_path = unserialize($file_path);
                    Core::app()->setLog(__METHOD__."[".__LINE__."] получены пути удаляемых файлов ".$file_path."", "n");
                } else {
                    $file_path = false;
                }
                //получаем и готовим переданные пути удаления директорий
                if ($this->params['dir_path']) {
                    $dir_path = base64_decode ($this->params['dir_path']);
                    $dir_path = unserialize($dir_path);
                    Core::app()->setLog(__METHOD__."[".__LINE__."] получены пути удаляемых директорий ".$file_path."", "n");
                } else {
                    $dir_path = false;
                }

                //универсальное свойство удаления данных
                $del = Core::app()->DBProcess->dropDataToID($this->params['id'],$this->params['table_name'],$file_path,$dir_path);

                if ($del == true) {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugSuccessMsg'>данные успешно удалены, 
                    удаляю связанные комментарии...".$file_path."", "</span>");

                    //удаляем связанные комментарии
                    $MsGbook = new Gbook;
                    $MsGbook->dropDataToSorceID($this->params['id'],$this->params['table_name']);

                    header('location:'.$this->params["back_url"].'');
                    exit(); 
                } else {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        <span id='debugErrMsg'>Возможны проблемы с удалением данных!</span>");
                }
            }
            break;

            //удаление файла:
            case "delete_file":
                $FileProcess = new FileProcess;
                $x = $FileProcess->delete_file($this->params['file_path']);
                //echo $x;die;
                if ($x) {
                    header('location:'.$_SERVER['HTTP_REFERER'].'');
                    exit(); 
                } else {
                    die("Ошибка удаления файла");
                }
            break;
            
            //подтверждение заказа
            case "order_confirm":
                Core::app()->setLog(__METHOD__."[".__LINE__."] попытка записи заказа пользователя ...");
                $MsOrderProcess = new OrderProcess;
                $x = $MsOrderProcess->orderInsertToDB($this->params);

                if(!$x){
                    Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        <span id='debugErrMsg'>Ошибка записи заказа пользователя!</span>");
                } else {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] 
                        <span id='debugSuccessMsg'>Заказ успешно записан в БД!</span>");
                }

                header('location:'.$this->params["back_url"].'');
                exit();
            break;
            
            //перемещение заказа в "отменённые"
            case "updateorder":
                Core::app()->setLog(__METHOD__."[".__LINE__."] попытка обновления данных заказа пользователя ...", "n");
                //обновляем данные в таблице "заказов"
                $x = Core::app()->DBProcess->universalUpdateDB('orders', $this->params['id_for_update'], $this->params);
                //$x = $MsOrderProcess->universalInsertDB('aborted_order',$this->params);//универсальный метод добавления данных в БД($this->params);
                if (!$x) {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>Ошибка обновления данных заказа!</span>");
                } else {
                    Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugSuccessMsg'>Данные заказа успешно обновлены!</span>");
                }
                //инициализация сообщения о выполненном с заказом действии
                //$x = true или false
                $MsOrderProcess = new OrderProcess();
                $MsOrderProcess->orderInitMassage($this->params['status'], $x);

                header('location:'.$this->params["back_url"].'');
                exit();
            break;
            
            //если неудовлетворительное кол-во параметров - выводим сообщение об ошибке
            default:
                Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugErrMsg'>Неверное количество
             параметров в запросе или неизвестное действие! Дальнейшая работа парсера невозможна.!</span>");
            die("<span id='debugErrMsg'>Ошибка обработки запроса...</span>");
        }
    }
    
    private function getAction($params) {
        $a = array_keys($params); //извлекаем ключи массива запроса в отдельный массив
        $this->action = strtolower(array_pop($a));//action является имя последнего ключа массива (имя кнопки формы) присваиваем свойству action - значение
        array_pop($this->params); //удаляем последний элемент массива запроса т.к. он уже не понадобится (action - извлечён)

        Core::app()->setLog(__METHOD__."[".__LINE__."] <span id='debugSuccessMsg'>определено действие: ".$this->action."<span>");

        return $this->action;
    }
    
    private function filterPost($data) {
        $array = array();
        foreach ($data as $key => $value) {
            $value = Core::app()->checkStr($value);
            $array[$key] = $value;
        }
        //unset($_POST);// уже не понадобится
        return $array;
    }
}
?>