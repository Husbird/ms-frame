<?php
//переработано
/**
 * DBConnect
 *
 * Соединение с базой данных
 *
 * Created by PhpStorm.
 * Author: Moskaleny
 * Date: 05.04.2017
 * Time: 20:14
 */

class DBConnect {

    /**
     * Константа
     *
     * кодировка БД
     *
     * @const string
     */
    const DB_CHARSET = 'utf8';

    /**
     * Свойство $mysqli
     *
     * метка соединения с базой данных
     *
     * @var object
     */
    public static $pdo = null;

    /**
     * Свойство $_instance
     *
     * @var object экземпляр класса DBConnect
     */
    protected static $_instance;

    /**
     * Метод класса "Singleton"
     * Создаёт экземпляр класса DBConnect если он ещё не создан
     * или возвращает ранее созданный экземпляр
     *
     * @return object
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self;
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                СОЗДАЮ и возвращаю экземпляр класса DBConnect: ");
        }
        //Core::app()->LogWriter->setLog(__METHOD__." возвращаю ИМЕЮЩИЙСЯ экземпляр класса DBConnect: ", "n");
        return self::$_instance;
    }

    /**
     * Конструктор класса
     * Выполняет подключение к базе данных
     * и инициализирует свойство $mysqli
     *
     * @return boolean
     */
    private function __construct() {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
            соединение с БД: [" . Core::app()->config->db_name . "] хост: [" . Core::app()->config->db_host . "]");

        try {
            $pdo = new PDO('mysql:host='.Core::app()->config->db_host.';dbname='.Core::app()->config->db_name.
                    ';charset='.self::DB_CHARSET.'',Core::app()->config->db_user, Core::app()->config->db_pass);

            $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

            $_SESSION['db_conn_num']++; //увеличиваем счётчик соединений с БД

            self::$pdo = $pdo;
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] соединение успешно установлено!");

            return true;

        } catch (PDOException $e) {
            Core::app()->setLog(__METHOD__ ."[".__LINE__."] 
                <span style='color:red;'>Ошибка подключения базе данных:</span> [" . $e->getMessage() . "]");
            //echo $e->getMessage();
            die("<p>ВЕДУТЬСЯ РАБОТЫ НА СЕРВЕРЕ, ПОПРОБУЙТЕ ПРИЙТИ ЧЕРЕЗ 5 МИНУТ ...</p>");
        }
    }

    /**
     * Метод класса
     *
     * Возвращает оъект соединения, доступно в любом месте кода
     *
     * @return object
     */

    public static function getPDO() {
        //$debug = debug_backtrace();
        // возвращаем объект
        return self::$pdo;
    }

    /**
     * Метод __clone()
     *
     * Запрещаем использование магического метода __clone()
     *
     */
    private function __clone() {}
}
?>