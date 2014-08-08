<?php $this->load->view('friend/side_nav'); ?>
<section id="task_list" class="right">
	<?php if(is_array($friend_list)): ?>
	<table class="rowstyle-alt">
		<caption>Friends</caption>
		<thead>
			<tr>
				<th class="sortable">Username</th>
				<th class="sortable">Email</th>
				<th class="sortable">Groups</th>
			</tr>
		</thead>
		<tbody>
		
		<?php $i = 0 ?>
		<?php foreach ($friend_list as $friend): ?>
			<tr class="<?= ($i%2 != 0 ) ? 'alt ' : "";?>">
				<td><?= $friend['username'] ?></td>
				<td><?= $friend['email'] ?></td>
				<td><?= implode(', ', (array)$friend['groups']) ?></td>
			</tr>
			<?php $i++ ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else: ?>
	<h1>Friends</h1>
	<p>You currently have no friends :(</p>
	<?php endif; ?>
</section>