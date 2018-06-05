<section style="margin-top:30%;">
    <h4 style="margin-bottom:20%;">Последнее за неделю...</h4>
<?php
//echo "<p style='text-align:center; margin-top:7%'><b>ПОСЛЕДНЕЕ ЗА НЕДЕЛЮ...</b></p>";
//var_dump($this->data->last_content_article);
$hfu = new Hfu;

    foreach ($this->data->last_content_article as $key => $value) {
        $translit = $hfu->hfu_gen($value['article_title']);
        //готовим к выводу изображение
        $img_path = "assets/media/images/article/{$value['id']}/000.jpg";//путь к изображению
        //проверяем наличие файла изображения
        if ( !file_exists($img_path) ) {
        $img_path = 'assets/images/img/no_photo.jpg';//указываем путь к "заглушке"
        }
        echo "
        <section>
        <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style='border: 0px solid #999; margin-bottom:5px;'>
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style=''>
                <p><a href='/$translit/article/v/{$value['id']}' title='{$value['article_title']}, читать...'>
                    <img src='/$img_path' class='img-responsive center-block img-rounded'/></a></p>
            </div>
            <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12' style=''>
            <h5>
                <a href='/$translit/article/v/{$value['id']}' title='Подробнее'>{$value['article_title']}</a>
            </h5>
            </div>
        </div>
        </section>
        <hr>
        ";

    }
?>
</section>