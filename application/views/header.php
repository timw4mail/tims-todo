
<head>
<?= $meta ?>
<title><?= $title ?></title>
	<link rel="icon" href="//todo.timshomepage.net/images/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="/fonts/Puritan/stylesheet.css" />
	<link rel="stylesheet" href="/css/todo.css" />
	<link rel="stylesheet" href="/css/message.css" />
	<link rel="stylesheet" href="/js/CLEditor/jquery.cleditor.css" />
	<link rel="stylesheet" href="/css/jquery-ui.min.css" />
<?php /* <?=  $css  ?> */ ?>

<?= $head_js ?>
</head>
<body <?= (!empty($body_id)) ? " id=\"" . $body_id . "\"" : ""; ?>>
<?php $this->load->view('menu') ?>
