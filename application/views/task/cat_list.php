<?php $this->load->view('task/side_nav'); ?>
<section class="right">
	<?= form_open('category/add_sub'); ?>
		<fieldset>
			<legend>Add Category</legend>
			<dl>
				<dt><label for="title">Title</label></dt>
				<dd>
					<input type="text" name="title" id="title" value="" placeholder="Category Heading" />
				</dd>

				<dt><label for="desc">Description</label></dt>
				<dd>
					<textarea rows="10" cols="80" name="desc" id="desc" placeholder="Category details"></textarea>
				</dd>

				<dt>&nbsp;</dt>
				<dd><input type="submit" name="add_sub" value="Add Category" /></dd>
			</dl>
		</fieldset>
	</form>
	<table class="rowstyle-alt">
		<caption>Categories</caption>
		<thead>
			<tr>
				<?php if($this->session->userdata('num_format') != -1): ?>
				<th class="sortable">ID</th>
				<?php endif ?>
				<th class="sortable">Name</th>
				<th class="sortable">Description</th>
				<th>&nbsp;</th>
				<th class="sortable">Type</th>
			</tr>
		</thead>
		<tbody>
			<?php $i = 1; ?>
			<?php foreach ($category as $c): ?>
			<tr <?= ($i%2 != 0 ) ? 'class="alt"' : "";?>>
				<?php if($this->session->userdata('num_format') != -1): ?>
				<td class="id"><?= ($this->session->userdata('num_format') == 1) ? $this->todo->kanji_num($c['id']) : $c['id']; ?></td>
				<?php endif ?>
				<td>
				<?php if($c['group_id'] != 0): ?>
					<a class="edit" href="<?= site_url('category/edit/'. $c['id']) ?>"><?= $c['title'] ?></a>
				<?php else: ?>
					<?= $c['title'] ?>
				<?php endif ?>
				</td>
				<td><?= $c['description'] ?></td>
				<td>
				<?php if($c['group_id'] != 0): ?>
					<input type="button" class="del_cat" id="cat_<?=$c['id']?>" value="Delete Category" />
				<?php else: ?>
					&nbsp;
				<?php endif ?>
				</td>
				<td><?= ($c['group_id'] == 0) ? "Public" : "Private"; ?></td>
			</tr>
			<?php $i++?>
			<?php endforeach; ?>
		
		</tbody>
	</table>
</section>