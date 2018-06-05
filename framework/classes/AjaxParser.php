<?php
// Переработано ++
/**
 * Created by PhpStorm.
 * User: MS-projects
 * Date: 31.03.2017
 * Time: 23:29
 */
class AjaxParser {

    private $receivedData = null; //принятые данные
    private $receivedDataSafety = null; //принятые данные после обработки (mysqli_real_escape_string)
    private $decodeData = null; //десериализованные принятые данные
    private $dataSource = null; //источник данных
    private $pdo = null; //метка соединения с БД

    function __construct($json) {
        Core::app()->setLog(__METHOD__ ."[".__LINE__."] Разбор пакета JSON ...");
        $this->receivedData = $json;
        // Разбор пакета JSON
        $this->decodeData = json_decode($json);
        //устанавливаем источник данных
        $this->dataSource = $this->decodeData->source;
        //если источник данных передан
        if ($this->dataSource) {
            $this->pdo = Core::app()->DBase; //получаем метку соединения

            $this->receivedDataSafety = Core::app()->checkStr($this->receivedData);//проверка на...
            $this->actionManager(); //вызываем обработчик действий
        }
    }
    //обработчик действий (в зависимости от источника переданных данных)
    function actionManager() {
        switch ($this->dataSource) {
            case "FormTrainOrder":

                $sql = "INSERT INTO `prog_form_info` (person_email, person_info)
                                      VALUES (:person_email, :person_info)";
                try {
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->execute(array(
                        ':person_email' => $this->decodeData->email,
                        ':person_info' => $this->receivedDataSafety
                    ));

                    $_SESSION['db_query_num']++;
                    $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

                    include_once("app/views/massages/_formSendOk.html"); //собщение об успехе

                } catch(PDOException $e) {
                    $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
                        Core::app()->setLog(__METHOD__."[".__LINE__."] Данные не занесены в БД!");

                    include_once("app/views/massages/_formSendError.html"); //собщение об ошибке
                    return false;
                }

                break;

            default:
                Core::app()->LogWriter->setLog(__METHOD__ ."[".__LINE__."] действие определить не удалось!", "e");
                echo "Ошибка: действие определить не удалось";
                die();
        }
    }
}
?>