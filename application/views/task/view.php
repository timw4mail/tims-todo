<?php $this->load->view('task/side_nav'); ?>
<section id="task_view" class="right">
	<h1><?= $title ?></h1>
	<input type="hidden" name="task_id" value="<?= $this->uri->segment($this->uri->total_segments()) ?>" id="task_id" />
	<input type="hidden" name="user_perms" value="<?= $user_perms ?>" id="user_perms" />

	<?php if($user_perms <= PERM_CHECKLIST_ACCESS): ?>
	<input type="hidden" id="status" value="<?= $status_id ?>" />
	<?php endif; ?>

	<?php if($user_perms > PERM_CHECKLIST_ACCESS): ?>
	<p id="editTask">
		<?php if($user_perms == PERM_ADMIN_ACCESS): ?>
		<a href="#" id="delTask" onclick="if(confirm('Are you sure you want to delete this task')){window.location='<?= site_url('task/delete').'/'.$this->security->xss_clean($this->uri->segment('3')) ?>'}">Delete Task</a>
		<?php endif ?>
		<a id="editTaskIcon" href="<?= site_url('task/edit').'/'.$task ?>">Edit Task</a>

	</p>
	<?php endif ?>
	<dl>
		<dt>Created</dt>
		<dd><?= date('D M d, Y g:iA T',$created) ?> by <strong><?= $username ?></strong></dd>

		<dt>Due</dt>
		<dd><?= ($due != 0 ) ? date('D M d, Y g:iA T', $due) : "N/A"; ?></dd>

		<dt>Priority</dt>
		<dd>
			<span class="priority <?= strtolower($priority) ?>"><?= $priority ?></span>
		</dd>

		<dt>Status</dt>
		<dd>
			<?php if($user_perms > PERM_CHECKLIST_ACCESS): ?>
			<form action="#" method="post">
				<select name="status" id="status">
					<?= $status ?>
				</select>
			</form>
			<?php else: ?>
			<?= $current_status ?>
			<?php endif ?>
		</dd>

		<dt>Modified</dt>
		<dd><?= ($modified < 1) ? 'N/A' : date('D M d, Y g:iA T', $modified); ?></dd>

		<dt>Category</dt>
		<dd>
			<?php if($user_perms > PERM_CHECKLIST_ACCESS): ?>
			<form action="#" method="post">
				<select name="category" id="category">
					<?= $category ?>
				</select>
			</form>
			<?php else: ?>
			<?= $cat_name ?>
			<?php endif ?>
		</dd>

		<dt>Task Description</dt>
		<dd id="task_desc">
			<?= $description ?>
		</dd>

		<?php if(is_array($selected_groups)): ?>
		<dt>Task Groups</dt>
		<dd></dd>
		<?php endif ?>
	</dl>
	<?php if($user_perms > PERM_READ_ACCESS): ?>
	<div id="tabs">
		<ul>
			<?php if($user_perms > PERM_COMMENT_ACCESS): ?>
			<li><a href="#tabs-1">Checklist</a></li>
			<?php endif ?>
			<li><a href="#tabs-2">Comments</a></li>
		</ul>
		<?php if($user_perms > PERM_COMMENT_ACCESS): ?>
		<div id="tabs-1">
			<?php if ($user_perms > PERM_CHECKLIST_ACCESS): ?>

			<span id="toggle_checklist"><span class="icon add"></span>Add Checklist item</span>
			<dl id="add_checklist_dl">
				<?php /*<dt>&nbsp;</dt>
				<dd id="ajax_status">&nbsp;</dd> */ ?>
				<dt><label for="check_desc">Checklist item:</label></dt>
				<dd>
					<input type="text" size="10" name="check_desc" id="check_desc" />&nbsp;&nbsp;
					<input type="button" name="add_checklist_item" id="add_checklist_item" value="Add Checklist Item" />
				</dd>
			</dl>
			<?php endif ?>
			<?php $this->load->view('task/checklist_view'); ?>
		</div>
		<?php endif ?>
		<div id="tabs-2">
			<span id="toggle_comments"><span class="icon add"></span>Add Comment</span>
			<dl id="add_comment_dl">
				<?php /* <dt>&nbsp;</dt>
				<dd id="ajax_status">&nbsp;</dd> */ ?>
				<dt>&nbsp;</dt>
				<dd>
					<textarea rows="10" cols="80" name="comment" id="comment"></textarea>
					<br />
					<input type="button" name="add_task_comment" id="add_task_comment" value="Submit comment" />
				</dd>
			</dl>
			<?php $this->load->view('task/comments_view'); ?>
		</div>
	</div>
	<?php endif ?>
</section>