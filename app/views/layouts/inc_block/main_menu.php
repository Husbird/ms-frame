<!--навигация nav-justified href="#home" data-toggle="tab"-->
    <nav class="navbar navbar-default" role="navigation nav-stacked">
    <h2 style="display:none">Main menu</h2>
        <div class="container-fluid">
            <!--Название компании и кнопка, которая отображается для мобильных устройств группируются для лучшего отображения при свертывание-->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#"></a> <!--логотип-->
            </div>
            
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="<?php echo $this->data->activeHome;?>"><a href="/">Главная</a></li>
                    
                    <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Статьи<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                    <?php
                    $article_catData = Core::app()->DBProcess->getTableInfo('article_cat');
                    $Hfu = new Hfu;
                    foreach ($article_catData as $key => $value) {
                        $value['title'] = htmlspecialchars_decode($value['title'], ENT_QUOTES);
                        $translitTitle = $Hfu->hfu_gen(htmlspecialchars_decode($value['title'], ENT_QUOTES));
                        echo '<li><a href="/'.$translitTitle.'/article/i/1/'.$value['id'].'">
                                '.htmlspecialchars_decode($value['title'], ENT_QUOTES).'</a></li>';
                    }
                    ?>
                        <li class="divider"></li>
                        <li><a href="/info/article/all/1">Все статьи</a></li>
                    <?php
                        if ( Core::app()->accessCheck('Admin,Moderator') ) {
                            echo '<li class="divider"></li>';
                            echo '<li><a href="/info/article/add_cat">Добавить категорию</a></li>';
                        }
                    
                    ?>
                    </ul>
                    </li>
                    
                    <li class="<?php echo $this->data->activeContacts;?>">
                        <a href="/contacts" id="contacts" aria-controls="contacts" role="tab">Контакты</a></li>

                    <li class="<?php echo $this->data->activeAbout;?>">
                        <a href="/about" id="about" aria-controls="about" role="tab">О проекте</a></li>

                    <?php
                        if ( !Core::app()->accessCheck('Guest') ) {
                            echo '<li class="'.$this->data->activeSite.'"><a href="/settings">Настройки</a></li>';
                        }
                    ?>
                    <!--<form class="navbar-form navbar-left" role="search">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Search">
                        </div>
                        <button type="submit" class="btn btn-default" title="данная функция в стадии разработки...">поиск</button>
                    </form>-->
                    
                </ul>
            </div>
        </div>
    </nav>
    <!--навигация end-->