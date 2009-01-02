<?php $paginator->options(array('url' => $this->passedArgs)); ?>
<table cellpadding="0" cellspacing="0">
<tr id="usersSorting">
<th class="id"><?php echo $paginator->sort('id');?></th><th class="created"><?php echo $paginator->sort('created');?></th><th class="modified"><?php echo $paginator->sort('modified');?></th><th class="group_id"><?php echo $paginator->sort('group_id');?></th><th class="firstname"><?php echo $paginator->sort('firstname');?></th><th class="lastname"><?php echo $paginator->sort('lastname');?></th><th class="email"><?php echo $paginator->sort('email');?></th><th class="active"><?php echo $paginator->sort('active');?></th>	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($users as $user):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="id">
			<?php echo $user['User']['id']; ?>
		</td>
		<td class="created">
			<?php echo $user['User']['created']; ?>
		</td>
		<td class="modified">
			<?php echo $user['User']['modified']; ?>
		</td>
		<td class="group_id">
			<?php echo $html->link($user['Group']['name'], array('controller'=> 'groups', 'action'=>'view', $user['Group']['id'])); ?>
		</td>
		<td class="firstname">
			<?php echo $user['User']['firstname']; ?>
		</td>
		<td class="lastname">
			<?php echo $user['User']['lastname']; ?>
		</td>
		<td class="email">
			<?php echo $user['User']['email']; ?>
		</td>
		<td class="active">
			<?php echo ($user['User']['active'] == 1) ? __('yes', true) : __('no', true); ?>
		</td>
		<td class="actions">
			<?php
				if($user['User']['deleted'] == 1){
					echo $html->link($html->image('admin/undo.gif'), array('action'=>'undelete', $user['User']['id']), array('title' => __('Restore', true)), null, false) . "\n";
					echo $html->link($html->image('admin/delete.gif'), array('action'=>'hard_delete', $user['User']['id']), array('title' => __('Delete', true)), sprintf(__('Are you sure you want to delete # %s?', true), $user['User']['id']), false) . "\n";
				}else{
					echo $html->link($html->image('admin/view.gif'), array('action'=>'view', $user['User']['id']), array('title' => __('View', true)), null, false) . "\n";
					echo $html->link($html->image('admin/edit.gif'), array('action'=>'edit', $user['User']['id']), array('title' => __('Edit', true)), null, false) . "\n";
					echo $html->link($html->image('admin/trash.gif'), array('action'=>'delete', $user['User']['id']), array('title' => __('Delete', true)), sprintf(__('Are you sure you want to delete # %s?', true), $user['User']['id']), false) . "\n";
				}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<div class="paging" id="usersPaging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>