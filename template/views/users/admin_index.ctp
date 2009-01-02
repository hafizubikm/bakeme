<h2><?php  __('Users');?></h2>
<div id="tabpanel"> 
	<ul> 
		<li><a href="#tab1"><span><?php __('Overview');?></span></a></li>
		<li><a href="#tab2"><span><?php __('Deleted Users');?></span></a></li>
	</ul>
	
	<div id="tab1">
		
		<div class="users index">
		
			<?php echo $this->element('datatable', array('controllerPath' => 'users'));?>		
			<div class="actions">
				<ul>
					<li><?php echo $html->link(__('New User', true), array('action'=>'add'), array('class' => 'add')); ?></li>
				</ul>
			</div>
		</div>
	</div>
	
	<div id="tab2">
		
		<div class="users deleted">
		
			<?php echo $this->element('datatable', array('controllerPath' => 'users', 'idName' => 'usersDeleted', 'filters' => array('deleted' => '1')));?>		
		</div>
	</div>
</div>

<script type="text/javascript"> 
  $("#tabpanel > ul").tabs();
</script>