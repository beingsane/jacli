<?php defined('_JEXEC') || die(' =;)');
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 04:27
 */

$do = JFactory::getApplication()->input->get('do');
?>

<!DOCTYPE html>
<html>
<head>
	<title>JACLI - A Joomla! CLI</title>

	<link rel="stylesheet"
	      href="<?= JURI::root(true); ?>/template/css/bootstrap.css">
	<link rel="stylesheet"
	      href="<?= JURI::root(true); ?>/template/css/jacli.css">
	<style type="text/css">
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}
	</style>

	<script
		src="<?= JURI::root(true); ?>/template/js/mootools-core-1.4.5.js"></script>
	<script src="<?= JURI::root(true); ?>/template/js/jacli.js"></script>
</head>
<body>

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse"
			   data-target=".nav-collapse"> <span class="icon-bar"></span> <span
				class="icon-bar"></span> <span class="icon-bar"></span>
			</a> <a class="brand" href="<?= JURI::root() ?>index.php">JACLI - A
			Joomla! CLI</a>

			<div class="nav-collapse">
				<ul class="nav">
					<? $class = ('' == $do) ? 'active' : '' ?>
					<li class="<?= $class ?>"><a href="<?= JURI::root() ?>index.php">Home</a>
					</li>
					<? $class = ('deploy' == $do) ? 'active' : '' ?>
					<li class="<?= $class ?>"><a
						href="<?= JURI::root() ?>index.php?do=deploy">Deploy</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<!-- JacliApplicationOutput -->
	<footer class="footer">
		<p class="pull-right">
			<a href="#">Back to top</a>
		</p>

		<p>
			Made by <a href="https://github.com/elkuku">elkuku</a>.
		</p>
	</footer>
</div>

</body>
</html>
