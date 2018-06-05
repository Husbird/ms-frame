<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries КОСТЫЛИ ДЛЯ IE-->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->
<!--[if lt IE 9]><script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
<!--<meta charset="utf-8" />-->
<title><?php echo $this->data->pageTitle;?></title>
<meta name="keywords" content="<?php echo $this->data->meta_keywords;?>" />
<meta name="description" content="<?php echo $this->data->meta_description;?>" />
<link rel="shortcut icon" href="/mss.ico" type="image/x-icon" />
<!--<link rel="icon" href="/mss.ico" type="ico"/>-->

<!-- Put this script tag to the <head> of your page -->
<?php
include_once("api_init.php");
?>

<!--стили bootstrap:-->
<link href="<?php echo Core::app()->config->bootstrap_path;?>css/bootstrap.css" rel="stylesheet"/>
<!--собственные фаилы стилей:-->
<link href="<?php echo Core::app()->config->css_path;?>ms_style.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>checkbox.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>radio.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>massages.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>gbook.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>captcha.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>ui.totop.css" rel="stylesheet"/>
<link href="<?php echo Core::app()->config->css_path;?>thesis.css" rel="stylesheet"/>

<!--jQuery (necessary for Bootstrap's JavaScript plugins)-->
<!--<script src="https://code.jquery.com/jquery.js"></script>-->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<!-- Latest compiled and minified CSS -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->

<!-- Optional theme -->
<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous"> -->

<!-- Latest compiled and minified JavaScript -->
<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script> -->

<script src="<?php echo Core::app()->config->bootstrap_path;?>js/bootstrap.js"></script>
<!--<script src="<?php echo Core::app()->config->bootstrap_path;?>js/bootstrap.min.js"></script>-->
<!--jQuery (necessary for Bootstrap's JavaScript plugins) END -->

<script src="<?php echo Core::app()->config->scripts_path;?>my_js/json2.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>my_js/myLibrary.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>my_js/app.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>my_js/mainMenu.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>my_js/gbook.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>my_js/captcha.js"></script>
<!-- <script src="<?php echo Core::app()->config->scripts_path;?>my_js/tagParser.js"></script> -->
<!--<script src="<?php //echo Core::app()->config->scripts_path;?>my_js/exercisesMenu.js"></script> -->

<!--UItoTop jQuery Plugin 1.2-->
<script src="<?php echo Core::app()->config->scripts_path;?>ulto_plugin/easing.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>ulto_plugin/jquery.ui.totop.js"></script>
<script src="<?php echo Core::app()->config->scripts_path;?>ulto_plugin/jquery.ui.totop.min.js"></script>
<!-- Starting the plugin -->
<script type="text/javascript">
 $(document).ready(function() {
  /*
   var defaults = {
   containerID: 'toTop', // fading element id
   containerHoverID: 'toTopHover', // fading element hover id
   scrollSpeed: 1200,
   easingType: 'linear'
   };
   */
  $().UItoTop({ easingType: 'easeOutQuart' });
 });
</script><!--UItoTop jQuery Plugin 1.2 END-->


<!--Starting the audioplayer-->
<!-- <script src="<?php echo Core::app()->config->scripts_path;?>/audiojs/audio.min.js"></script>
    <script>
      audiojs.events.ready(function() {
        var as = audiojs.createAll();
      });
    </script> -->
<!--audioplayer END -->

<!--<script src="<?php echo Core::app()->config->scripts_path;?>maskedinput.js"></script>--><!--маски для полей input форм (маска для поля ввода № телефона)-->