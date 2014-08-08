<!DOCTYPE html>
<html>
<head>
<title>Database Error</title>
<style type="text/css">
*{
	margin:0;
	padding:0;
	font-family:'Puritan2.0Normal',serif;
}
/* Declare html5 elements as block-level elements */
section, header{
	display:block;
}
h1, p{
	display:block;
	width:80%;
	margin:0 auto;
}
section, header{
	background:#fff;
	background:rgba(255,255,255,.65);
	padding:.5%;
	margin:1em;
	-moz-border-radius:.5em;
	-webkit-border-radius:.5em;
	border-radius:.5em;
}

section{
	width:90%;
	margin:0 auto;
	text-align:justify;
}

h1{
	text-align:center;
}

html, body{
	background:url('/images/bgs/blue2.png');
	color:#005;
}

</style>
</head>
<body>
	<header>
	<figure>
		<img src="/images/todo.png" alt="Tim's ToDo" id="bannerImg" />
		<figcaption style="display:none;">Tim's Todo</figcaption>
	</figure>
	</header>
	<section>
		
		<h1><?php echo $heading; ?></h1>
		<p><?php echo $message; ?><br />
		<?php echo (isset($_SERVER['HTTP_REFERER'])) ? '<a href="'.$_SERVER['HTTP_REFERER'].'">Go back</a>' : '' ?></p>
	</section>
</body>
</html>