<?php $html = new P3_Html() ?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width,initial-scale=1.0" />
<title><?php echo SITE_TITLE ?></title>
<link rel="stylesheet" href="//cdn.jsdelivr.net/bootstrap/2/css/bootstrap.min.css" />
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

<script src="//cdn.jsdelivr.net/g/jquery@2,bootstrap@2"></script>
<?php echo $html->script($this->baseUrl() . '/masters.js', '.') ?>

</body>
</html>
