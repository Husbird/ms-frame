<?php
/**
 * @author Biblos
 * @copyright 2014
 * add.php (Article)
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
                    <label for="category_id">Выберите категорию:</label>
                    <select class="form-control" name="category_id">
                    <?php
                    foreach ($this->data->article_catData as $key => $value) {
                            echo "<option value=".$value['id']."> ".$value['title']."</option>";
                    }
                    ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="article_title">Заголовок:</label>
                      <input type="text" name="article_title" value=""
                        placeholder="Заголовок статьи" class="form-control" />
                </div>
                
                <div class="form-group">
                    <label for="article_text">Описание статьи:</label>
                      <textarea name="article_description" cols="50" rows="6" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="article_text">Текст статьи:</label>
                      <textarea name="article_text" cols="50" rows="18" class="form-control"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Загрузить картинку 1:</label>
                        <!-- <input type="hidden" name="MAX_FILE_SIZE" value="1048576" /> -->
                        <input type="file" name="image[]" class="btn btn-link"/>
                </div>
                <div class="form-group">
                    <label for="image">Загрузить картинку 2:</label>
                        <input type="file" name="image[]" class="btn btn-link"/>
                </div>
                <div class="form-group">
                    <label for="image">Загрузить картинку 3:</label>
                        <input type="file" name="image[]" class="btn btn-link"/>
                </div>
                
                
                <div class="form-group">
                    <label for="author_source">Автор/источник:</label>
                      <input type="text" name="author_source" value=""
                        placeholder="Укажите автора или источник" class="form-control" />
                </div>
                
                <div class="form-group">
                    <label for="source_link">Ссылка на источник:</label>
                      <input type="text" name="source_link" value=""
                        placeholder="Ссылка на источник (если есть)" class="form-control" />
                </div>
                
                <div class="form-group">
                    <label for="article_keywords">Ключевые слова:</label>
                      <input type="text" name="article_keywords" value=""
                       required placeholder="Ключевые слова (для поиска) через запятую" class="form-control" />
                </div>
                    <p>Рекомендуемые ключевые слова:</p>
                    <?php
                    //вывожу перечень ключевых слов
                    $stringProcess->echoKeyWords($this->data->allKeyWords);
                    ?>
                <input name="admin_info" type="hidden" value="<?php echo $this->data->admin_info; ?>">
                <input name="date_add" type="hidden" value="<?php echo $this->data->date_add; ?>">
                <input name="back_url" type="hidden" value="<?php echo $_SERVER['HTTP_REFERER']; ?>">
                <input name="table_name" type="hidden" value="<?php echo $this->data->parser['table_name']; ?>">
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
                <p>- При вводе ключевых слов (меток) старайтесь выбирать их из уже имеющихся (рекомендуемых).
                Только в случае необходимости - добавляйте новую (свою) метку. Такой подход, будет способствовать повышению удобства использования
                функции поиска в текущем разделе;</p>
                <p>- Все введённые данные могут быть отредактированы позднее в данном разделе сайта.</p>
                <p style="color:gray; float: right;">... Moskaleny <a href="https://plus.google.com/u/0/112479966809654700772/about" target="_blank" 
                title="удачной работы с тезисами НА! =)"><img src="/assets/media/images/main/smailik_biznes.gif" height="25" width="28"/></a></p>
            </div>
        </div><!--.admin_work_forms-->
    </div><!--.content div-->
</div><!-- .content row-->