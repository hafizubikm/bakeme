<?php $paginator->options(array('url' => $this->passedArgs)); ?>
<table cellpadding="0" cellspacing="0">
<tr id="groupsSorting">
<th class="id"><?php echo $paginator->sort('id');?></th><th class="created"><?php echo $paginator->sort('created');?></th><th class="modified"><?php echo $paginator->sort('modified');?></th><th class="name"><?php echo $paginator->sort('name');?></th><th class="parent_id"><?php echo $paginator->sort('parent_id');?></th><th class="lft"><?php echo $paginator->sort('lft');?></th><th class="rght"><?php echo $paginator->sort('rght');?></th>	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($groups as $group):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td class="id">
			<?php echo $group['Group']['id']; ?>
		</td>
		<td class="created">
			<?php echo $group['Group']['created']; ?>
		</td>
		<td class="modified">
			<?php echo $group['Group']['modified']; ?>
		</td>
		<td class="name">
			<?php echo $group['Group']['name']; ?>
		</td>
		<td class="parent_id">
			<?php echo $group['Group']['parent_id']; ?>
		</td>
		<td class="lft">
			<?php echo $group['Group']['lft']; ?>
		</td>
		<td class="rght">
			<?php echo $group['Group']['rght']; ?>
		</td>
		<td class="actions">
			<?php
				if($group['Group']['deleted'] == 1){
					echo $html->link($html->image('admin/undo.gif'), array('action'=>'undelete', $group['Group']['id']), array('title' => __('Restore', true)), null, false) . "\n";
					echo $html->link($html->image('admin/delete.gif'), array('action'=>'hard_delete', $group['Group']['id']), array('title' => __('Delete', true)), sprintf(__('Are you sure you want to delete # %s?', true), $group['Group']['id']), false) . "\n";
				}else{
					echo $html->link($html->image('admin/view.gif'), array('action'=>'view', $group['Group']['id']), array('title' => __('View', true)), null, false) . "\n";
					echo $html->link($html->image('admin/edit.gif'), array('action'=>'edit', $group['Group']['id']), array('title' => __('Edit', true)), null, false) . "\n";
					echo $html->link($html->image('admin/trash.gif'), array('action'=>'delete', $group['Group']['id']), array('title' => __('Delete', true)), sprintf(__('Are you sure you want to delete # %s?', true), $group['Group']['id']), false) . "\n";
				}
			?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
<div class="paging" id="groupsPaging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>