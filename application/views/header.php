
<head>
<?= $meta ?>
	<link rel="icon" href="//todo.timshomepage.net/images/favicon.ico" type="image/x-icon" />
<?= $css ?>
	<title><?= $title ?></title>
<?= $head_js ?>
</head>
<body <?= (!empty($body_id)) ? " id=\"" . $body_id . "\"" : ""; ?>>
<?php $this->load->view('menu') ?>
