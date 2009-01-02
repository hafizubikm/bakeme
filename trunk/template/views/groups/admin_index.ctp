<h2><?php  __('Groups');?></h2>
<div id="tabpanel"> 
	<ul> 
		<li><a href="#tab1"><span><?php __('Overview');?></span></a></li>
		<li><a href="#tab2"><span><?php __('Deleted Groups');?></span></a></li>
	</ul>
	
	<div id="tab1">
		
		<div class="groups index">
		
			<?php echo $this->element('datatable', array('controllerPath' => 'groups'));?>		
			<div class="actions">
				<ul>
					<li><?php echo $html->link(__('New Group', true), array('action'=>'add'), array('class' => 'add')); ?></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div id="tab2">
		
		<div class="groups deleted">
		
			<?php echo $this->element('datatable', array('controllerPath' => 'groups', 'idName' => 'groupsDeleted', 'filters' => array('deleted' => '1')));?>		
		</div>
	</div>
</div>

<script type="text/javascript"> 
  $("#tabpanel > ul").tabs();
</script>