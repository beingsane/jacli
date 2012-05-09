<?php defined('_JEXEC') || die(' =;)');
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 04:27
 */
?>

<!DOCTYPE html>
<html>
<head>
	<title>JACLI - A Joomla! CLI</title>

	<link rel="stylesheet" href="<?= JURI::root(true); ?>/template/css/bootstrap.css">
	<link rel="stylesheet" href="<?= JURI::root(true); ?>/template/css/jacli.css">
	<style type="text/css">
		body {
			padding-top: 60px;
			padding-bottom: 40px;
		}
	</style>

	<script src="<?= JURI::root(true); ?>/template/js/mootools-core-1.4.5.js"></script>
	<script src="<?= JURI::root(true); ?>/template/js/jacli.js"></script>
</head>
<body>

<div class="navbar navbar-fixed-top">
	<div class="navbar-inner">
		<div class="container">
			<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</a>

			<a class="brand" href="<?= JURI::root() ?>index.php">JACLI - A Joomla! CLI</a>

			<div class="nav-collapse">
				<ul class="nav">
					<li class="active"><a href="#">Deploy</a></li>
					<li><a href="#about">About</a></li>
					<li><a href="#contact">Contact</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>

<div class="container">
	<h1>Deploy</h1>

	<? // @todo move
	if ($this->input->get('target')):
		$model = new AcliModelDeploy($this->config);

		$model->deploy();
	endif;
	?>

	<form action="<?= JURI::current(); ?>">
		<div class="row-fluid">

			<div class="span6">
				<div class="well">

					<ul>
						<li>
							<h2 id="appLabel">Application</h2> <?= $this->lists['appversion'] ?>
						</li>
						<li>
							<label>Target folder </label><input type="text" name="target"/>
						</li>
						<li><h3>Primary Configuration</h3></li>
						<? foreach ($this->cfg as $key => $v) : ?>
						<li>
							<label for="<?= $key ?>"><?= ucfirst($key) ?></label>
							<input id="<?= $key ?>" name="<?= $key ?>" value="<?= $v ?>"/>
						</li>
						<? endforeach; ?>
					</ul>
				</div>
			</div>

			<div class="span6">
				<div id="appConfig" class="well hero-unit">
					<!-- Application configuration -->
				</div>

			</div>
		</div>
		<div class="buttonbox well">
			<input type="submit" class="btn btn-primary"/>
		</div>

	</form>

	<footer class="footer">
		<p class="pull-right"><a href="#">Back to top</a></p>

		<p>Made by <a href="https://github.com/elkuku">elkuku</a>.</p>
	</footer>
</div>

</body>
</html>
