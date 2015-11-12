<?php $this->load->view('task/side_nav'); ?>
<section id="task_add" class="right">
	<h1>Add Task</h1>
	<?= form_open('task/add') ?>
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
						<option>&nbsp;</option>
						<?= $cat_list ?>
					</select>
				</dd>

				<dt><label for="priority">Priority</label></dt>
				<dd>
					<select name="priority" id="priority">
						<?= $pri_list ?>
					</select>
				</dd>

				<dt><label for="due">Due date [YYYY-MM-DD] (0&nbsp;is no due date)</label></dt>
				<dd>
					<input type="text" name="due" id="due" value="<?= date('Y-m-d', $due) ?>" placeholder="YYYY-MM-DD" size="10" />
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
					<label> Minute:<input type="text" name="due_minute" id="due_minute" value="<?= date('i', $due) ?>" size="2" /></label>
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
				<dd><input type="checkbox" name="share" id="share" /></dd>
			</dl>
			<dl id="share_form">
				<dt>Share with:</dt>
				<dd>
					<label for="friend_share">Individual Friends</label>
					<input type="radio" name="share_type" value="friend" id="friend_share" />
					&nbsp;
					&nbsp;
					<label for="group_share">Groups of Friends</label>
					<input type="radio" name="share_type" value="group" id="group_share" />
				</dd>
				<?php if(is_array($friends)): ?>
				<dt class="friend_share"><strong>Friend Settings</strong></dt>
				<dd class="friend_share">
					<dl>
						<dt><label for="friend">Friends</label></dt>
						<dd>
							<select name="friend[]" id="group" multiple="multiple" size="5">
								<?php foreach ($friends as $friend): ?>
								<?php $uid = $this->session->userdata('uid'); ?>
								<?php $option_value = ($friend['uid'] == $uid) ? $friend['user_friend_id'] : $friend['uid'] ?>
								<option value="<?= $option_value ?>"><?= $friend['username'] ?></option>
								<?php endforeach ?>
							</select>
						</dd>
						<dt><label for="friend_perms">Permissions</label></dt>
						<dd>
							<select name="friend_perms" id="friend_perms">
								<option value="-1">No Access</option>
								<option value="0">Read-only Access</option>
								<option value="1">Comment-only Access</option>
								<option value="2">Comment and Checklist Access</option>
								<option value="3">Read and Write Access</option>
								<option value="9">Task Admin (Read/Write/Delete)</option>
							</select>
						</dd>
					</dl>
				</dd>
				<?php else: ?>
				<dd class="friend_share">
					You don't currently have any friends :(
				</dd>
				<?php endif ?>
				<?php if(is_array($groups)): ?>
				<dt class="group_share"><strong>Group-wide Settings</strong></dt>
				<dd class="group_share">
					<dl>
						<dt><label for="group">Groups</label></dt>
						<dd>
							<select name="group[]" id="group" multiple="multiple" size="5">
								<?php foreach ($groups as $group): ?>
								<option value="<?= $group['id'] ?>"><?= $group['name'] ?></option>
								<?php endforeach ?>
							</select>
						</dd>
						<dt><label for="group_perms">Permissions</label></dt>
						<dd>
							<select name="group_perms" id="group_perms">
								<option value="-1">No Access</option>
								<option value="0">Read-only Access</option>
								<option value="1">Comment-only Access</option>
								<option value="2">Comment and Checklist Access</option>
								<option value="3">Read and Write Access</option>
								<option value="9">Task Admin (Read/Write/Delete)</option>
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
					<input type="submit" name="add_sub" value="Add Task" />
				</dd>
			</dl>
		</fieldset>
	</form>
</section>