<?php
return (object)array(
  'app_name' => 'Code-info.ru', // название нашего приложения
  'controller_path' => 'app/controllers/',  // путь к папке с контроллерами
  'views_path' => 'app/views/',    // путь к папке файлов отображения
  'error_404' => 'app/views/layouts/404.php',    // путь к файлу "Ошибка 404.php"
  'models_path' => 'app/models/',        // путь к папке с моделями
  'framework_base' => 'framework/',       // путь к папке фреймворка
  'app_classes_path' => 'app/classes/',        // путь к классам приложения (пользовательским)
  'framework_classes' => 'framework/classes/',       // путь к папке фреймворка
  'framework_widgets' => 'framework/components/widgets/',       // путь к папке c виджетами
  'framework_modules' => 'framework/components/modules/',       // путь к папке c виджетами
  'css_path' => '/assets/css/',    // путь к папке стилей
  'scripts_path' => '/assets/scripts/',     // путь к папке скриптов
  'bootstrap_path' => '/assets/bootstrap_336/',    // путь к папке фреймворка bootstrap

  'site_path' => 'http://code-info.ru',    // абсолютный адрес сайта исп. для формирования обратных ссылок в письмах и т.д.
  //'site_path' => 'http://thesis.lyusiena.in.ua',    // абсолютный адрес сайта исп. для формирования обратных ссылок в письмах и т.д.

  // перечень адресов, на которые будут отправляться сообщения из формы обратной связи
  'email_developer' => 'ms-projects@mail.ru',
  'email_admin' => 'ms-box@mail.ru',

  'telephone' => '+375295049673',
  'address' => 'Республика Беларусь',

  'debug' => false, //режим отладки true-включить; false-отключить
  'session_log' => false, //режим ведения журнала в сессии (метод в Core)
  'log_files_write' => false, //ведение журнала логов true - вкл, false - выкл
  //'save_all' => true, //режим записи всех действий пользователей ДОДЕЛАТЬ

  // data base on localhost
  /*'db_host' => 'localhost',
  'db_user' => 'root',
  'db_pass' => 'root',
  'db_name' => '_ms_tech-help',*/

  // data base on hosting
  'db_host' => 'lyusi.mysql.tools',
  'db_user' => 'lyusi_codeinfo',
  'db_pass' => 'fxp7knp7',
  'db_name' => 'lyusi_codeinfo',

  // VK Open API options:
  'vk_api_enable' => true, // VK open API init
  'vk_api_id' => 6254754,
  'vk_comments_enable' => true, // comments widget on\off
  'vk_like_enable' => true, // like widget on\off

  // Gbook comments options:
  'access_for_guests' => true, // доступ к комментированию незарегистр.пользователей

  //арибуты действий (action):
  'action' => array(
     'i' => 'Index',
     'v' => 'View',
     'c' => 'Create',
     'u' => 'Update',
     'd' => 'Delete',
     'a' => 'Admin',
     'l' => 'Login',
     'r' => 'Registration',
     'e' => 'Edit',
     'add' => 'Add',
     'add_cat' => 'AddCategory',
     'e_cat' => 'EditCategory',
     'all' => 'All', //вывод всего содержимого(например статей) без учёта категорий категории
  ),
); 
// возвращаем массив настроек в виде массива объектов
?>