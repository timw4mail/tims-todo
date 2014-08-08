<?php $this->load->view('friend/side_nav'); ?>
<section id="task_list" class="right">
	<?php if(is_array($request_list)): ?>
	<table class="rowstyle-alt">
		<caption>Friend Requests</caption>
		<thead>
			<tr>
				<th class="sortable">Username</th>
				<th class="sortable">Email</th>
				<th class="sortable">Accept/Reject</th>
			</tr>
		</thead>
		<tbody>
		
		<?php $i = 0 ?>
		<?php foreach ($request_list as $request): ?>
			<tr class="<?= ($i%2 != 0 ) ? 'alt ' : "";?>">
				<td><?= $request['username'] ?></td>
				<td><?= $request['email'] ?></td>
				<td>
					<input type="button" class="accept_request" id="af_<?= $request['user_id'] ?>" value="Accept Friend Request" />
					<input type="button" class="reject_request" id="rf_<?= $request['user_id'] ?>" value="Reject Friend Request" />
				</td>
			</tr>
			<?php $i++ ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php else: ?>
	<h1>Friend Requests</h1>
	<p>You currently have no requests.</p>
	<?php endif; ?>
</section>