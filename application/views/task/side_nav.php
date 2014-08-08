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
			<li <?= ($link == "add") ? 'class="active"' : "";?>>
				<span class="icon add"></span>
				<a href="<?= site_url('task/add')?>">Add Task</a>
			</li>
			<li <?= ($this->uri->segment(1) == 'task' && $link == "list") ? 'class="active"' : "";?>>
				<span class="icon active_tasks"></span>
				<a href="<?= site_url('task/list')?>">Active Tasks</a>
			</li>
			<li <?= ($link == "shared") ? 'class="active"' : "";?>>
				<span class="icon group"></span>
				<a href="<?= site_url('task/shared') ?>">Shared Tasks</a>
			</li>
			<li <?= ($link == "overdue") ? 'class="active"' : "";?>>
				<span class="icon immediate"></span>
				<a href="<?= site_url('task/overdue')?>">Overdue Tasks</a>
			</li>
			<li <?= ($link == "archive") ? 'class="active"' : "";?>>
				<span class="icon archive"></span>
				<a href="<?= site_url('task/archive')?>">Archived Tasks</a>
			</li>
			<li <?= ($link == "calendar") ? 'class="active"' : "";?>>
				<span class="icon calendar"></span>
				<a href="<?= site_url('task/calendar')?>">Task Calendar</a>
			</li>
			<li <?= ($this->uri->segment(1) == 'category' && $link == "list") ? 'class="active"' : "";?>>
				<psan class="icon cat"></psan>
				<a href="<?= site_url('category/list')?>">Task Categories</a>
			</li>
		</ul>
	</nav>
</aside>