<?php if( ! empty($err)) : ?>
<div class="err_wrap">
	Fix the following errors:
<?php foreach ($err as $e): ?>
	<p class="error"><?= $e ?></p>
<?php endforeach;?>
</div>
<?php endif; ?>
	<section class="left">
		<h1>What is Tim's Todo?</h1>
		<p>Tim's Todo is a task manager that allows you to...</p>
		<ul class="bulleted">
			<li>Create, edit, and categorize tasks</li>
			<li>Send email reminders that a task is due</li>
			<li>Share tasks with friends</li>
			<li>Create task checklists</li>
			<li>Comment on your tasks, and those shared with you</li>
		</ul>
		<p>If it sounds interesting, or useful, sign up and try it out.</p>
		<p><strong>Want to try it without creating an account? Login with username: <em>guest</em> and password: <em>guest</em></strong></p>
		<p>It's nice to have feedback. Send suggestions/comments/criticism to <a href="mailto:tim@timshomepage.net?subject=Tim's Todo Feedback">tim (at) timshomepage.net</a>.</p>
	</section>
	<section class="right">
	<?= form_open('login') ?>
		<fieldset>
			<legend>Login</legend>
			<dl>
				<dt><label for="user">Email or Username:</label></dt>
				<dd>
					<input type="text" name="user" id="user"
						   required="required" value="" size="10" />
				</dd>
				<dt><label for="pass">Password:</label></dt>
				<dd>
					<input type="password" name="pass" id="pass"
						   required="required" value="" size="10" />
				</dd>
				<dt>&nbsp;</dt>
				<dd>
					<input type="submit" name="login_sub" value="Login" />
				</dd>
			</dl>
		</fieldset>
	</form>
		<br />
		<br />
		Don't have an account? <a href="<?=site_url('register');?>">Register</a>
	</section>

