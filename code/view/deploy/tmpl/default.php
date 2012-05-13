<?php
/**
 * User: elkuku
 * Date: 12.05.12
 * Time: 13:57
 */

?>

<h1>Deploy</h1>

<form action="<?= JURI::current(); ?>">

	<div class="row-fluid">

		<div class="span6">
			<div class="well">

				<ul>
					<li>
						<h2 id="appLabel">Application</h2> <?= $this->lists['appversion'] ?>
					</li>
					<li>
						<label for="target">Target folder </label><input type="text" id="target" name="target"/>
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
			<div class="well">
				<h3>Application configuration</h3>

				<p>This values will override the primary configuration.</p>

				<div id="appConfig">
					<!-- Application configuration -->
					Please select an application
				</div>
			</div>

		</div>
	</div>
	<div class="buttonbox well">
		<input type="submit" class="btn btn-primary"/>
	</div>

	<input type="hidden" name="do" value="deploy">

</form>

