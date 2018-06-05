<?php
/**
 * Core
 *
 * Ядро приложения
 *
 * Created by PhpStorm.
 * Author: Moskaleny
 * Date: 09.04.2017
 * Time: 13:52
 */
class Core {

    /**
     * Свойство $instance
     * экземпляр класса Core
     *
     * @var object
     */
    private static $instance = null;

    /**
     * Метод "Singleton"
     * Создаёт экземпляр класса Core если он ещё не создан
     * или возвращает ранее созданный экземпляр
     *
     * @return object
     */
    public static function app() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;  // возвращаем экземпляр объекта MSS
    }

    /**
     * Свойство $config
     * настройки приложения из (app/config/main.php)
     *
     * @var array
     */
    public $config;

    /**
     * Свойство $requestArgs
     *
     * результат обработки запроса парсером
     *
     * @var array
     */
    public static $requestArgs = null;

    /**
     * Свойство $user_role
     *
     * псевдоним категории прав пользователей
     * (например: "Guest" это псевдоним от "-1" )
     *
     * @var string
     */
    public static $user_role = "Guest";

    /**
     * Свойство $root
     *
     * категория прав пользователя
     *
     * @var integer
     */
    public static $root = -1;

    /**
     * Свойство $userData
     *
     * все данные инициализированного пользователя
     * в виде ассоциативного массива
     * ПРИМЕР исп.: Core::$userData['name'];
     *
     * @var array
     */
    public static $userData = false;

    public static $modelData = null;// данные текущей модели

    /**
     * Свойство $userDevice
     *
     * данные об устройстве пользователя
     *
     * @var string
     */
    public static $userDevice = null;

    /**
     * Свойство $userOS
     *
     * данные об операционной системе пользователя
     *
     * @var string
     */
    public static $userOS = null;

    /**
     * Свойство $userIp
     *
     * ip адрес текушего пользователя
     *
     * @var string
     */
    public static $userIp = null;

    /**
     * Свойство $DBase
     *
     * метка соединения с базой данных
     *
     * @var object
     */
    public $DBase = null;

    /**
     * Свойство $DBProcess
     *
     * Объект для работы с базой данных
     *
     * @var object
     */
    public $DBProcess = null;

    private function __clone() {}  // запрещаем использование магических методов
    
    private function __construct() {
        //присваиваем свойству $config массив настроек из нашего файла настроек.
        $this->config = (object)require_once("app/config/main.php");
    }

    //добавление нового свойства
    private function setProperty($propName, $propValue) {
        $this->$propName = $propValue;
    }

    //Добавление расширения
    public function __setExtension($propName, $extensionObject) {
        Core::app()->setLog(__METHOD__."[".__LINE__."] подключаю расширение: (".$propName.")");
        $this->setProperty($propName, $extensionObject);
    }

    //устанавливаем права:
    public static function setRole($root) {
        switch ($root) {
            case 0:
            self::$user_role = 'User';//User - псевдоним категории прав пользователей "0"
            break;
            
            case 1:
            self::$user_role = 'Moderator';//Moderator - псевдоним категории прав пользователей "1"
            break;
            
            case 2:
            self::$user_role = 'SuperUser';//SuperUser - псевдоним прав категории пользователей "2"
            break;
            
            case 3:
            self::$user_role = 'Суперчеловек ;)';//Moderator - псевдоним прав категории пользователей "3"
            break;
            
            case 4:
            self::$user_role = 'Admin';//Admin - псевдоним категории прав пользователей "4"
            break;
            
            default:
            self::$user_role = 'Guest';//Guest - псевдоним категории прав пользователей "-1"
            break;
        }

        Core::app()->setLog(__METHOD__."[".__LINE__."] установлены права (".self::$user_role.")");
    }

    //Запись массива с данными пользователя в свойство
    public static function setUserData($data = false) {
        if (is_array($data)) {
            self::$userData = $data;
            Core::app()->setLog(__METHOD__."[".__LINE__."] данные пользователя успешно помещены в 
                свойство Core::\$userData ...");

            return true;
        } else {
            Core::app()->setLog(__METHOD__."[".__LINE__."] ошибка в полученных аргументах (не массив)");

            return false;
        }
    }
    
    //проверка на соответствие правам. Передаётся параметр, в котором указаны категории прав допуск для которых открыть
    //далее метод сравнивает указанные категории с текущей категорией и запускает соответствующие сценарии
    public static function accessCheck($accessRole) {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] проверяю права доступа пользователя ...");
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
            доступ разрешен пользователям с правами: (".$accessRole.")");
        $array = explode(',',$accessRole);

        $i = 0;
        foreach ($array as $value)  {
             //var_dump($value);die;
            if ($value == self::$user_role) {
                Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                    совпадение с текущими правами: (".self::$user_role.")");
                $i++;
            }
        }
        if ($i > 0) {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] доступ РАЗРЕШЕН!");
            
            return true;
        } else {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                совпадений с текущими правами (".self::$user_role.") не найдено!");
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] доступ ЗАПРЕЩЁН!");
            
            return false;
        }
    }
    
    //получение ip пользователя
    public static function getRealIp() {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] получаю реальный текущий ip ...");
        $ip = $_SERVER['REMOTE_ADDR'];
        $ipFrom = '$_SERVER[REMOTE_ADDR]';
        //var_dump($_SERVER['HTTP_FORWARDED']);die;
        if ($_SERVER['HTTP_FORWARDED'] !== NULL) {
            $ip2 = substr($_SERVER['HTTP_FORWARDED'],4);//вырезаем "for="
        } else {
            $ip2 = 'не определён';
        }
        $ipFrom2 = '$_SERVER[HTTP_FORWARDED]';

        $device = $_SERVER['HTTP_USER_AGENT'];
        $deviceIdRequest = '$_SERVER[HTTP_USER_AGENT]';
        self::$userDevice = $device;
         
        //проверка для компенсации бага :
        //при авторизации и моб телефона в классе MsAuthoriz определяется правильный текущий ip средством $_SERVER[REMOTE_ADDR]
        //при этом HTTP_FORWARDED - пуст.
        //после установки кук и редиректе на главную страницу в классе MsCheckRole текущий ip определённый средством $_SERVER[REMOTE_ADDR]
        //уже не совпадает с предидущим результатом определения тем же средством (в MsAuthoriz) (записанным в куке), но
        //при этом HTTP_FORWARDED уже не пуст и в нём появляется "правильное" значение ip которое до редиректа было определено в
        // классе MsAuthoriz средством $_SERVER[REMOTE_ADDR], поэтому - костыль: Если определили что Android - делаем подмену ip для
        //корректной работы MsCheckRole:
        //если используют гаджет с Аndroid и определён $_SERVER['HTTP_FORWARDED']
        if ( stristr($device, 'Android') ) {
            if ($_SERVER['HTTP_FORWARDED']) {
                Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                    пришли с устройства Android, HTTP_FORWARDED установлен, меняю ip: (".$ip.") из (".$ipFrom.") на 
                    ip (".$ip2.") из (".$ipFrom2.")");

                $ip = $ip2;
                //self::$userOS = 'Android';//присваиваем данные об операционной системе пользователя свойству  $userOS
            }
        }

        Core::app()->setLog(__METHOD__ ."[".__LINE__."] получен IP: (".$ip.") из (".$ipFrom.")");
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] получен IP2: (".$ip2.") из (".$ipFrom2.")");
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] данные об устройстве пользователя:
            (".$device.") из (".$deviceIdRequest.")");

         self::get_all_ip();//получаем все возможные ip
         self::$userIp = $ip;

         return $ip;
        
    }
    
    //получаем все возможные ip
    private static function get_all_ip() {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] получаю все возможные ip из заголовков HTTP...");

        $ip_pattern="#(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)#";
        $ret="";
        foreach ($_SERVER as $k => $v) {
            if (substr($k,0,5)=="HTTP_" AND preg_match($ip_pattern,$v)) $ret.=$k.": ".$v."\n";
        }

        Core::app()->setLog(__METHOD__ ."[".__LINE__."] полученные ip: (".$ret.")");

        return $ret;
    }

    //сведения об операционной системе пользователя
    public static function get_OS(){
        Core::app()->setLog(__METHOD__."[".__LINE__."] получаю сведения об ОС пользователя ...");

        $userAgent = $_SERVER["HTTP_USER_AGENT"];
        $oses = array (
                    // Mircrosoft Windows Operating Systems
                    'Windows 3.11' => '(Win16)',
                    'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
                    'Windows 98' => '(Windows 98)|(Win98)',
                    'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
                    'Windows 2000 Service Pack 1' => '(Windows NT 5.01)',
                    'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
                    'Windows Server 2003' => '(Windows NT 5.2)',
                    'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
                    'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
                    'Windows 8' => '(Windows NT 6.2)|(Windows 8)',
                    'Windows 10 x64' => '(Windows NT 10.0; WOW64)',
                    'Windows 10' => '(Windows NT 10)',
                    'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
                    'Windows ME' => '(Windows ME)|(Windows 98; Win 9x 4.90 )',
                    'Windows CE' => '(Windows CE)',
                    'Windows Phone 8.1' => '(Windows Phone 8.1)',
                    'Windows v.xxx' => '(Windows)',


                    // Mobile Devices
                    'Android 4.4.2' => '(Android 4.4.2)',
                    'Android' => '(Android)',
                    'iPod' => '(iPod)',
                    'iPhone' => '(iPhone)',
                    'iPad' => '(iPad)',

                    // UNIX Like Operating Systems
                    'Mac OS X Kodiak (beta)' => '(Mac OS X beta)',
                    'Mac OS X Cheetah' => '(Mac OS X 10.0)',
                    'Mac OS X Puma' => '(Mac OS X 10.1)',
                    'Mac OS X Jaguar' => '(Mac OS X 10.2)',
                    'Mac OS X Panther' => '(Mac OS X 10.3)',
                    'Mac OS X Tiger' => '(Mac OS X 10.4)',
                    'Mac OS X Leopard' => '(Mac OS X 10.5)',
                    'Mac OS X Snow Leopard' => '(Mac OS X 10.6)',
                    'Mac OS X Lion' => '(Mac OS X 10.7)',
                    'Mac OS X' => '(Mac OS X)',
                    'Mac OS' => '(Mac_PowerPC)|(PowerPC)|(Macintosh)',
                    'Open BSD' => '(OpenBSD)',
                    'SunOS' => '(SunOS)',
                    'Solaris 11' => '(Solaris/11)|(Solaris11)',
                    'Solaris 10' => '((Solaris/10)|(Solaris10))',
                    'Solaris 9' => '((Solaris/9)|(Solaris9))',
                    'CentOS' => '(CentOS)',
                    'QNX' => '(QNX)',

                    // Kernels
                    'UNIX' => '(UNIX)',

                    // Linux Operating Systems
                    'Ubuntu 12.10' => '(Ubuntu/12.10)|(Ubuntu 12.10)',
                    'Ubuntu 12.04 LTS' => '(Ubuntu/12.04)|(Ubuntu 12.04)',
                    'Ubuntu 11.10' => '(Ubuntu/11.10)|(Ubuntu 11.10)',
                    'Ubuntu 11.04' => '(Ubuntu/11.04)|(Ubuntu 11.04)',
                    'Ubuntu 10.10' => '(Ubuntu/10.10)|(Ubuntu 10.10)',
                    'Ubuntu 10.04 LTS' => '(Ubuntu/10.04)|(Ubuntu 10.04)',
                    'Ubuntu 9.10' => '(Ubuntu/9.10)|(Ubuntu 9.10)',
                    'Ubuntu 9.04' => '(Ubuntu/9.04)|(Ubuntu 9.04)',
                    'Ubuntu 8.10' => '(Ubuntu/8.10)|(Ubuntu 8.10)',
                    'Ubuntu 8.04 LTS' => '(Ubuntu/8.04)|(Ubuntu 8.04)',
                    'Ubuntu 6.06 LTS' => '(Ubuntu/6.06)|(Ubuntu 6.06)',
                    'Red Hat Linux' => '(Red Hat)',
                    'Red Hat Enterprise Linux' => '(Red Hat Enterprise)',
                    'Fedora 17' => '(Fedora/17)|(Fedora 17)',
                    'Fedora 16' => '(Fedora/16)|(Fedora 16)',
                    'Fedora 15' => '(Fedora/15)|(Fedora 15)',
                    'Fedora 14' => '(Fedora/14)|(Fedora 14)',
                    'Chromium OS' => '(ChromiumOS)',
                    'Google Chrome OS' => '(ChromeOS)',
                    // Kernel
                    'Linux' => '(Linux)|(X11)',
                    // BSD Operating Systems
                    'OpenBSD' => '(OpenBSD)',
                    'FreeBSD' => '(FreeBSD)',
                    'NetBSD' => '(NetBSD)',

                    //DEC Operating Systems
                    'OS/8' => '(OS/8)|(OS8)',
                    'Older DEC OS' => '(DEC)|(RSTS)|(RSTS/E)',
                    'WPS-8' => '(WPS-8)|(WPS8)',
                    // BeOS Like Operating Systems
                    'BeOS' => '(BeOS)|(BeOS r5)',
                    'BeIA' => '(BeIA)',
                    // OS/2 Operating Systems
                    'OS/2 2.0' => '(OS/220)|(OS/2 2.0)',
                    'OS/2' => '(OS/2)|(OS2)',
                    // Search engines
                    'Search engine or robot' => '(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp)|(msnbot)|(Ask Jeeves/Teoma)|(ia_archiver)'
                        );

        foreach ($oses as $os => $pattern) {
            //echo $pattern." ";
            if ( preg_match("/$pattern/i", $userAgent) ) {
                Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                    ОС пользователя: ".$os." пишу в self::\$userOS");
                //пишу в свойство
                self::$userOS = $os;

                return $os;
            }
        }
        Core::app()->setLog(__METHOD__."[".__LINE__."] 
            Операционная система НЕ определена! АДМИН расширь базу устройств!");
        self::$userOS = 'Unknown';

        return 'Unknown';
    }

    public static function setLog($note) {
        if (Core::app()->config->session_log === true) {
            $_SESSION['app_log'][] = $note;
            return true;
        }
    }

    //обработка числа перед записью в БД
    public static function clearInt($number) {
        return intval($number);
    }

    //обработка строковых данных
    public static function checkStr($string) {
        $string = strip_tags(trim($string));
        $string = htmlspecialchars($string);
        return ($string);
    }
}
?>