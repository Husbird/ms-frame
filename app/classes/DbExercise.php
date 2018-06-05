<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 18.04.2017
 * Time: 22:13
 */

class DbExercise
{
    private $pdo;

    /**
     *    function __construct(){
     *
     *     }
     */
    function __construct(){
        //parent:: __construct();
        //получаем метку соединения
        Core::app()->setLog(__METHOD__ . "[" . __LINE__ . "] получаю метку соединения ...", "n");
        $this->pdo = Core::app()->DBase; //получаем метку соединения
    }

    public function selectExMain($ex_part_id) {
        $ex_part_id = intval($ex_part_id);

        $sql = "SELECT t1.id, t1.ex_main_name, t1.ex_main_description, t2.ex_part_name, t2.ex_part_description, t2.id AS ex_part_id     
                    FROM `ex_main` t1 LEFT JOIN `ex_part` t2 ON t2.id = t1.ex_part_id WHERE t1.ex_part_id = $ex_part_id ORDER 
                        by t1.id ASC";

        Core::app()->setLog(__METHOD__."[".__LINE__."] выполняю SQL: ".$sql);

        try {
            $data = array();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

            return $data;

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
            return false;
        }
    }

    public function selectExercises($ex_main_id) {
        $ex_main_id = intval($ex_main_id);
        $sql = "SELECT * FROM `exercise`  WHERE ex_main_id = $ex_main_id ORDER BY id ASC";

        try {
            $data = array();
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql;

            return $data;

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage();
            return false;
        }
    }
}
?>