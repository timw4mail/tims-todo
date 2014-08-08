<?php $this->load->view('task/side_nav'); ?>
<section id="task_add" class="right">
	<form action="<?= site_url('category/edit_sub');?>" method="post">
		<fieldset>
			<legend>Edit Category</legend>
			<dl>
				<dt><label for="title">Title</label></dt>
				<dd>
					<input type="text" name="title" id="title" value="<?= $cat['title'] ?>" placeholder="Category Heading" />
					<input type="hidden" value="<?= $this->uri->segment('4') ?>" name="id" />
				</dd>

				<dt><label for="desc">Description</label></dt>
				<dd>
					<textarea rows="10" cols="80" name="desc" id="desc" placeholder="Category details">
					<?= $cat['description'] ?>
					</textarea>
				</dd>

				<dt>&nbsp;</dt>
				<dd><input type="submit" name="edit_sub" value="Save Changes" /></dd>
			</dl>
		</fieldset>
	</form>
</section>