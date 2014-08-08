<?php $this->load->view('friend/side_nav'); ?>
<section class="right">
	<?php if($this->session->userdata('username') !== 'guest'): ?>
	<form action="<?= site_url('group/add_sub');?>" method="post">
		<fieldset>
			<legend>Add Group</legend>
			<dl>
				<dt><label for="name">Name</label></dt>
				<dd>
					<input type="text" name="name" id="name" value="" placeholder="Group Name" />
				</dd>

				<dt>&nbsp;</dt>
				<dd><input type="submit" name="add_sub" value="Add Group" /></dd>
			</dl>
		</fieldset>
	</form>
	<?php endif ?>
	<table class="rowstyle-alt">
		<caption>Your Groups</caption>
		<thead>
			<tr>
				<th class="sortable">Name</th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; ?>
			<?php if(is_array($group)): ?>
			<?php foreach ($group as $c): ?>
			<tr <?= ($i%2 != 0 ) ? 'class="alt"' : "";?>>
				<td><span class="icon edit"></span><a href="<?= site_url('group/manage/' . $c['id']) ?>"><?= $c['name'] ?></a></td>
				<td>
					<input type="button" class="del_group" id="group_<?= $c['id'] ?>" value="Delete Group" />
				</td>
			</tr>
			<?php $i++?>
			<?php endforeach; ?>
			<?php else: ?>
			<tr>
				<td>You don't have any friend groups</td>
			</tr>
			<?php endif ?>
		</tbody>
	</table>
</section>