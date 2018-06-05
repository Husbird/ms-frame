<script type="text/javascript">
	window.onload = function() {
		<?php 
		if (Core::app()->config->vk_comments_enable === true) {
			echo 'VK.Widgets.Comments("vk_comments", {limit: 10, width: "665", attach: "*"});';
		}

		if (Core::app()->config->vk_like_enable === true) { 
			echo "VK.Widgets.Like('vk_like', {width: 500, pageTitle: '$data->pageTitle', type: 'button', pageImage: 'http://code-info.ru/assets/media/images/article/".$this->data->id.".jpg'}, ".$this->data->id.");";
		}
		?>
		/*VK.Widgets.Poll("vk_poll", {width: "300"}, "278613253_8a62a17205e6b57db2");*/
	}
</script>