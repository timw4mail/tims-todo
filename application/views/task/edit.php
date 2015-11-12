<?php $this->load->view('task/side_nav'); ?>
<section id="task_add" class="right">
	<h1>Edit Task</h1>
	<?php if($user_perms == PERM_ADMIN_ACCESS): ?>
	<p id="delTask"><a href="#" onclick="if(confirm('Are you sure you want to delete this task')){window.location='<?= site_url('task/delete').'/'.$this->security->xss_clean($this->uri->segment('3')) ?>'}">Delete Task</a></p>
	<?php endif ?>
	<?= form_open('task/edit' . '/' . (int)$this->uri->segment(3)); ?>

	<fieldset>
			<legend>Task</legend>
			<dl>
				<dt><label for="title">Title</label></dt>
				<dd>
					<input type="text" name="title" id="title" value="<?= $title ?>" placeholder="Task Heading" />
				</dd>

				<dt><label for="desc">Description</label></dt>
				<dd>
					<textarea rows="10" cols="80" name="desc" id="desc" placeholder="Task details"><?= $description ?></textarea>
				</dd>

				<dt><label for="category">Category</label></dt>
				<dd>
					<select name="category" id="category">
						<?= $cat_list ?>
					</select>
				</dd>

				<dt><label for="priority">Priority</label></dt>
				<dd>
					<select name="priority" id="priority">
						<?= $pri_list ?>
					</select>
				</dd>

				<dt><label for="status">Status</label></dt>
				<dd>
					<select name="status" id="status">
						<?= $stat_list ?>
					</select>
				</dd>

				<dt><label for="due">Due date [YYYY-MM-DD] (0 is no due date)</label></dt>
				<dd>
					<input type="text" name="due" id="due" value="<?= ($due != 0) ? date('Y-m-d', $due) : 0 ?>" placeholder="YYYY-MM-DD" size="10" />
					<label>Hour:
						<select name="due_hour">
							<option value="00"<?= (date('H', $due) == 0) ? ' selected="selected"' : "" ?>>Midnight</option>
							<option value="01"<?= (date('H', $due) == 1) ? ' selected="selected"' : "" ?>>1 AM</option>
							<option value="02"<?= (date('H', $due) == 2) ? ' selected="selected"' : "" ?>>2 AM</option>
							<option value="03"<?= (date('H', $due) == 3) ? ' selected="selected"' : "" ?>>3 AM</option>
							<option value="04"<?= (date('H', $due) == 4) ? ' selected="selected"' : "" ?>>4 AM</option>
							<option value="05"<?= (date('H', $due) == 5) ? ' selected="selected"' : "" ?>>5 AM</option>
							<option value="06"<?= (date('H', $due) == 6) ? ' selected="selected"' : "" ?>>6 AM</option>
							<option value="07"<?= (date('H', $due) == 7) ? ' selected="selected"' : "" ?>>7 AM</option>
							<option value="08"<?= (date('H', $due) == 8) ? ' selected="selected"' : "" ?>>8 AM</option>
							<option value="09"<?= (date('H', $due) == 9) ? ' selected="selected"' : "" ?>>9 AM</option>
							<option value="10"<?= (date('H', $due) == 10) ? ' selected="selected"' : "" ?>>10 AM</option>
							<option value="11"<?= (date('H', $due) == 11) ? ' selected="selected"' : "" ?>>11 AM</option>
							<option value="12"<?= (date('H', $due) == 12) ? ' selected="selected"' : "" ?>>12 Noon</option>
							<option value="13"<?= (date('H', $due) == 13) ? ' selected="selected"' : "" ?>>1 PM</option>
							<option value="14"<?= (date('H', $due) == 14) ? ' selected="selected"' : "" ?>>2 PM</option>
							<option value="15"<?= (date('H', $due) == 15) ? ' selected="selected"' : "" ?>>3 PM</option>
							<option value="16"<?= (date('H', $due) == 16) ? ' selected="selected"' : "" ?>>4 PM</option>
							<option value="17"<?= (date('H', $due) == 17) ? ' selected="selected"' : "" ?>>5 PM</option>
							<option value="18"<?= (date('H', $due) == 18) ? ' selected="selected"' : "" ?>>6 PM</option>
							<option value="19"<?= (date('H', $due) == 19) ? ' selected="selected"' : "" ?>>7 PM</option>
							<option value="20"<?= (date('H', $due) == 20) ? ' selected="selected"' : "" ?>>8 PM</option>
							<option value="21"<?= (date('H', $due) == 21) ? ' selected="selected"' : "" ?>>9 PM</option>
							<option value="22"<?= (date('H', $due) == 22) ? ' selected="selected"' : "" ?>>10 PM</option>
							<option value="23"<?= (date('H', $due) == 23) ? ' selected="selected"' : "" ?>>11 PM</option>
						</select>
					</label>
					<label> Minute:<input type="text" name="due_minute" id="due_minute" value="<?= date('i', $due)?>" size="2" /></label>
				</dd>
				<dt><label for="reminder">Email Reminder</label></dt>
				<dd><input type="checkbox" name="reminder" id="reminder" value="rem_true" <?= ($reminder == TRUE) ? 'checked="checked"': '';?> /></dd>
				<dt>&nbsp;</dt>
				<dd id="reminder_form">
					<label for="rem_hours">Hours</label>:<input type="text" name="rem_hours" id="rem_hours" size="2" value="<?= $rem_hours ?>" />
					<label for="rem_minutes">Minutes</label>:<input type="text" name="rem_minutes" id="rem_minutes" size="2" value="<?= $rem_minutes ?>" />
					before the task is due.
				</dd>
			</dl>
		</fieldset>
		<fieldset>
			<legend>Task Permissions</legend>
			<dl>
				<dt><label for="share">Share this task</label></dt>
				<dd><input type="checkbox" name="share" id="share" <?= ( ! empty($selected_groups) || ! empty($selected_friends)) ? 'checked="checked"' : '' ?> /></dd>
			</dl>
			<dl id="share_form">
				<dt>Share with:</dt>
				<dd>
					<label for="friend_share">Individual Friends</label>
					<input type="radio" name="share_type" value="friend" id="friend_share" <?= (empty($selected_friends)) ? "": 'checked="checked"' ?>/>
					&nbsp;
					&nbsp;
					<label for="group_share">Groups of Friends</label>
					<input type="radio" name="share_type" value="group" id="group_share" <?= (empty($selected_groups)) ? "": 'checked="checked"' ?> />
				</dd>
				<?php if( ! empty($friends)): ?>
				<?php if( ! is_array($selected_friends)) {$selected_friends = array();} ?>
				<dt class="friend_share"><strong>Friend Settings</strong></dt>
				<dd class="friend_share">
					<dl>
						<dt><label for="friend">Friends</label></dt>
						<dd>
							<select name="friend[]" id="friend" multiple="multiple" size="5">
								<?php foreach ($friends as $friend): ?>
								<?php $uid = $this->session->userdata('uid'); ?>
								<?php $option_value = ($friend['uid'] == $uid) ? $friend['user_friend_id'] : $friend['uid'] ?>
								<option value="<?= $option_value ?>" <?= (in_array($option_value, $selected_friends))?'selected="selected"':''?>><?= $friend['username'] ?></option>
								<?php endforeach ?>
							</select>
						</dd>
						<dt><label for="friend_perms">Permissions</label></dt>
						<dd>
						<select name="friend_perms" id="friend_perms">
							<option value="-1" <?= ($friend_perms === PERM_NO_ACCESS) ? 'selected="selected"':''?>>No Access</option>
							<option value="0" <?= ($friend_perms === PERM_READ_ACCESS) ? 'selected="selected"':''?>>Read-only Access</option>
							<option value="1" <?= ($friend_perms === PERM_COMMENT_ACCESS) ? 'selected="selected"':''?>>Comment-only Access</option>
							<option value="2" <?= ($friend_perms === PERM_CHECKLIST_ACCESS) ? 'selected="selected"':''?>>Comment and Checklist Access</option>
							<option value="3" <?= ($friend_perms === PERM_WRITE_ACCESS) ? 'selected="selected"':''?>>Read and Write Access</option>
							<option value="9" <?= ($friend_perms === PERM_ADMIN_ACCESS) ? 'selected="selected"':''?>>Task Admin (Read/Write/Delete)</option>
						</select>
						</dd>
					</dl>
				</dd>
				<?php else: ?>
				<dd class="friend_share">
					You don't currently have any friends :(
				</dd>
				<?php endif ?>
				<?php if( ! empty($groups)): ?>
				<?php if( ! is_array($selected_groups)) $selected_groups = array(); ?>
				<dt class="group_share"><strong>Group-wide Settings</strong></dt>
				<dd class="group_share">
					<dl>
						<dt><label for="group">Groups</label></dt>
						<dd>
							<select name="group[]" id="group" multiple="multiple" size="5">
								<?php foreach ($groups as $group): ?>
								<option value="<?= $group['id'] ?>" <?= (in_array($group['id'], $selected_groups))?'selected="selected"':''?>><?= $group['name'] ?></option>
								<?php endforeach ?>
							</select>
						</dd>
						<dt><label for="group_perms">Permissions</label></dt>
						<dd>
							<select name="group_perms" id="group_perms">
								<option value="-1" <?= ($group_perms == "-1") ? 'selected="selected"':''?>>No Access</option>
								<option value="0" <?= ($group_perms == "0") ? 'selected="selected"':''?>>Read-only Access</option>
								<option value="1" <?= ($group_perms == "1") ? 'selected="selected"':''?>>Comment-only Access</option>
								<option value="2" <?= ($group_perms == "2") ? 'selected="selected"':''?>>Comment and Checklist Access</option>
								<option value="3" <?= ($group_perms == "3") ? 'selected="selected"':''?>>Read and Write Access</option>
								<option value="9" <?= ($group_perms == "4") ? 'selected="selected"':''?>>Task Admin (Read/Write/Delete)</option>
							</select>
						</dd>
					</dl>
				</dd>
				<?php else: ?>
				<dd class="group_share">
					You need to create <a href="<?= site_url('friend/group/add') ?>">friend groups</a> before you can share tasks.
				</dd>
				<?php endif ?>
			</dl>
		</fieldset>
		<fieldset>
			<dl>
				<dt>&nbsp;</dt>
				<dd>
					<input type="hidden" name="task_id" value="<?= $this->uri->segment($this->uri->total_segments()) ?>" id="task_id" />
					<input type="submit" name="edit_sub" value="Update Task" />
				</dd>
			</dl>
		</fieldset>
	</form>
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1">Checklist</a></li>
			<li><a href="#tabs-2">Comments</a></li>
		</ul>
		<div id="tabs-1">
			<span id="toggle_checklist" class="add">Add Checklist item</span>
			<dl id="add_checklist_dl">
				<dt>&nbsp;</dt>
				<dd id="ajax_status">&nbsp;</dd>
				<dt><label for="check_desc">Checklist item:</label></dt>
				<dd>
					<input type="text" size="10" name="check_desc" id="check_desc" />&nbsp;&nbsp;
					<button name="add_checklist_item" id="add_checklist_item">Add Checklist Item</button>
					<?php /*<input type="button" name="add_checklist_item" id="add_checklist_item" value="Add Checklist Item" /> */ ?>
				</dd>
			</dl>
			<?php $this->load->view('task/checklist_view'); ?>
		</div>
		<div id="tabs-2">
			<span id="toggle_comments" class="add">Add Comment</span>
			<dl id="add_comment_dl">
				<dt>&nbsp;</dt>
				<dd id="ajax_status">&nbsp;</dd>
				<dt>&nbsp;</dt>
				<dd>
					<textarea rows="10" cols="80" name="comment" id="comment"></textarea>
					<br />
					<button name="add_task_comment" id="add_task_comment">Submit Comment</button>
					<?php /*<input type="button" name="add_task_comment" id="add_task_comment" value="Submit comment" />*/ ?>
				</dd>
			</dl>
			<?php $this->load->view('task/comments_view'); ?>
		</div>
	</div>
</section>