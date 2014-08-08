<header>
	<figure class="left">
		<img src="/images/todo.png" alt="Tim's ToDo" id="bannerImg" height="34" />
		<figcaption style="display:none;">Tim's Todo</figcaption>
	</figure>
	<?php if ($this->session->userdata('uid') != FALSE): ?>
	<nav id="header_nav" class="right">
		<ul>
			<li <?= ($this->uri->segment('1') == 'task') ? 'class="active"' : "";?>>
				<a href="<?= site_url('task/list');?>">Tasks</a>
			</li>
			<li <?= ($this->uri->segment('1') == 'account') ? 'class="active"' : "";?>>
				<a href="<?=site_url('account');?>">Account</a>
			</li>
			<li <?= ($this->uri->segment('1') == 'friend') ? 'class="active"' : "";?>>
				<a href="<?= site_url('friend/list');?>">Friends</a>
				<?php if($this->todo->get_friend_requests() > 0): ?>
				<span id="num_requests"><?= $this->todo->get_friend_requests() ?></span>
				<?php endif; ?>
			</li>
			<li><a href="<?= site_url('logout');?>">Logout</a></li>
		</ul>
	</nav>
	<?php endif; ?>
</header>
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
<?php if($this->session->flashdata('message') != FALSE): ?>
<?= $this->page->set_message($this->session->flashdata('message_type'), $this->session->flashdata('message'), TRUE); ?>
<?php endif ?>
<div class="wrap">
