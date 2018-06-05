<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.04.2017
 * Time: 20:14
 */
//переработано+ //$_SESSION['mss_monitor'][] = 'Удаляю cookie...<br>'; ЗАПИЛИТЬ логов запись в сессию
//Запись в журнал

class LogWriter {

    protected $log_write_config = null; //true/false - активно/неактивно (из config файла приложения)
    protected $log_string = null; //текст записи
    protected $time = null; //время записи
    protected $note_type = null; //тип записи: "n" - обычная запись; "e" - запись ошибки
    protected $file_path = 'framework/log/log.txt';
    protected $counter = 0;
    protected $timeStart = null; //метка начала работы скрипта
    protected $timeLast = null; //метка предидущего вызова

    function __construct() {

        $this->log_write_config = Core::app()->config->log_files_write;
        $this->logClear(); //чистим лог файл
        $this->timeStart = time(); //устанавливаем время начала работы скрипта
    }

    public function setLog($logString, $noteType) {
        if ($this->log_write_config === true) {
            $this->time = date('m.d.y|H:i:s');
            $this->log_string = trim($logString);
            $this->note_type = $noteType;
            $this->writeLog();
        }
    }

    //пишем данные в лог фаил
    private function writeLog() {
        if ($this->log_write_config) {
            $this->counter++; //увеличиваем счётчик вызовов метода

            $timer = $this->meteringSpeed();

            if ($this->note_type == "n") {
                $note = "[".$this->counter."][Note:][".$this->time."] ".$this->log_string.
                    "[".$timer["process_time"]."][".$timer["total_process_time"]."] \n";//символ \n писать только в двойных кавычках !!!
            } elseif ($this->note_type == "e") {
                $note = "[".$this->counter."][Error:][".$this->time."] ".$this->log_string.
                    "[".$timer["process_time"]."][".$timer["total_process_time"]."] \n";
            } else {
                $note = "[".$this->counter."][undefined:][".$this->time."] ".$this->log_string.
                    "[".$timer["process_time"]."][".$timer["total_process_time"]."] \n";;
            }
            //$log_content = file_get_contents($this->file_path); //получаем имеющееся содержимое
            //$resultNote = $note.$log_content; //добавляем к нему новую запись
            //$file_put = file_put_contents($this->file_path, $resultNote); //перезаписываем фаил
            $file_put = file_put_contents($this->file_path, $note, FILE_APPEND); //перезаписываем фаил

            return $file_put;
        }
    }

    //вычисление затраченного времени между вызовом writeLog()
    private function meteringSpeed() {
        $currentTime = time(); //текущее время

        if ($this->timeLast == null) {
            $this->timeLast = $currentTime;

            $deltaTime = $currentTime - $this->timeStart; //затраченное время между вызовом writeLog()
            //var_dump($deltaTime); die;
        } else {
            $deltaTime = $currentTime - $this->timeLast; //затраченное время между вызовом writeLog()
            $this->timeLast = $currentTime; //обновляем время последнего вызова на текущее время
        }
        $totalProcessTime = $currentTime - $this->timeStart;

        return array("process_time" => $deltaTime, "total_process_time" => $totalProcessTime);
    }

    //очистка файла записи логов
    private function logClear() {
        $time = date('m.d.y|H:i:s');
        $this->counter = 0;

        if (file_exists($this->file_path)) {
            unlink($this->file_path);
            $file_put = file_put_contents($this->file_path,
                "[".$this->counter."][".$time."][LogWriter_start!] \n", FILE_APPEND); //перезаписываем фаил
        } else {
            $file_put = file_put_contents($this->file_path,
                "[".$this->counter."][".$time."][LogWriter_start!] \n", FILE_APPEND); //перезаписываем фаил
        }
    }
}

?>