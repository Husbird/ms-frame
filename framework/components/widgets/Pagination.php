<?php
/**
* Виджет: постраничная навигация*/
class Pagination
{

 
    function __construct(){
        //$this->mysqli = MsDBConnect::getInstance()->getMysqli();

    }
    
    //выводим блок с сылками страниц
    public static function run($pagesArray = false){
        if(is_array($pagesArray)){
             //навигация страниц
            echo '
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 ms_pagination">
                        <div class="pagination">'
                        .$pagesArray[0].$pagesArray[1].$pagesArray[2].$pagesArray[3].$pagesArray[4].$pagesArray[5].$pagesArray[6].
                        $pagesArray[7].$pagesArray[8].$pagesArray[9].$pagesArray[10].$pagesArray[11].$pagesArray[12].$pagesArray[13].
                        '
                        </div>
                    </div>
                </div>
            </div>';  
        }else{
            echo '<b>'.__METHOD__.'</b> Ошибка! Не удалось получить необходимые параметры!<br>';
        }
    }
}

?>