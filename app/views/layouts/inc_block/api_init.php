<?php if (Core::app()->config->vk_api_enable === true) { ?>
<!-- Put this script tag to the <head> of your page -->
<script src="https://vk.com/js/api/openapi.js?150" type="text/javascript"></script>
<script type="text/javascript">
  VK.init({apiId: <?php echo Core::app()->config->vk_api_id;?>, onlyWidgets: true});


</script>

<?php
}
?>