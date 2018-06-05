<?php
/**
 * @author Biblos
 * @copyright 2014
 * index.php (Article)
 */

$data = $this->data->data;//данные из БД
$parser = $this->data->parser;//поарметры из парсера

//для формирования ссылки на view
$modelName = strtolower($this->data->parser['model']);

$TimeProcess = new TimeProcess;
$StringProcess = new StringProcess;
//$ImgProcess = new IMGProcess;
$Hfu = new Hfu;
$Gbook = new Gbook;//создаём объект

//кнопка удаления раздела
if ($this->data->delBtn) {
    /*пишем в массив пути файлов которые необходимо удалить 
        (полные пути включая имя и расширение файла!!!)*/
    /*$file_path = array(
        "0" => "assets/media/images/{$this->data->parser['table_name']}/{$this->data->id}.jpg"
    ,);*/
    $dir_path = array(
        "0" => "assets/media/images/article_cat/{$this->data->cat_id}/"
    ,);
    $HtmlDetails = new HtmlDetails();
    $delBtn = $HtmlDetails->delButtonRun($this->data->cat_id, "article_cat", 
            false, $dir_path, false, "Удалить раздел");
    $delBtn = "<p><center>$delBtn</center></p>";
}

//подключаем поиск по ключевому слову
/*$SearchKWord = new SearchKWord($tableName = strtolower($this->data->parser['model']), 
    strtolower($this->data->parser['model']), $this->data->parser['action_atribute'], 
    $this->data->parser['translit']); */
?>
<div class="clearfix"></div>
<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">
    <?php echo $this->leftSideBarContent;?>
</div><!-- (блок левый)-->

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12"><!-- контент (средний блок)-->
    <div id="mainPageText">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
<?php
 //кнопка "ДОБАВИТЬ"
if ($this->data->addNewBtn) {
print <<<HERE
   <p><a href="/admin/{$this->data->parser['model']}/add" class="a_decoration_off_ms">
   <button type="button" class="btn btn-primary btn-lg btn-block">Добавить новую статью!
   </button></a></p>
HERE;
}

//выводит результат загрузки файлов:
echo $_COOKIE['upload_files'];
setcookie("upload_files", "", time()-3600*2,"/");

if (is_file("assets/media/images/article_cat/{$this->data->cat_id}/000.jpg")) {
    echo "<p style='text-align:center;'><img src='/assets/media/images/article_cat/{$this->data->cat_id}/000.jpg' align='' vspace='5' hspace='5' alt='{$this->data->meta_description}' style='max-width: 30%; border-radius: 6px;'/></p>";
} else {
    echo '<h1 id="h1">"'.$this->data->pageTitle.'"</h1>';
}
// кнопки:
echo "<center>".$this->data->button_edit."</center>";
echo $delBtn;

print <<<HERE
<div style="text-align: center">
<hr>
<big><p style="text-align:center"><big><u>Тема данного раздела:</u></p>
{$this->data->meta_description}</big>
<hr>
</div>
HERE;
//навигация страниц
//Pagination::run($this->data->pagesNav);

    //var_dump($diaryPageData[$i]);
    foreach ($data as $key => $value) {
       //var_dump($data);
    $id = $value['id'];
    $article_title = $value['article_title'];
    $article_category_title = $value['title'];

    $article_titleTranslit = $Hfu->hfu_gen($article_title);//для ЧПУ

    $article_text = $StringProcess->cutText_mss(htmlspecialchars_decode($value['article_text'], ENT_QUOTES),300 );
    // convert the tags of the form "[]" into tags "<>"
    $article_text = $StringProcess->textBracketsDecode($article_text);
    $author_source = $value['author_source'];
    $source_link = $value['source_link'];

    if ($source_link) {
        $source_link = "<a href='$source_link' target='_blank' title='перейти по ссылке'><img src='/assets/media/images/main/net.png' height='25' width='25'/></a><br>";
    }

    $admin_info = $value['admin_info'];
    $admin_info = $StringProcess->cutAdminName_mss($admin_info);

    $date_edit = $value['date_edit'];
    if ($date_edit) {
        //var_dump($date_edit);die;
        $date_edit = $TimeProcess->dateFromTimestamp($date_edit);
    } else {
        $edit_info = "запись не редактировалась";
    }

    $date_add = $value['date_add'];
    $date_add = $TimeProcess->dateFromTimestamp($date_add);
    
    //var_dump($date_edit);die;
    
    $article_keywords = $value['article_keywords'];
    if ($this->data->show_views === true) {
        $views = intval($value['views']);
        $views = "<p class='text-right'>[Просмотров:$views]</p>";
    }
    
    //считаем кол-во комментариев у записи:
    $comments = $Gbook->selectComments($id, $parser['table_name']);//отбираем соответствующие комментарии
    $commentsCall = count($comments);
    if ($commentsCall === 0) {
        $commentsCall = "<span style='color:red'>$commentsCall</span>";
    } else {
        $commentsCall = "<span style='color:green'>$commentsCall</span>";
    }
    
    //готовим к выводу изображение
    $img_path = "assets/media/images/article/$id/000.jpg";//путь к изображению
    
    //проверяем наличие файла изображения
	if ( !file_exists($img_path) ) {
		$img_path = 'assets/images/img/no_photo.jpg';//указываем путь к "заглушке"
	}



print <<<HERE
<div class="row" style="padding-left:2%; padding-right:2%">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- name -->
        <h4 id="h_v1"><a href="/$article_titleTranslit/$modelName/v/$id" style="text-decoration:none;">$article_title</a></h4>
    </div><!--.name -->

    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12"> <!-- img -->
        <p><a href="/$article_titleTranslit/$modelName/v/$id" title="Подробнее">
        <img src="/$img_path" class="img-responsive center-block img-rounded"/></a></p>
    </div><!--.img -->
    
    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12" style="background-color:"> <!-- text -->
        <p>$article_text</p>
        <p class="text-right">
            <a href="/$article_titleTranslit/$modelName/v/$id"><small>Подробнее...</small></a>
        </p>
        <div class="small text-muted">
            <!-- <p class="text-right">Комментариев: $commentsCall</p> -->
            $views
        </div>
        <hr>
        
    </div><!--.text -->
</div>
HERE;
    }
//навигация страниц
Pagination::run($this->data->pagesNav);

//выводим ключевые слова (метки):
//$SearchKWord->keyWordsPrint();

 //кнопка "ДОБАВИТЬ"
if ($this->data->addNewBtn) {
print <<<HERE
   <p><a href="/admin/{$this->data->parser['model']}/add" class="a_decoration_off_ms">
   <button type="button" class="btn btn-primary btn-lg btn-block">Добавить новую статью!
   </button></a></p>
HERE;
}
?>
            </div>
        </div>
    </div>

</div><!--.контент (средний блок) END-->

<!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
    <?php //echo $this->rightSideBarContent;?>
</div><!-- (блок правый)-->