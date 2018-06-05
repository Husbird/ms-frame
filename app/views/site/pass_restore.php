<div class="row"><!-- content row-->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><!--content div-->
        <div class="ms_pass_restore_form_div"><!--ms_pass_restore_form_div-->
            <h1><?php echo $this->data->title ?></h1>
            
            <?php //echo $this->data->system_massage;?>
            <?php echo $this->data->parser['email_not_find'];?>
            <?php //echo $this->data->text?>
            
            <form method="POST" action="/" role="form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input name="email" required type="email" class="form-control" id="email" placeholder="Введите email">
                    <!-- <p class="help-block">если вы уже зарегистрированы - введите e-mail, который указывали при регистрации</p> -->
                </div>
                <button name="pass_restore" type="submit" class="btn btn-success">Восстановить</button>
                <a href="/Login"><button type="button" class="btn btn-link pull-right">Назад &gt;&gt;&gt;</button></a>
            </form>
            
            <br/>
                <div class="alert alert-warning" role="alert">
                    <p>ВНИМАНИЕ!<br/> Для восстановления пароля,<br/> 
                    введите в представленную форму электронный адрес (Email), который Вы использовали при регистрации.</p>
                    <p>На него будет выслано письмо с сылкой для автоматического восстановления пароля!</p>
                    <p>В случае потери доступа к электронному адресу который Вы использовали при регистрации,<br/>
                    или если Вы его забыли - восстановление пароля к сайту будет <b>невозможно</b> :(</p>
                    <p>ПОМНИТЕ! Вы всегда можете заново зарегистрироваться используя другой электронный адрес...
                    <a href="/Registration"><button type="button" class="btn btn-link ">Регистрация</button></a></p>
                    
                </div>
            <br/>
        </div><!--.ms_pass_restore_form_div-->
    </div><!--.content div-->
</div><!-- .content row-->