<style>
    .leftimg {
        float:left; /* Выравнивание по левому краю */
        margin: 7px 7px 7px 0; /* Отступы вокруг картинки */
    }
    .rightimg  {
        float: right; /* Выравнивание по правому краю  */ 
        margin: 7px 0 7px 7px; /* Отступы вокруг картинки */
    }
    .ms_last_content_div{
        background-color: #fff;
        float:  left;
    }
  </style>
<?php
$imgProcess = new MsIMGProcess;
$comments = new MsGbook;//создаём объект
$hfu = new MsHfu; //транслит обработчик
$timeProcess = new MsTimeProcess;
$max_scale = 50;//максимальный размер наибольшей стороны изображения
?>
<div class="row" style="background-color:"><!-- Общий блок (row) вывода виджета "last_content" -->

<!------------------------------------------------------------ 1Блок - последние статьи --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Статьи:</h4>
<?php
foreach($this->data['article'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/article/{$value['id']}.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'article');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
     $titleTranslit = $hfu->hfu_gen($value['article_title']);//для ЧПУ
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/article/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/article/v/{$value['id']}" title="Подробнее">{$value['article_title']}</a>
        <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
    
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние статьи END--------------------------------------------------------->

<!------------------------------------------------------------ 2Блок - последние заговоры --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div"> <!-- hidden-sm hidden-xs -->
    <h4>Заговоры:</h4>
<?php
foreach($this->data['plot'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/plot/{$value['id']}.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'plot');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    $titleTranslit = $hfu->hfu_gen($value['plot_name']);//для ЧПУ
    
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/plot/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/plot/v/{$value['id']}" title="Подробнее">{$value['plot_name']}</a>
   <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние заговоры END--------------------------------------------------------->


<!------------------------------------------------------------ 3Блок - последние тезисы --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Тезисы:</h4>
<?php
foreach($this->data['thesis'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/thesis/{$value['id']}.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'thesis');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
     $titleTranslit = $hfu->hfu_gen($value['date_add']);//для ЧПУ
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/thesis/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/thesis/v/{$value['id']}" title="Подробнее">{$value['thesis_text']}</a>
        <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
    
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние тезисы END--------------------------------------------------------->

<!------------------------------------------------------------ 4Блок - последние персоны --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Персоны:</h4>
<?php
foreach($this->data['author'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/author/_{$value['id']}/_ava.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'author');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    $titleTranslit = $hfu->hfu_gen($value['aunhor_name']." ".$value['author_surname']." ".$value['author_nik']);//для ЧПУ
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/author/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/author/v/{$value['id']}" title="Подробнее">{$value['author_nik']}
            <small>{$value['author_surname']} {$value['aunhor_name']} {$value['author_patronymic']}</small></a>
        <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
    
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние персоны END--------------------------------------------------------->
<div class="clearfix"></div>
<!------------------------------------------------------------ 5Блок - последние фото --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Фото:</h4>
<?php
foreach($this->data['photo'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/photo/{$value['id']}.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'photo');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    $titleTranslit = $hfu->hfu_gen($value['photo_name']);//для ЧПУ
    
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/photo/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/photo/v/{$value['id']}" title="Подробнее">{$value['photo_name']}</a>
   <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние фото END--------------------------------------------------------->


<!------------------------------------------------------------ 6Блок - последние аудиозаписи --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Аудиозаписи:</h4>
<?php
foreach($this->data['audio'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/audio/{$value['id']}.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'audio');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    $titleTranslit = $hfu->hfu_gen($value['audio_name']);//для ЧПУ
    
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/audio/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/audio/v/{$value['id']}" title="Подробнее">{$value['audio_name']}</a>
   <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние аудиозаписи END--------------------------------------------------------->


<!------------------------------------------------------------ 7Блок - последние видеозаписи --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Видеозаписи:</h4>
<?php
foreach($this->data['video'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/video/{$value['id']}.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/media/images/main/god_of_bibleism.jpg';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'video');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    $titleTranslit = $hfu->hfu_gen($value['video_name']);//для ЧПУ
    
    
print <<<HERE
    <p style="clear:both;"><a href="/$titleTranslit/video/v/{$value['id']}" title="Подробнее">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/></a>
        <a href="/$titleTranslit/video/v/{$value['id']}" title="Подробнее">{$value['video_name']}</a>
   <br>
        <small>
            Комментариев: [$commentsCall];
        </small>
   </p>
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние видеозаписи END--------------------------------------------------------->

<!------------------------------------------------------------ 8Блок - последние пользователи --------------------------------------------------------->
    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-6 ms_last_content_div">
    <h4>Пользователи:</h4>
<?php
foreach($this->data['user'] as $key => $value){
    //готовим к выводу изображение
    $img_path = "assets/media/images/user/{$value['id']}/ava.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if (!file_exists($img_path)) {
		$img_path = 'assets/images/img/avatar_male.png';//указываем путь к "заглушке"
	}
    //$max_scale = 80;//максимальный размер наибольшей стороны изображения
    $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, $max_scale); //ресайз изображения
    $h_view = $ava_size_massiv[0]; //полученная высота
    $w_view = $ava_size_massiv[1]; //полученная длинна
    
    //считаем кол-во комментариев у записи:
    $comments->selectComments($value['id'],'user');//отбираем соответствующие комментарии
    $commentsCall = $comments->selectedCommentsCount;//получаем кол-во соответсвующих комментариев
    
    $titleTranslit = $hfu->hfu_gen($value['name']);//для ЧПУ
    $date_last = $timeProcess->dateFromTimestamp($value['date_last']);
    
print <<<HERE
    <p style="clear:both;">
        <img src="/$img_path"  width="{$ava_size_massiv[1]}" height="$ava_size_massiv[0]" class="leftimg img-rounded"/>
        <span class="leftimg">{$value['name']} {$value['patronymic']}
        <br /><small>Последний визит: $date_last</small>;
        </span>
   </p>
HERE;
}
?>
    </div>
<!------------------------------------------------------------ Блок - последние пользователи END--------------------------------------------------------->





    
    
</div><!-- Общий блок (row) вывода виджета "last_content" END-->




<?php
//echo "FFFFFFFFFFFFFFFFFFFFFFFFFFF";
//var_dump($this->data);
//echo __METHOD__;
//$this->data['article']['main_title']

//echo '<div class="col-lg-4 col-md-4 col-sm-4 col-xs-4" style="background-color: #ccc; height:50px;">';

//echo '</div>'; 
    //var_dump($value);
     //echo $value[0].'<br>';
     //foreach($value[0] as $key2 => $value2){
        //echo $value2.'<br>';
        
     //}


//echo '<br><pre>';
       // print_r($this->data['article']);
//echo '<pre>';
?>
