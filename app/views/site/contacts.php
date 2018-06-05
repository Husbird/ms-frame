<div class="clearfix"></div>
<div class="col-lg-3 col-md-3 hidden-sm hidden-xs" style="">
    <?php echo $this->leftSideBarContent;?>
</div><!-- (блок левый) col-lg-3 col-md-3 d-sm-none sm-hidden xs-hidden -->

<div class="col-lg-9 col-md-9 col-sm-12 col-xs-12"><!-- контент (средний блок)-->

    <div id="mainPageText">
        <h1 id="h1"><?php echo $this->data->h1 ?></h1>
        <div class="ms_push_div_to_center"><!--ms_content_forms_div-->
        <?php
            echo $_SESSION['sendMailReport'];unset($_SESSION['sendMailReport']);
            echo $this->data->massage;
            echo $this->data->text;
            echo "<p><center>".$this->data->button_edit."</center></p>"; //кнопка редактирования
        ?>

            <form method="post" action="/" enctype="multipart/form-data"  role="form" style="margin-top: 7%;">
                <div class="input-group">
                    <span class="input-group-addon">Ваше имя</span>
                    <input type="text" name="client_name" value="<?php echo $_SESSION['contactFormClient_name']; unset($_SESSION['contactFormClient_name']); ?>"
                           required="required" placeholder="введите Ваше имя" class="form-control" />
                </div>
                <br/>
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input name="email" required="required" type="email" class="form-control" id="email"
                           value="<?php echo $_SESSION['contactFormEmail']; unset($_SESSION['contactFormEmail']); ?>" placeholder="Введите email" />
                    <p style='color:red'><b><?php echo $_SESSION['emailCheckErrorMassage']; unset($_SESSION['emailCheckErrorMassage']) ?></b></p>
                </div>
                <p class="help-block">(на указанный email - мы отправим наш ответ)</p>

                <div class="form-group">
                    <label for="user_massage">Текст Вашего вопроса\предложения:</label>
                    <textarea name="user_massage" cols="50" rows="10" class="form-control"><?php echo $_SESSION['contactFormUser_massage']; unset($_SESSION['contactFormUser_massage']); ?></textarea>
                    <p style='color:red'><b><?php echo $_SESSION['massageCheckErrorMassage']; unset($_SESSION['massageCheckErrorMassage']) ?></b></p>
                </div>

                <?php
                $Captcha = new Captcha;
                $Captcha->showCaptcha();
                ?>

                <input name="id" type="hidden" value="<?php echo $this->data->id; ?>" />
                <input name="back_url" type="hidden" value="/Contacts" />
                <div class="row">
                    <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                        <button name="dispatch_massage" type="submit" class="btn btn-success btn-sm btn-block">Отправить сообщение</button>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                        <a href="javascript:history.go(-1)" mce_href="javascript:history.go(-1)" class="a_decoration_off_ms">
                            <button type="button" class="btn btn-danger btn-sm btn-block">Отмена</button></a>
                    </div>
                </div>
            </form>
        </div><!--.ms_content_forms_div-->

    </div>
    <?php
    //подключаем вывод протокола работы приложения (если включен режим отладки)
    //include_once('framework/components/massages/sysLog.php');
    ?>
</div><!--.контент (средний блок) END-->

<!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
    <?php echo $this->rightSideBarContent;?>
</div><!-- (блок правый)-->