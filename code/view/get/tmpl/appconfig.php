<?php
/**
 * User: elkuku
 * Date: 12.05.12
 * Time: 19:09
 */

$blacks = array('application', 'version', 'interface', 'gitBin', 'browserBin',);
?>

<h4><?= ucfirst($this->appName) ?></h4>
<ul>
	<? foreach ($this->appConfig as $k => $v) : ?>
	<? if (in_array($k, $blacks)) continue; ?>
	<li>
		<label for="<?= $k ?>"><?= ucfirst($k) ?></label>
		<input id="<?= $k ?>" name="<?= $k ?>" value="<?= $v ?>"/>
	</li>
	<? endforeach; ?>
</ul>
