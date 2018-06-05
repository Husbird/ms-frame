<?php
$data = $this->data;

$timeProcess = new TimeProcess;
$stringProcess = new StringProcess;
$imgProcess = new IMGProcess;

$img_path = "assets/media/images/article/$data->id/000.jpg";//путь к главному изображению
//проверяем наличие файла изображения
if ( !file_exists($img_path) ) {
	$img_path = 'assets/images/img/no_photo.jpg';//указываем путь к "заглушке"
}
//кнопка удаления
if ($this->data->delBtn) {
    //пишем в массив пути файлов которые необходимо удалить (полные пути включая имя и расширение файла!!!)
    /*$file_path = array(
        "0" => "assets/media/images/{$this->data->parser['table_name']}/{$this->data->id}.jpg"
    ,);*/
    $file_path = false;
    $dir_path = array(
        "assets/media/images/{$this->data->parser['table_name']}/{$this->data->id}/"
    ,);
    $HtmlDetails = new HtmlDetails();
    $delBtn = $HtmlDetails->delButtonRun($this->data->id, $this->data->parser['table_name'], $file_path, $dir_path);
}


include_once("app/views/layouts/inc_block/widgets_init.php");
?>
<div class="clearfix"></div>

<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">
    <?php echo $this->leftSideBarContent;?>
</div><!-- (блок левый)-->

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12"><!-- контент (средний блок)-->
    <div id="mainPageText">
        <div itemscope itemtype="http://schema.org/TechArticle"> <!-- микроразметка start -->
            <h1 id="h1" itemprop="name"><?php echo $data->pageTitle; ?></h1> <!-- Название статьи (имеет приоритет перед name для Яндекса) -->
            <meta itemprop="headline" content="<?php echo $data->pageTitle; ?>"> <!-- Название статьи -->
            <meta itemprop="description" content="<?php echo $data->meta_description;?>"> <!-- Краткое описание статьи -->
            <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="<?php echo $data->mainEntityOfPageUrl;?>"/>
            <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization"><!-- Издатель start -->
                <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                    <img itemprop="url image" src="<?php echo $data->logoImgUrl;?>" style="display:none;"/>
                    <meta itemprop="width" content="150">
                    <meta itemprop="height" content="150">
                </div>
                <meta itemprop="name" content="<?php echo Core::app()->config->app_name;?>">
                <meta itemprop="telephone" content="<?php echo Core::app()->config->telephone;?>">
                <meta itemprop="address" content="<?php echo Core::app()->config->address;?>">
            </div><!-- Издатель end -->
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- article block start -->
                <figure>
                <img itemprop="image" src="https://code-info.ru/assets/media/images/article/<?php echo $data->id;?>/000.jpg" class="leftimg" alt="<?php echo $this->data->meta_description;?>" style="max-width: 30%; border-radius: 6px;" /> <!--src="/<?php echo $img_path; ?>"-->
                </figure>
                <p class="text-justify" itemprop="articleBody"> <!-- Краткое описание статьи (имеет приоритет перед description для Яндекса) -->
                    <article>
                        <big><?php echo $data->article_text;?></big>
                        <div style="float:right;"><div id="vk_like"></div></div>
                        <div id="vk_poll"></div>
                    </article>
                </p>
                <div style="display:none"> <!-- hidden microcode start -->
                    <img itemprop="image" src="https://code-info.ru/assets/media/images/article/<?php echo $data->id;?>/000.jpg" />
                    <p itemprop="genre">Техническая</p> <!-- Жанр (множественное) -->
                    <p itemprop="author"><?php echo $data->author_source;?></p> <!-- Автор (краткое) -->
                    
                    <meta itemprop="datePublished" content="<?php echo $data->datePublished;?>"> <!-- datePublished -->
                    <meta itemprop="dateModified" content="<?php echo $data->dateModified;?>"> <!-- dateModified -->
                </div> <!-- hidden microcode end -->
            </div><!-- article block end -->
        </div><!-- микроразметка end -->
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" style=""> <!-- text -->
            <div class="small text-muted">
                <hr>
                <p class="text-right">Добавлено: <?php echo $timeProcess->dateFromTimestamp($data->date_add);?></p>
                <p class="text-right">[Просмотров: <?php echo $data->views ?>]</p>
                <p class="text-right"><a href="<?php echo $data->source_link;?>" target="_blank">Источник: <?php echo $data->author_source;?></a></p>
                <hr>
            </div>
        </div><!--.text -->

        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"> <!-- buttons -->

            <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12">
                <div id="vk_comments" style="margin: 0 auto; margin-top: 1%;"></div>
            </div>

            <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 text-right">
                <p><?php echo $this->data->button_edit;?></p>
                <p>
                    <?php
                    //выводит результат загрузки файлов:
                    echo $_COOKIE['upload_files'];
                    setcookie("upload_files", "", time()-3600*2,"/");
                    ?>
                </p>
                <p><?php echo $delBtn;?></p>
                <p><a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)">
                <img src="/assets/media/images/main/back.png" height="130" width="130"/></a></p>
            </div>

        </div><!--.buttons -->
<?php
//подключаем блок коммениарии:
//$gBook = new Gbook;
//$gBook->openForm($this->data->parser['table_name'],$this->data->id);
?>
    </div><!--mainPageText END-->
</div><!--.контент (средний блок) END-->

<!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
    <?php echo $this->rightSideBarContent;?>
</div>--> <!--(блок правый)-->