<div id="tabpanel"> 
	<ul> 
		<li><a href="#tab1"><span><?php __('Edit Group');?></span></a></li>
	</ul>
	
	<div id="tab1">
		<script type="text/javascript">
			$(document).ready(function() {
				$('#groupForm').load('<?php echo $html->url(array('controller'=>'groups','action'=>'form', 'edit', $id));?>','#groupForm');
			});
		</script>
		<div id="groupForm"></div>
		<div class="actions">
			<ul>
				<li><?php echo $html->link(__('Delete', true), array('action'=>'delete', $form->value('Group.id')), array('class' => 'delete'), sprintf(__('Are you sure you want to delete # %s?', true), $form->value('Group.id'))); ?></li>
				<li><?php echo $html->link(__('List Groups', true), array('action'=>'index'), array('class' => 'index'));?></li>
					</ul>
		</div>
	</div>
</div>

<script type="text/javascript"> 
  $("#tabpanel > ul").tabs();
</script>