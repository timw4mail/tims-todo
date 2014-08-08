<?php $this->load->view('task/side_nav'); ?>
<section id="task_list" class="right">
	<?php if(is_array($task_list)): ?>
	<table class="rowstyle-alt">
		<caption><?= $list_type ?> Tasks</caption>
		<thead>
			<tr>
				<?php if($this->session->userdata('num_format') != -1): ?>
				<th class="sortable">Id</th>
				<?php endif ?>
				<th class="sortable">Task</th>
				<th class="sortable">Status</th>
				<th class="sortable">Category</th>
				<th class="sortable">Priority</th>
				<th class="sortable">Due</th>
				<th class="sortable"><?= ($this->uri->segment(2) != 'archive') ? "Modified" : "Completed" ?></th>
			</tr>
		</thead>
		<tbody>
		
		<?php $i = 0 ?>
		<?php foreach ($task_list as $task): ?>
			<?php if (!is_array($task)){continue;} ?>
			<tr class="<?= ($i%2 != 0 ) ? 'alt ' : "";?><?= ($task['overdue'] == TRUE) ? 'overdue' : ''?>">
				<?php if($this->session->userdata('num_format') != -1): ?>
				<td class="id"><?= ($this->session->userdata('num_format') == 1) ? $this->todo->kanji_num($task['id']) : $task['id']; ?></td>
				<?php endif ?>
				<td class="taskTitle">
					<?php if($this->uri->segment(2) != 'archive' && $this->uri->segment(2) != 'shared'): ?>
					<a style="float:right;" href="<?= site_url('task/edit') .'/'. $task['id'];?>" title="Edit this task"><img src="/images/icons/pencil.png" alt="Edit" /></a>
					<?php endif ?>
					<a href="<?= site_url('task/view') .'/'. $task['id'];?>" title="View this task"><?= $task['title']; ?></a>
				</td>
				<td><?= $task['status']; ?></td>
				<td><?= $task['category'] ?></td>
				<td class="priority <?= strtolower($task['priority'])?>"><?= $task['priority'] ?></td>
				<td><?= ($task['due'] != 0 ) ? date('D M d, Y g:iA', $task['due']) : "N/A" ?></td>
				<td><?= ($task['modified'] < 1) ? 'N/A' : date('D M d, Y g:iA', $task['modified']); ?></td>
			</tr>
			<?php $i++ ?>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php if(isset($pagination) && !empty($pagination)): ?>
	<section id="pagination">
		<?= $this->pagination->create_links(); ?>
	</section>
	<?php endif ?>
	<?php elseif(count($task_list) < 1): ?>
	<h1><?= $list_type ?> Tasks</h1>
	<p>You currently have no <?= $list_type ?> tasks.</p>
	<?php endif; ?>
</section>