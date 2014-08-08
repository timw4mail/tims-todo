
<?php if (isset($err)) : ?>
<?php if(is_array($err)) : ?>
<div class="err_wrap">
	Fix the following errors:
<?php foreach ($err as $e): ?>
	<p class="error"><?= $e ?></p>
<?php endforeach;?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $this->load->view('account/side_nav'); ?>
<section class="right">
<form action="<?= site_url('account/password'); ?>" method="post">
	<fieldset>
		<legend>Change Password</legend>
		<dl>
			<dt><label for="user">New Password:</label></dt>
			<dd>
				<input type="password" name="pass" id="pass"
					   required="required" value="" size="10" />
					   
				<input type="password" name="pass1" id="pass1"
					   required="required" value="" size="10" />
			</dd>
			<dt><label for="pass">Old Password:</label></dt>
			<dd>
				<input type="password" name="old_pass" id="old_pass"
					   required="required" value="" size="10" />
			</dd>
			<dt>&nbsp;</dt>
			<dd>
				<input type="submit" name="pass_sub" value="Change Password" />
			</dd>
		</dl>
	</fieldset>
</form>
</section>