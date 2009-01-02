<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $html->charset(); ?>
	<title>
		<?php __('Admin Site:'); ?>
		<?php echo $title_for_layout;?>
	</title>
	
	<?php
		echo $html->meta('icon');

		echo $html->css('admin');
		echo $html->css('../js/jquery/jquery.autocomplete');

		echo $javascript->link('ckfinder/ckfinder.js');
		echo $javascript->link('fck/fckeditor.js');
		echo $javascript->link('jquery/jquery.js');
		echo $javascript->link('jquery/jquery-ui.js');
		echo $javascript->link('jquery/jquery.autocomplete.js');

		echo $scripts_for_layout;
	?>
	<script type="text/javascript">
		function mainmenu(){
		$(" #menuBar ul ").css({display: "none"}); // Opera Fix
		$(" #menuBar li").hover(function(){
				$(this).find('ul:first:hidden').css({visibility: "visible",display: "none"}).fadeIn(400);
				},function(){
				$(this).find('ul:first').css({visibility: "hidden"});
				});
		}
		
		 $(document).ready(function(){
			mainmenu();
		});
	</script>
</head>
<body>
	<div id="container">
		<div id="header">
			<h1>Admin Site</h1>
			<?php echo $this->renderElement("menu/jsmenu"); ?>
		</div>
		<div id="content">
			<?php
				if($session->check('Message.error')){
					$session->flash('error');
				}
				if($session->check('Message.info')){
					$session->flash('info');
				}
				if ($session->check('Message.flash')){
					$session->flash();
				}
				if ($session->check('Message.auth')){
					$session->flash('auth');
				}
			?>

			<?php echo $content_for_layout;?>

		</div>
		<div id="footer">

		</div>
	</div>
	<?php echo $cakeDebug?>
</body>
</html>