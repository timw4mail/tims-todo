<?php $link = $this->uri->segment(2) ?>
<aside id="left_nav" class="left">
	<nav>
		<ul>
			<li <?= ($link == FALSE) ? 'class="active"':"";?>>
				<span class="icon information"></span>
				<a href="<?= site_url('account')?>">Your Account</a>
			</li>
			<li <?= ($link == "password") ? 'class="active"' : "";?>>
				<span class="icon lock_edit"></span>
				<a href="<?= site_url('account/password')?>">Change Password</a>
			</li>
		</ul>
	</nav>
</aside>