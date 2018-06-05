<?php
/**
 * @author Biblos
 * @copyright 2014
 * add_cat (Article)
 */

$modelName = strtolower($this->data->parser['model']); //для формирования ссылки на view

$hfu = new Hfu; //подключаем транслит кодер
$imgProcess = new IMGProcess;//для использования метода ресайза изображения
$stringProcess = new StringProcess;
$timeProcess = new TimeProcess;
?>

<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="admin_work_forms"><!--admin_work_forms-->

            <?php echo '<h1 id="h1">'.$this->data->pageTitle.'</h1>';?>

            <p>Имеющиеся изображения к разделу:</p>
<?php 
    //$img_path = 'assets/images/img/no_photo.jpg';//путь к "заглушке"
    foreach ($this->data->pictures as $key => $fileName) {
        if ($fileName) {
            $img_path = "assets/media/images/article_cat/{$this->data->id}/$fileName";//путь к изображению
            $ava_size_massiv = $imgProcess->img_out_size_mss($img_path, 140); //ресайз изображения
            $h_view = $ava_size_massiv[0]; //полученная высота
            $w_view = $ava_size_massiv[1]; //полученная длинна
            //echo "<img src='/$img_path' height='$h_view' width='$w_view'/>\n $fileName";


print <<<HERE
<div style="display: inline-block; text-align: center; padding:4px;">
    <img src='/$img_path' height='$h_view' width='$w_view'/>
    <div class="small text-muted">
        <p class="text-right">$fileName</p>
        <form method="post" action="/" enctype="multipart/form-data" role="$key">
            <input name="file_path" type="hidden" value="$img_path" />
            <button name="delete_file" type="submit" class="btn btn-danger btn-sm">Удалить</button>
        </form>
    </div>
</div>
HERE;
        } else {
            echo "<img src='/assets/images/img/no_photo.jpg' height='$h_view' width='$w_view'/><br>";
        }
    }
?>
            
            <form method="post" action="/" enctype="multipart/form-data"  role="form">

                <div class="form-group">
                    <label for="title">Наименование категории:</label>
                    <input type="text" name="title" value="<?php echo $this->data->title; ?>"
                        placeholder="Наименование категории" class="form-control" />
                </div>
                
                <div class="form-group">
                    <label for="image">Загрузить картинку::</label>
                    <input type="file" name="image[]" clsass="btn btn-link"/>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание категории:</label>
                    <textarea name="description" cols="30" rows="8" class="form-control"><?php echo $this->data->description; ?></textarea>
                </div>
                
                <input name="id" type="hidden" value="<?php echo $this->data->id; ?>" />
                <input name="back_url" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>" />
                <input name="table_name" type="hidden" value="article_cat" />
                
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <button name="update" type="submit" class="btn btn-success btn-lg btn-block">Сохранить</button>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)" class="a_decoration_off_ms">
                        <button type="button" class="btn btn-danger btn-lg btn-block">Отмена</button></a>
                    </div>
                </div>
            </form>

            <div class="alert alert-warning" role="alert"style="margin-top: 5%; padding-bottom: 6%;">
                <p>Внимание!</p>
                <p>- При отсутствии ссылки - оставляйте поле пустым;</p>
                <p>- Все введённые данные могут быть отредактированы позднее в данном разделе сайта.</p>
                <p style="color:gray; float: right;">... Moskaleny <a href="https://plus.google.com/u/0/112479966809654700772/about" target="_blank" 
                title="удачной работы с тезисами НА! =)"><img src="/assets/media/images/main/smailik_biznes.gif" height="25" width="28"/></a></p>
            </div>
        </div><!--.admin_work_forms-->
    </div><!--.content div-->
</div><!-- .content row-->