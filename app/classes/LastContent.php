<?php
// Последние введённые данные в БД
class LastContent
{
    private $pdo;
    public $table_name = false;
    public $quantity = 3; // количество выводимых строк
    public $order_by = "id";
    public $asc_desc = "DESC";
    public $last_content_data; // извлечённые данные


    function __construct() {
        //parent:: __construct();
        //получаем метку соединения
        Core::app()->setLog(__METHOD__ . "[" . __LINE__ . "] получаю метку соединения ...", "n");
        $this->pdo = Core::app()->DBase;
    }

     //метод для получения данных для вывода страниц с перечнием тезисов  $sql = "SELECT * FROM `$tableName` ORDER by $order_by $asc_desc LIMIT $start, $num "; LIMIT $this->quantity
    public function get_data() {
        if ($this->table_name === false) {
            die(__METHOD__ . "[" . __LINE__ . "] не задано имя таблицы");
        }

        $sql = "SELECT * FROM `$this->table_name` ORDER by $this->order_by $this->asc_desc LIMIT $this->quantity";

        try {
            $stmt = $this->pdo->prepare($sql);
            //$stmt->bindParam(':id', $id, PDO::PARAM_INT); //PDO::PARAM_STR
            $stmt->execute();
            //$stmt->execute([':id' => $id]); # No need to escape it!
            $selectedData = $stmt->fetchAll();

            $_SESSION['db_query_num']++;
            $_SESSION['db_query_from'][] = __METHOD__." | ".$sql." | затронуто: ".$stmt->rowCount()." строк";
            
            return $this->last_content_data = $selectedData;

        } catch (PDOException $e) {
            $_SESSION['db_query_errors'][] = __METHOD__." | ".$e->getMessage()." | SQL: ".$sql;
            return false;
        }
    }
}
?>