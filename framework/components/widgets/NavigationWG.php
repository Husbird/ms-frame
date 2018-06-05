<?php
/**
* Виджет: навигация 
*/

class NavigationWG
{
    //public $MsDBProcess;
   
    public $action_atribute = 'i'; //атрибут действия для формирования урлов
    public $model_name = 'production'; //имя модели которая будет использована после перехода по ссылке меню для формирования урлов
    public $elementsArray; //массив данных полученный из таблицы с пунктами меню
    
    protected $mysqli = null; //метка соединения с БД

 
    function __construct(){
        $this->mysqli = Core::app()->DBase; //получаем метку соединения
        //var_dump($data);
        //$this->MsDBProcess = new MsDBProcess;
    }
    
    //вывод вертикального меню; 
    public function runVertMenuOne($currentCategoryId = false, $currentCategory_key = false){
        $MsHfu = new Hfu;
        //получаем все данные из таблицы категорий продукции
        $this->elementsArray = $this->menuOneSelect('prod_cat');
print <<<HERE
<!-- <div class="clearfix"></div> navbar-inverse -->
<img src="/assets/media/images/main/categories.jpg" class="img-responsive" style="width:100%; margin-top:17%; margin-left:-14%;"/>
<nav class="navbar" role="navigation"> 
  <ul class="nav nav-pills nav-stacked">
    
HERE;
        //Отдельный пункт вывода ВСЕЙ продукции
        if((!$currentCategoryId) OR ($currentCategory_key == 'brand')){ //если пункт категориине указан или используют гориз меню брендов (без выбора категории)
            $active = 'class="active"';
        }else{
            $active = '';
        }
        echo '<li role="presentation" '.$active.'><a href="/sport/production/all/1">Вся продукция</a></li>';//Отдельный пункт вывода ВСЕЙ продукции
        
        //Выводим пункты категорий продукции    
        foreach ($this->elementsArray as $key=>$value){
            //если текущая категория совпадает с выводящейся в меню - делаем пункт меню активным (учитываем ключ категории чтобы не было накладки с id при выборе сортировки по бренду)
            if(($value['id'] == $currentCategoryId) AND ($currentCategory_key == 'category')){
                $active = 'class="active"';
                //var_dump($_SESSION['bbb']);
            }else{
                $active = '';
            }
            echo '<li role="presentation" '.$active.'><a href="/'.$MsHfu->hfu_gen($value['category_name']).'/'
            .$this->model_name.'/'.$this->action_atribute.'/1/'.$value['id'].'/category">'.$value['category_name'].'</a></li>';
        }
print <<<HERE
  </ul>
</nav>
HERE;
    }
    
    //мобильная версия вертикального меню
    /**
 * public function runVertMenuOneMobilVersion($currentCategoryId = false, $currentCategory_key = false){
 *         $MsHfu = new MsHfu;
 *         //получаем все данные из таблицы категорий продукции
 *         $this->elementsArray = $this->menuOneSelect('prod_cat');
 * print <<<HERE
 *     <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation nav-stacked">
 *         <div class="container-fluid">
 *             <!-- Название компании и кнопка, которая отображается для мобильных устройств группируются для лучшего отображения при свертывание -->
 *             <div class="navbar-header">
 *                 <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-2">
 *                 <span class="sr-only">Toggle navigation</span>
 *                 <span class="icon-bar"></span>
 *                 <span class="icon-bar"></span>
 *                 <span class="icon-bar"></span>
 *                 </button>
 *                 <a class="navbar-brand" href="#">Категории товаров</a>
 *             </div>
 *             
 *             <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-2">
 *                 <ul class="nav navbar-nav">
 * HERE;
 * //Отдельный пункт вывода ВСЕЙ продукции
 *         if((!$currentCategoryId) OR ($currentCategory_key == 'brand')){ //если пункт категориине указан или используют гориз меню брендов (без выбора категории)
 *             $active = 'class="active"';
 *         }else{
 *             $active = '';
 *         }
 *         echo '<li '.$active.'><a href="/sport/production/all/1">Вся продукция</a></li>';//Отдельный пункт вывода ВСЕЙ продукции
 *         //Выводим пункты категорий продукции    
 *         foreach ($this->elementsArray as $key=>$value){
 *             //если текущая категория совпадает с выводящейся в меню - делаем пункт меню активным (учитываем ключ категории чтобы не было накладки с id при выборе сортировки по бренду)
 *             if(($value['id'] == $currentCategoryId) AND ($currentCategory_key == 'category')){
 *                 $active = 'class="active"';
 *                 //var_dump($_SESSION['bbb']);
 *             }else{
 *                 $active = '';
 *             }
 *             echo '<li '.$active.'><a href="/'.$MsHfu->hfu_gen($value['category_name']).'/'
 *             .$this->model_name.'/'.$this->action_atribute.'/1/'.$value['id'].'/category">'.$value['category_name'].'</a></li>';
 *         }
 * print <<<HERE
 *                 </ul>
 *             </div>
 *         </div>
 *     </nav>
 * HERE;
 *     }
 */
    
    //мобильная версия вертикального меню
    public function runVertMenuOneDropdownLiMobilVersion($currentCategoryId = false, $currentCategory_key = false){
        $MsHfu = new Hfu;
        //получаем все данные из таблицы категорий продукции
        $this->elementsArray = $this->menuOneSelect('prod_cat');

    //Отдельный пункт вывода ВСЕЙ продукции
        if((!$currentCategoryId) OR ($currentCategory_key == 'brand')){ //если пункт категориине указан или используют гориз меню брендов (без выбора категории)
            $active = 'class="active"';
        }else{
            $active = '';
        }
        echo '<li '.$active.'><a href="/sport/production/all/1">Вся продукция</a></li>';//Отдельный пункт вывода ВСЕЙ продукции
        //Выводим пункты категорий продукции    
        foreach ($this->elementsArray as $key=>$value){
            //если текущая категория совпадает с выводящейся в меню - делаем пункт меню активным (учитываем ключ категории чтобы не было накладки с id при выборе сортировки по бренду)
            if(($value['id'] == $currentCategoryId) AND ($currentCategory_key == 'category')){
                $active = 'class="active"';
                //var_dump($_SESSION['bbb']);
            }else{
                $active = '';
            }
            echo '<li '.$active.'><a href="/'.$MsHfu->hfu_gen($value['category_name']).'/'
            .$this->model_name.'/'.$this->action_atribute.'/1/'.$value['id'].'/category">'.$value['category_name'].'</a></li>';
        }
    }
    
    //вывод горизонтальное меню (брендов); 
    public function runGorMenuOne($currentCategoryId = false, $sub_category_id_active = false, $category_key = 'brand'){
        $MsHfu = new Hfu;
        ///если перешли по пункту данного меню (ПОСЛЕ сортировки по категории товара (т.е. основной категорией является КАТЕГОРИЯ ТОВАРА, а 
        // бренд является подкатегорией) то формируем ссылку с ключом category, после каторого указываем id подкатегории (т.е. бренда)
//echo'<p>Производители:</p>';
        if($category_key == 'category'){
            //получаем все данные из таблицы брендов для использования в выводе меню
            $this->elementsArray = $this->menuOneSelect('prod_brand');
print <<<HERE
<div class="clearfix"></div>

  <ul class="nav nav-pills">
HERE;
            //пункт ВСЕ ПРОИЗВОДИТЕЛИ /proteiny/production/i/1/6/category
            if(!$sub_category_id_active){
                $active = 'class="active"';
            }else{
                $active = '';
            }
            
            //var_dump($_SESSION['bbb']);
            echo '<li role="presentation" '.$active.'><a href="/all/'
                .$this->model_name.'/'.$this->action_atribute.'/1/'.$currentCategoryId.'/category">Все производители</a></li>';

            foreach ($this->elementsArray as $key=>$value){ //$value['id'] - подкатегория, $currentCategoryId - основная текущая категория
                $tableName = strtolower($this->model_name);
                $sub_category_id = $value['id'];
                //считаем кол-во продуктов соответствующих текущему пункту
                $sql = "SELECT id FROM `$tableName` WHERE category_id = $currentCategoryId AND brand_id = $sub_category_id";
                $query = $this->mysqli->query($sql);//true OOП
                $total = mysqli_num_rows($query); //кол-во всех записей
                //если текущая подкатегория совпадает с выводящейся в меню - делаем пункт меню активным
                if($sub_category_id_active == $sub_category_id){
                    $active = 'class="active"';
                }else{
                    $active = '';
                }
                //если продуктов бренда больше нуля
                if($total > 0){
                    echo '<li role="presentation" '.$active.'><a href="/'.$MsHfu->hfu_gen($value['brand_name']).'/'
                .$this->model_name.'/'.$this->action_atribute.'/1/'.$currentCategoryId.'/category/'.$value['id'].'">'.$value['brand_name'].' ['.$total.']</a></li>';    
                }
            }
print <<<HERE
  </ul>

HERE;
        //если перешли по пункту данного меню (без сортировки по категории товара или со страницы ВСЯ ПРОДУКЦИЯ (где ключ категории отсутствует)
        // то формируем ссылку с ключом категории brand)
        }elseif(($category_key == 'brand') OR ($category_key == NULL)){
            //получаем все данные из таблицы брендов для использования в выводе меню
            $this->elementsArray = $this->menuOneSelect('prod_brand');
print <<<HERE
<div class="clearfix"></div>


  <ul class="nav nav-pills">

HERE;
        //пункт ВСЕ ПРОИЗВОДИТЕЛИ
        if(!$currentCategoryId){
            $active = 'class="active"';
        }else{
            $active = '';
        }
        echo '<li role="presentation" '.$active.'><a href="/sport/production/all/1">Все производители</a></li>';
        
            foreach ($this->elementsArray as $key=>$value){ //$value['id'] - подкатегория, $currentCategoryId - основная текущая категория
                $tableName = strtolower($this->model_name);
                $category_id = $value['id'];
                //считаем кол-во продуктов соответствующих текущему пункту
                $sql = "SELECT id FROM `$tableName` WHERE brand_id = $category_id";
                $query = $this->mysqli->query($sql);//true OOП
                $total = mysqli_num_rows($query); //кол-во всех записей
                //если текущая подкатегория совпадает с выводящейся в меню - делаем пункт меню активным
                if($currentCategoryId == $category_id){
                    $active = 'class="active"';
                }else{
                    $active = '';
                }
                //http://thesis-ms.loc:81/zhiroszhigateli-energetiki/production/i/1/9/category
                echo '<li role="presentation" '.$active.'><a href="/'.$MsHfu->hfu_gen($value['brand_name']).'/'
                .$this->model_name.'/'.$this->action_atribute.'/1/'.$value['id'].'/brand">'.$value['brand_name'].' ['.$total.']</a></li>';
            }
print <<<HERE
  </ul>

HERE;
        }

    }
    
    
    
    //вывод вертикального (АНАЛОГА горизонтальное меню (брендов))
    public function runGorMenuOneVertAnalog($currentCategoryId = false, $sub_category_id_active = false, $category_key = 'brand'){
        $MsHfu = new Hfu;
        ///если перешли по пункту данного меню (ПОСЛЕ сортировки по категории товара (т.е. основной категорией является КАТЕГОРИЯ ТОВАРА, а 
        // бренд является подкатегорией) то формируем ссылку с ключом category, после каторого указываем id подкатегории (т.е. бренда)
//echo'<p>Производители:</p>';
        if($category_key == 'category'){
            //получаем все данные из таблицы брендов для использования в выводе меню
            $this->elementsArray = $this->menuOneSelect('prod_brand');
print <<<HERE
<div class="clearfix"></div>
<img src="/assets/media/images/main/brands.jpg" class="img-responsive" style="width:100%;"/>
<nav class="navbar" role="navigation"> 
  <ul class="nav nav-pills nav-stacked">
HERE;
            //пункт ВСЕ ПРОИЗВОДИТЕЛИ /proteiny/production/i/1/6/category
            if(!$sub_category_id_active){
                $active = 'class="active"';
            }else{
                $active = '';
            }
            
            //var_dump($_SESSION['bbb']);
            echo '<li role="presentation" '.$active.'><a href="/all/'
                .$this->model_name.'/'.$this->action_atribute.'/1/'.$currentCategoryId.'/category">Все производители</a></li>';

            foreach ($this->elementsArray as $key=>$value){ //$value['id'] - подкатегория, $currentCategoryId - основная текущая категория
                $tableName = strtolower($this->model_name);
                $sub_category_id = $value['id'];
                //считаем кол-во продуктов соответствующих текущему пункту
                $sql = "SELECT id FROM `$tableName` WHERE category_id = $currentCategoryId AND brand_id = $sub_category_id";
                $query = $this->mysqli->query($sql);//true OOП
                $total = mysqli_num_rows($query); //кол-во всех записей
                //если текущая подкатегория совпадает с выводящейся в меню - делаем пункт меню активным
                if($sub_category_id_active == $sub_category_id){
                    $active = 'class="active"';
                }else{
                    $active = '';
                }
                //если продуктов бренда больше нуля
                if($total > 0){
                    echo '<li role="presentation" '.$active.'><a href="/'.$MsHfu->hfu_gen($value['brand_name']).'/'
                .$this->model_name.'/'.$this->action_atribute.'/1/'.$currentCategoryId.'/category/'.$value['id'].'">'.$value['brand_name'].' ['.$total.']</a></li>';    
                }
            }
print <<<HERE
  </ul>
</nav>
HERE;
        //если перешли по пункту данного меню (без сортировки по категории товара или со страницы ВСЯ ПРОДУКЦИЯ (где ключ категории отсутствует)
        // то формируем ссылку с ключом категории brand)
        }elseif(($category_key == 'brand') OR ($category_key == NULL)){
            //получаем все данные из таблицы брендов для использования в выводе меню
            $this->elementsArray = $this->menuOneSelect('prod_brand');
print <<<HERE
<div class="clearfix"></div>
<img src="/assets/media/images/main/brands.jpg" class="img-responsive" style="width:100%;"/>
<nav class="navbar" role="navigation"> 
  <ul class="nav nav-pills nav-stacked">

HERE;
        //пункт ВСЕ ПРОИЗВОДИТЕЛИ
        if(!$currentCategoryId){
            $active = 'class="active"';
        }else{
            $active = '';
        }
        echo '<li role="presentation" '.$active.'><a href="/sport/production/all/1">Все производители</a></li>';
        
            foreach ($this->elementsArray as $key=>$value){ //$value['id'] - подкатегория, $currentCategoryId - основная текущая категория
                $tableName = strtolower($this->model_name);
                $category_id = $value['id'];
                //считаем кол-во продуктов соответствующих текущему пункту
                $sql = "SELECT id FROM `$tableName` WHERE brand_id = $category_id";
                $query = $this->mysqli->query($sql);//true OOП
                $total = mysqli_num_rows($query); //кол-во всех записей
                //если текущая подкатегория совпадает с выводящейся в меню - делаем пункт меню активным
                if($currentCategoryId == $category_id){
                    $active = 'class="active"';
                }else{
                    $active = '';
                }
                //http://thesis-ms.loc:81/zhiroszhigateli-energetiki/production/i/1/9/category
                echo '<li role="presentation" '.$active.'><a href="/'.$MsHfu->hfu_gen($value['brand_name']).'/'
                .$this->model_name.'/'.$this->action_atribute.'/1/'.$value['id'].'/brand">'.$value['brand_name'].' ['.$total.']</a></li>';
            }
print <<<HERE
  </ul>
</nav>
HERE;
        }

    }
    
    
    
    
    //выборка всех данных указанной таблицы
    public function menuOneSelect($tableName){
        
        $sql = "SELECT * FROM `$tableName`";
            //$sql = "SELECT * FROM `$tableName` WHERE category_id = $category_id ORDER by $order_by $asc_desc LIMIT $start, $num ";
            //var_dump($sql);
        $query = $this->mysqli->query($sql);//true
        // В цикле переносим результаты запроса в массив $authorsData[]
		while ($data[] = mysqli_fetch_assoc($query));
        array_pop($data);//удаляем последний (пустой)элемент массива"
        //var_dump($data);
        return $data; //возвращаем массив 
        
        /**
         * array(17) { [0]=> array(3) { ["id"]=> string(2) "14" ["category_name"]=> 
         *         string(107) "Гормональные бустеры(тестостерон,гормон роста,инсулин)" ["cat_description"]=> 
         *         string(0) "" } [1]=> array(3) { ["id"]=> 
         */
    }
}
?>
<!-- ПРИМЕР БЛОКА:
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <ul class="breadcrumb">
              <li><a href="/">Главная</a> <span class="divider">/</span></li>
              <li><a href="#">Библиотека</a> <span class="divider">/</span></li>
              <li class="active">Данные</li>
            </ul>
        </div>
    </div>
</div> -->