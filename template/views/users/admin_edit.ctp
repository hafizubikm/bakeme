<div id="tabpanel"> 
	<ul> 
		<li><a href="#tab1"><span><?php __('Edit User');?></span></a></li>
	</ul>
	
	<div id="tab1">
		<script type="text/javascript">
			$(document).ready(function() {
				$('#userForm').load('<?php echo $html->url(array('controller'=>'users','action'=>'form', 'edit', $id));?>','#userForm');
			});
		</script>
		<div id="userForm"></div>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('User.id')), array('class' => 'delete'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('User.id'))); ?></li>
				<li><?php echo $html->link(__('List Users', true), array('action'=>'index'), array('class' => 'index'));?></li>
					</ul>
		</div>
	</div>
</div>

<script type="text/javascript"> 
  $("#tabpanel > ul").tabs();
</script>