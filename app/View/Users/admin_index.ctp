<div class="users index">
	<h2><?php echo __('Users');?></h2>
	<table cellpadding="0" cellspacing="0" class="table table-striped table-condensed">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('org');?></th>
			<th><?php echo $this->Paginator->sort('role_id', 'Role');?></th>
			<th><?php echo $this->Paginator->sort('email');?></th>
			<th><?php echo $this->Paginator->sort('autoalert');?></th>
			<th><?php echo $this->Paginator->sort('contactalert');?></th>
			<th><?php echo $this->Paginator->sort('gpgkey');?></th>
			<th><?php echo $this->Paginator->sort('nids_sid');?></th>
			<th><?php echo $this->Paginator->sort('termsaccepted');?></th>
			<th><?php echo $this->Paginator->sort('newsread');?></th>
			<th class="actions"><?php echo __('Actions');?></th>
	</tr>
	<?php
foreach ($users as $user): ?>
	<tr>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo h($user['User']['id']); ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo h($user['User']['org']); ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo $this->Html->link($user['Role']['name'], array('controller' => 'roles', 'action' => 'view', $user['Role']['id'])); ?></td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo h($user['User']['email']); ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo $user['User']['autoalert']? 'Yes' : 'No'; ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo $user['User']['contactalert']? 'Yes' : 'No'; ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo $user['User']['gpgkey']? 'Yes' : 'No'; ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo h($user['User']['nids_sid']); ?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php
	if (h($user['User']['termsaccepted']) == 1) {
				echo "Yes";
	} else {
				echo "No";
	}
		?>&nbsp;</td>
		<td class="short" onclick="document.location ='<?php echo $this->Html->url(array('admin' => true, 'action' => 'view', $user['User']['id']), true);?>';">
		<?php echo h($user['User']['newsread']); ?>&nbsp;</td>
		<td class="actions">
			<?php
	if (($isAclAdmin && (($user['User']['org'] == $me['org'])) || ('1' == $me['id'])) || ($me['org'] == 'ADMIN')) {
				echo $this->Html->link(__('Edit'), array('admin' => true, 'action' => 'edit', $user['User']['id']), array('class' => 'btn'));
				echo $this->Form->postLink(__('Delete'), array('admin' => true, 'action' => 'delete', $user['User']['id']), array('class' => 'btn'), __('Are you sure you want to delete # %s?', $user['User']['id']));
	}?>
			<?php echo $this->Html->link(__('View'), array('admin' => true, 'action' => 'view', $user['User']['id']), array('class' => 'btn')); ?>
		</td>
	</tr>
	<?php
endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>

	<div class="pagination">
		<ul>
		<?php
			echo $this->Paginator->prev('&laquo; ' . __('previous'), array('tag' => 'li', 'escape' => false), null, array('tag' => 'li', 'class' => 'prev disabled', 'escape' => false, 'disabledTag' => 'span'));
			echo $this->Paginator->numbers(array('modulus' => 20, 'separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'span'));
			echo $this->Paginator->next(__('next') . ' &raquo;', array('tag' => 'li', 'escape' => false), null, array('tag' => 'li', 'class' => 'next disabled', 'escape' => false, 'disabledTag' => 'span'));
		?>
		</ul>
	</div>
</div>
<div class="actions">
	<ul>
		<?php echo $this->element('actions_menu'); ?>
	</ul>
</div>