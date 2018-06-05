<?php
// Переработано ++
//корзина покупок
class Basket /*extends DBProcess*/ {
    
    /**
 * public $model_name;//имя модели
 *     public $action;//вызываемое действие
 *     public $params = array();
 */
    public $mysqli = null;
    public $url_back = false;
    
    function __construct($cookie = false) {
        $this->url_back = $_SERVER['HTTP_REFERER'];
        $this->mysqli = Core::app()->DBase; //получаем метку соединения
    }
    
    //добавляем товар в корзину (в сессию)
    //возвращает строку со значениями записанных в сессию id
    public function addToBasket($id_product) {
        $_SESSION['id_ms_product'] = $_SESSION['id_ms_product'].'|'.intval($id_product);
        return $_SESSION['id_ms_product'];
    }
    
    //удаляем товар из корзины
    //возвращает значение записанной в сессию строки с id товаров
    public function dellFromBasket($id_product) {
        //var_dump($_SESSION['id_ms_product']); die;
        $array = explode('|',$_SESSION['id_ms_product']);
        unset($array[array_search($id_product, $array)]); //array_search - находит соответствующий ключ в массиве, unset - удаляет по ключу
        array_shift($array); //удаляем появившийся первый пустой элемент массива
        
        if( (!empty($array)) ) { //если удалили НЕ последний продукт из корзины
            $string = implode('|',$array);
            $_SESSION['id_ms_product'] = "|".$string;//добавляем недостающий разделитель в начало строки и пишем в сессию
        } else {
            //если массив остался пуст (удалили последний товар)
            unset($_SESSION['id_ms_product']);
        }
        
        return $_SESSION['id_ms_product'];
        
    }
    
    public function echoBasket() {
        //если сессия не пуста - получаем массив с id
        if ($_SESSION['id_ms_product']) {
            $productIdArray = explode('|',$_SESSION['id_ms_product']);
            array_shift($productIdArray); //удаляем первы пустой элемент массива
        }
        
        if( (!empty($productIdArray)) ){ //AND ($productIdArray[0] !== '')
            $total = count($productIdArray);
            $word_product = $this->wordProductCorect($total);//корректируем слово "товаров" в зависимости от кол-ва
            $hfu = new Hfu;
print <<<HERE
<!-- Скрипт плавного открытия и закрытия блока -->
<script type="text/javascript"> 
    function diplay_hide (blockId){
        
        if ($(blockId).css('display') == 'none'){ 
                $(blockId).animate({height: 'show'}, 500); 
            } 
        else{     
                $(blockId).animate({height: 'hide'}, 500);
            }
    } 
</script>

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- блок корзина -->
       <div class="panel panel-default"><!-- panel panel -->
              <div class="panel-body">
                <span class="glyphicon glyphicon-shopping-cart"></span> В Вашей корзине <b>$total</b> $word_product
              </div>
              <div class="panel-footer" style="padding-bottom:15%;">
HERE;
$num = 0;
$totalCost = 0;
foreach ($productIdArray as $key => $value) {
            Core::app()->DbShop->productSingleSelect($value);
           $totalCost = $totalCost + $productDataArray['price'];
           $prodNameTranslit = $hfu->hfu_gen($productDataArray['prod_name']);
           ++$num;
           echo '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';
           
                echo '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">';    
                        echo '<a href="/'.$prodNameTranslit.'/production/v/'.$productDataArray['id'].'"><img src="/assets/media/images/production/'.$productDataArray['id'].'.jpg" 
                        class="img-responsive left-block img_basket_ms"/></a>';
                        
                echo '</div>';
                echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">';
                    echo '<div class="small text-muted">';   
                        echo '<p><a href="/'.$prodNameTranslit.'/production/v/'.$productDataArray['id'].'">'.$productDataArray['prod_name'].'</a></p>';
                        //echo "<p>{$productDataArray['v']}</p>";
                        echo "<p>{$productDataArray['price']} p.</p>";
                        echo '<p title="Удалить из корзины"><a href="/dellFromBasket/'.$productDataArray['id'].'"><span class="glyphicon glyphicon-trash"
                                style=""></span></a></p>';
                    echo '</div>';
                echo '</div>';
           echo '</div>';
    
           //для корректного отображения через каждые 3 итерации
           if( ($num == 3) OR ($num == 6) OR ($num == 9) OR ($num == 12) OR ($num == 15) OR ($num == 18) ) {
                echo '<div class="clearfix"></div>';
           }
            
        }

$back_url = $_SERVER['HTTP_REFERER'];
print <<<HERE
          </div>
          
          <div class="clearfix"></div>
          
        <script type="text/javascript">
            //активируем маску для поля ввода номера телефона
            jQuery(function($){
               $("#phone").mask("+38 (999) 999-99-99");
            });
        </script>
          
          <div class="panel-body">
                Общая стоимость: <b>$totalCost</b> руб. <a href="#" onclick="diplay_hide('#order_form_block');return false;">
                <button class="btn btn-success btn-bg">Оформить заказ</button></a>
                <div id="order_form_block" style="display: none;">
                
                    <form method="POST" action="/" role="form">
                    <div class="form-group">
                        <label for="client_name">Имя:</label>
                        <input type="text" name="client_name" value="" required
                            placeholder="Введите ваше имя" class="form-control" />
                    </div>
                    
                    <div class="form-group">
                        <label for="telephone_num">Номер телефона:</label>
                        <input type="text" name="telephone_num" value="" required
                            placeholder="" id="phone" class="form-control" />
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="control-label" >E-mail</label>
                        <input name="email" required type="email" class="form-control" id="email" 
                        value="" placeholder="Введите Ваш действующий e-mail">
                        <p class="help-block">на указанный Вами e-mail, будут высланы данные о Вашем заказе</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Адрес доставки:</label>
                        <textarea name="address" cols="15" rows="3" class="form-control" required></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="extra">Дополнительно:</label>
                        <textarea name="extra" cols="15" rows="3" class="form-control"></textarea>
                    </div>
                    
                    <input name="products_id" type="hidden" value="" />
                    <input name="back_url" type="hidden" value="$back_url" />
                    
                    <button name="order_confirm" type="submit" class="btn btn-success">Подтвердить заказ <span class="glyphicon glyphicon-ok"></span></button>
                    <a href="#" onclick="diplay_hide('#order_form_block');return false;"><button type="submit" class="btn btn-link"><span class="glyphicon glyphicon-remove-circle"></span> Отмена</button></a>
                </form>
          </div>
        </div>
    </div><!-- panel panel END -->
    
    
     
</div> <!-- блок корзина  END -->
HERE;
        } else {
print <<<HERE
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
       <div class="panel panel-default">
              <div class="panel-body">
                <span class="glyphicon glyphicon-shopping-cart"></span> Ваша корзина покупок пока пуста
              </div>
        </div>
    </div>
HERE;
            
        }
        //var_dump($productIdArray);
        //unset($_SESSION['id_ms_product']);
    }
    
    //запись заказа в базу данных
    //$params - массив данных переданный из формы оформления заказа
    public function orderInsertToDB($params) {
        $params['order_date'] = time(); //$date_today = date("d.m.y / H:i:s"); //дата и время заказа
        //получаем код заказа
        $params['order_code'] = strtoupper('X-'.substr(md5($this->params['order_date'].$this->params['telephone_num']),1,8));
        $params['products_id'] = $_SESSION['id_ms_product'];
        $add = $this->universalInsertDB('curr_order',$params);//универсальный метод добавления данных в БД
        
        //обрабатываем результат внесения заказа в БД
        if ($add) {
            unset($_SESSION['id_ms_product']); //чистим корзину
            $_SESSION['order_confirm'] = 'successful'; //ставим метку "успех"
            return true;
        } else {
             // оставляем товары в корзине (не чистим сессию с id товаров)
            $_SESSION['order_confirm'] = 'abortively';//ставим метку "неудача"
            return false;
        }
    }
    
    //вывод сообщения пользователю о результате обработки его заказа
    public function orderConfirmMassage() {
        if ($_SESSION['order_confirm'] == 'successful') {
            echo "
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='text-align: center;'>
                <br/>
                <div class='alert alert-success' role='alert'>
                    <p><b>Поздравляем!</b> Ваш заказ успешно принят!</p>
                    <p>Код заказа и другие данные о заказе высланы на указанный Вами e-mail.</p>
                    <p>Рекомендуем сохранить эти данные до получения заказа!</p>
                    <p>Спасибо за покупку!</p>
                </div>
                <br/>
            </div>
            ";
            
        } elseif($_SESSION['order_confirm'] == 'abortively') {
            echo '
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style="text-align: center;">
                <br/>
                <div class="alert alert-danger" role="alert">
                    К сожалению при обработке заказа возникла ошибка, оформить заказ не удалось =(<br />
                    Мы уже знаем об этом, в ближайшее время ошибка будет устранена! <br />
                    Приносим свои извенения. Администрация.
                </div>
            </div>
            ';
        }
        unset($_SESSION['order_confirm']); //чистим сессию
    }
    
    //корректируем слово "товаров" в зависимости от кол-ва
    public function wordProductCorect($totalNubmer) {
        //$last_number = substr($total, -1);// возвращает последний символ
            switch($totalNubmer){
                case 1:
                    $word_product = 'товар';
                break;
                
                case 2:
                    $word_product = 'товара';
                break;
                
                case 3:
                    $word_product = 'товара';
                break;
                
                case 4:
                    $word_product = 'товара';
                break;
                
                case 21:
                    $word_product = 'товар';
                break;
                
                case 22:
                    $word_product = 'товара';
                break;
                
                case 23:
                    $word_product = 'товара';
                break;
                
                case 24:
                    $word_product = 'товара';
                break;
                
                default:
                    $word_product = 'товаров';
                break;
            }
            return $word_product;
    }
}
?>