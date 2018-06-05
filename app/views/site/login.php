<div class="clearfix"></div>
<div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
    <?php echo $this->leftSideBarContent;?>
</div><!-- (блок левый)-->

<div class="col-lg-9 col-md-9 col-sm-9 col-xs-9"><!-- контент (средний блок)-->
    
    <div id="mainPageText">
        <h1 id="h1"><?php echo $this->data->h1 ?></h1>
        
        <div class="ms_login_form_div"><!--ms_login_form_div-->

            <div class="alert alert-success" role="alert">
                <p>Добро пожаловать на страницу авторизации! :)</p>
                <p>Если вы уже регистрировались, для входа на сайт введите свой e-mail и пароль, указанные при регистрации и нажмите "Вход".</p>
            </div>

            <?php echo $this->data->text?>

            <?php echo $this->data->system_massage?>
            <form method="POST" action="/" role="form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" required type="email" class="form-control" id="email" placeholder="Введите email">
                    <!-- <p class="help-block">если вы уже зарегистрированы - введите e-mail, который указывали при регистрации</p> -->
                </div>

                <div class="form-group">
                    <label for="pass">Пароль</label>
                    <input name="pass" required type="password" class="form-control" id="pass" placeholder="Пароль">
                </div>

                <div class="radio_">
                    <label>
                        <input type="radio" name="optionsRadios" id="radio_" value="2" checked>
                        Чужой компьютер (автовыход через 2 часа)
                    </label>
                </div>
                <div class="radio_">
                    <label>
                        <input type="radio" name="optionsRadios" id="radio_" value="72">
                        Запомнить меня на 72 часа (3 дня)
                    </label>
                </div>
                <div class="radio_">
                    <label>
                        <input type="radio" name="optionsRadios" id="radio_" value="2weeks">
                        Запомнить меня на 2 недели (14 дней)
                    </label>
                </div>

                <button name="log_in" type="submit" class="btn btn-success">Вход</button>
                <a href="/PassRestore"><button type="button" class="btn btn-link">Забыли пароль?</button></a>
                <a href="/Registration"><button type="button" class="btn btn-primary pull-right">Регистрация</button></a>
            </form>
        </div><!--.ms_login_form_div-->
    </div>

</div><!--.контент (средний блок) END-->

<!--<div class="col-lg-2 col-md-2 col-sm-2 col-xs-2">
<?php //echo $this->rightSideBarContent;?>
</div><!-- (блок правый)-->