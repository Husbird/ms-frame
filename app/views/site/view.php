<div class="clearfix"></div>
<div class="col-lg-3 col-md-3 hidden-sm hidden-xs">
        <?php echo $this->leftSideBarContent;?>
</div><!-- (блок левый)-->

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12"><!-- контент (средний блок)-->
    <div id="mainPageText">
        <article>
            <h1 id="h1"><?php echo $this->data->h1; ?></h1>
            <?php //var_dump($this->data);
                echo $this->data->massage;
                echo "<p><big>".$this->data->text."</big></p>";
                echo "<p><center>".$this->data->button_edit."</center></p>"; //phpinfo();
            ?>
        </article>
    </div>
        <?php include_once("app/views/layouts/inc_block/vk_comments_widget.php");?>
</div><!--.контент (средний блок) END-->

<!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
<?php echo $this->rightSideBarContent;?>
</div>--> <!--(блок правый)-->