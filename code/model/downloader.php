<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 01:01
 */

class AcliModelDownloader extends JModelBase
{
	/**
	 * Check out from a git repository.
	 *
	 * @param string           $dir
	 * @param SimpleXMLElement $version
	 *
	 * @throws Exception
	 *
	 * @return \AcliModelDeploy
	 */
	public function checkoutGit($dir, SimpleXMLElement $version)
	{
		$git = $this->state->get('gitBin');

		/* @var JApplicationCli $application */
		$application = JFactory::getApplication();

		$parts = explode('/', $dir);

		//@todo OS specific
		$subDir = array_pop($parts);
		$path = implode('/', $parts);

		if (JFolder::exists($dir))
		{
			if ($application->input->get('updaterepo'))
			{
				$application->out('Updating git repository...');

				passthru("cd \"$dir\" && $git fetch origin && $git merge origin master");
			}
			else
			{
				$application->out('No update was requested (use --updaterepo)');
			}
		}
		else
		{
			if (!JFolder::create($path))
				throw new Exception(__METHOD__ . ' - can not create folder: ' . JError::getError());

			$application->out('Cloning git repository...');

			passthru("cd $path && $git clone $version->url $subDir", $status);

			if (0 != $status)
				throw new Exception(__METHOD__ . ' - checkout failed', $status);
		}

		return $this;
	}

	/**
	 * SVN checkout.
	 *
	 * @todo implement
	 */
	private function checkoutSVN()
	{
		echo 'Checking out the Joomla! trunk to:' . NL . $BASE . DS . $jTrunkDir . NL;
		$JSVN = 'http://joomlacode.org/svn/joomla/development/trunk';
		passthru('svn co ' . $JSVN . ' "' . $BASE . DS . $jTrunkDir . '"');

		/*
		 * SVN export
		 */
		echo "Exporting the Joomla! trunk to $theDir...";
		passthru('svn export "' . $BASE . DS . $jTrunkDir . '" "' . $BASE . DS . $theDir . '"');

	}

	/**
	 * @param $dir
	 * @param SimpleXMLElement $version
	 * @return AcliModelDownloader
	 * @throws Exception
	 */
	public function download($dir, SimpleXMLElement $version)
	{
		jimport('joomla.filesystem.file');

		if (JFolder::exists($dir))
			return $this;

		if (!JFolder::create($dir))
			throw new Exception(__METHOD__ . ' - can not create folder: ' . JError::getError());

		$wget = 'wget';

		passthru("cd \"$dir\" && $wget $version->url", $status);

		if (0 != $status)
			throw new Exception(__METHOD__ . ' - download failed', $status);

		$files = JFolder::files($dir);

		if (!$files)
			throw new Exception(__METHOD__ . ' - download failed', 2);

		$file = $files[0];

		switch (JFile::getExt($file))
		{
			case'zip':
				passthru("cd \"$dir\" && unzip $file", $status);

				if (0 != $status)
					throw new Exception(__METHOD__ . ' - download failed', $status);

				JFile::delete("$dir/$file");
				break;

			default:
				throw new Exception(__METHOD__ . ' - unknown file extension: ' . $file);
		}

		return $this;
	}

}
