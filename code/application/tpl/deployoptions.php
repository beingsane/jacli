<?php defined('_JEXEC') || die(' =;)');
/**
 * User: elkuku
 * Date: 04.05.12
 * Time: 04:27
 */

//var_dump($_REQUEST);
//var_dump($cfg);

$cfg = $this->config->toObject();

?>
<!DOCTYPE html>
<html>
<head>
	<title>JApplicationWeb - Detect Client</title>
</head>
<body>

<div>
	<h1>JACLI - A Joomla! CLI</h1>

	<?php
	if ($this->input->get('target')):
		$model = new AcliModelDeploy($this->config);

		$model->deploy();
	else:
		echo '<p>Please select a target.</p>';
	endif;
	?>

	<form action="<?php echo JURI::current(); ?>">

		<div class="important">
			<label>Target: </label><input type="text" name="target"/>
		</div>

		<ul>
			<?php
			foreach ($cfg as $key => $v) :
				if (in_array($key, array('target', 'execution', 'uri'))):
					continue;
				endif;

				echo '<li>';

				if (is_object($v) || is_array($v)):
					echo $key;

					echo '<ul>';

					if (!count($v)):
						echo '<li>(empty)</li>';
					endif;

					foreach ($v as $k1 => $v1):
						echo '<li><label>' . $k1 . '</label><input name="' . $k1 . '" value="' . $v1 . '" /></li>';
					endforeach;

					echo '</ul>';
				else:
					echo '<label>' . $key . '</label><input name="' . $key . '" value="' . $v . '" />';
				endif;

				echo '</li>';
			endforeach;
			?>
		</ul>

		<input type="submit" value="Submit"/>
	</form>
</div>

</body>
</html>
