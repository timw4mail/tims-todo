<?php if(is_array($err)) : ?>
<div class="err_wrap">
	Fix the following errors:
<?php foreach ($err as $e): ?>
	<p class="error"><?= $e ?></p>
<?php endforeach;?>
</div>
<?php endif; ?>
<section class="left">
	Already have an account? <a href="<?=site_url('login')?>">Login</a>

</section>
<section class="right">
<?= form_open('register') ?>
	<fieldset>
		<legend>Create an account</legend>
		<dl>
			<dt><label for="user">Username:</label></dt>
			<dd>
				<input type="text" name="user" id="user" 
					   required="required" value="<?= set_value('user') ?>" size="10" />
			</dd>
			<dt><label for="pass">Password:</label></dt>
			<dd>
				<input type="password" name="pass" id="pass" 
					   required="required" value="" size="10" />
				<input type="password" name="pass1" id="pass1" 
					   required="required" value="" size="10" />
			</dd>
			<dt><label for="email">Email:</label></dt>
			<dd>
				<input type="email" name="email" id="email" 
					   required="required" value="<?= set_value('email') ?>" size="10" />
			</dd>
			<dt>&nbsp;</dt>
			<dd>
				<input type="submit" name="reg_sub" value="Register" />
			</dd>
		</dl>
	</fieldset>
</form>
</section>
