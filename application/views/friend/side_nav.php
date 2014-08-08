<?php if(isset($err)): ?>
<?php if(is_array($err)) : ?>
<div class="err_wrap">
	Fix the following errors:
<?php foreach ($err as $e): ?>
	<p class="error"><?= $e ?></p>
<?php endforeach;?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $link = $this->uri->segment(2) ?>
<aside id="left_nav" class="left">
	<nav>
		<ul>
			<li <?= ($link == "" || $link == "list") ? 'class="active"' : "";?>>
				<span class="icon status_online"></span>
				<a href="<?= site_url('friend/list')?>">Your Friends</a>
			</li>
			<li <?= ($link == "find") ? 'class="active"' : "";?>>
				<span class="icon search"></span>
				<a href="<?= site_url('friend/find')?>">Find Friends</a>
			</li>
			<li <?= ($link == "requests") ? 'class="active"' : "";?>>
				<span class="icon user_comment"></span>
				<a href="<?= site_url('friend/requests')?>">Friend Requests</a>
				<?php if($this->todo->get_friend_requests() > 0): ?>
				<span id="side_num_requests"><?= $this->todo->get_friend_requests() ?></span>
				<?php endif; ?>
			</li>
			<li <?= ($link == "manage") ? 'class="active"' : "";?>>
				<span class="icon group"></span>
				<a href="<?= site_url('group/manage')?>">Friend Groups</a>
			</li>
		</ul>
	</nav>
</aside>