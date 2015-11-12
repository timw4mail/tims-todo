<?php $this->load->view('friend/side_nav'); ?>
<?php $uid = $this->session->userdata('uid'); ?>
<?php $group_id = $this->uri->segment('3'); ?>
<section class="right">
	<h1>Manage Group</h1>
	<?= form_open("group/manage/" . (int)$group_id) ?>
	<fieldset>
		<legend>Group Members</legend>
		<dl>
			<dt><label for="group_name">Group Name:</label></dt>
			<dd>
				<input type="text" name="group_name" id="group_name" value="<?= $group_name ?>" size="10" />
			</dd>
			<dt><label for="friends">Friends in group:</label></dt>
			<dd>
				<select multiple="multiple" size="10" name="friends[]" id="friends">
				<?php if(is_array($friends)): ?>
				<?php foreach($friends as $friend) : ?>
					<?php $option_value = ($friend['uid'] == $uid) ? $friend['user_friend_id'] : $friend['uid'] ?>
					<option value="<?= $option_value ?>" <?= (in_array($option_value, $selected_friends)) ? 'selected="selected"' : '' ?>><?= $friend['username'] ?></option>
				<?php endforeach; ?>
				<?php endif ?>
					<option value="">&nbsp;</option>
				</select>
			</dd>
			<dt>&nbsp;</dt>
			<dd><input type="submit" name="group_sub" value="Save Changes" /></dd>
		</dl>
	</fieldset>
	</form>
</section>