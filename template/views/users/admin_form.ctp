<div class="users form">
<?php echo $form->create('User', array('url' => $form_url));?>
	<?php
		if(empty($form_action) || $form_action != 'add') echo $extendedForm->input('User.id');
		echo $extendedForm->input('User.group_id');
		echo $extendedForm->input('User.firstname');
		echo $extendedForm->input('User.lastname');
		echo $extendedForm->input('User.email');
		echo $extendedForm->input('User.new_password', array('class' => 'form-item', 'value' => ''));
		echo $extendedForm->input('User.active');
	?>
<?php echo $form->end('Submit');?>
</div>