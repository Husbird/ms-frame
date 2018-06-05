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
            
            <form method="post" action="/" enctype="multipart/form-data"  role="form">

                <div class="form-group">
                    <label for="title">Наименование категории:</label>
                    <input type="text" name="title" value=""
                        placeholder="Наименование категории" class="form-control" />
                </div>
                
                <div class="form-group">
                    <label for="image">Загрузить картинку::</label>
                    <input type="file" name="image[]" class="btn btn-link"/>
                </div>
                
                <div class="form-group">
                    <label for="description">Описание категории:</label>
                    <textarea name="description" cols="30" rows="8" class="form-control"></textarea>
                </div>
                
                <input name="table_name" type="hidden" value="<?php echo 'article_cat';//$this->data->parser['table_name']; ?>">
                <input name="admin_info" type="hidden" value="<?php echo $this->data->admin_info; ?>">
                <input name="date_add" type="hidden" value="<?php echo $this->data->date_add; ?>">
                <input name="back_url" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <button name="add" type="submit" class="btn btn-success btn-lg btn-block">Сохранить</button>
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