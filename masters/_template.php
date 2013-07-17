<?php
$jQuery = '1.10.2';
$bootstrap = '2.3.2';
$fontAwesome = '3.2.1';

$html = new P3_Html();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title><?php echo SITE_TITLE ?></title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/<?php echo $bootstrap ?>/css/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/<?php echo $bootstrap ?>/css/bootstrap-responsive.min.css" />
<link rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootswatch/<?php echo $bootstrap ?>/cerulean/bootstrap.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/<?php echo $fontAwesome ?>/css/font-awesome.min.css" />
<!--[if IE 7]>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/<?php echo $fontAwesome ?>/css/font-awesome-ie7.css" />
<![endif]-->
<?php echo $html->css($this->baseUrl() . '/masters.css', '.') ?>
</haed>

<body>
<div class="navbar navbar-fixed-top">
<div class="navbar-inner">
<span class="brand" style="padding-left: 50px"><?php echo SITE_TITLE ?></span>
<ul class="nav">
<?php
global $tables;

foreach ($tables as $table => $prop) {
	if ($this->url(0) == $table) {
		$class = 'active';
	} else {
		$class = '';
	}
	
	$url = $this->baseUrl() . '/' . $table;
	$label = arrayValue('label', $prop, $table);
	echo '<li class="' . $class . '"><a href="' . $url . '">' . "$label</a></li>\n";
}
?>
</ul>
</div><!-- .navbar-inner -->
</div><!-- .navbar -->

<div id="container" class="container-fluid"><?php echo $content ?></div>

<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/<?php echo $jQuery ?>/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/<?php echo $bootstrap ?>/js/bootstrap.min.js"></script>
<?php echo $html->script($this->baseUrl() . '/masters.js', '.') ?>

</body>
</html>
