<?php
/**
 * User: elkuku
 * Date: 05.05.12
 * Time: 01:01
 */

class JacliModelDownloader extends JModelBase
{
	/**
	 * Check out from a git repository.
	 *
	 * @param string           $dir
	 * @param SimpleXMLElement $version
	 *
	 * @throws Exception
	 *
	 * @return \JacliModelDeploy
	 */
	public function checkoutGit($dir, SimpleXMLElement $version)
	{
		$git = $this->state->get('gitBin');

		/* @var JApplicationCli $application */
		$application = JFactory::getApplication();

		//@todo OS specific
		$parts = explode('/', $dir);

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
				throw new Exception(__METHOD__ . ' - can not create folder: ' . $path);

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
	 * @param string           $dir
	 * @param SimpleXMLElement $version
	 *
	 * @throws Exception
	 * @return \JacliModelDownloader
	 */
	public function checkoutSVN($dir, SimpleXMLElement $version)
	{
		/* @var JApplicationCli $application */
		$application = JFactory::getApplication();

		$application->out('Checking out from SVN...');

		if (JFolder::exists($dir))
		{
			if ($application->input->get('updaterepo'))
			{
				$application->out('Updating SVN repository...');

				passthru("cd \"$dir\" && svn up");
			}
			else
			{
				$application->out('No update was requested (use --updaterepo)');
			}
		}
		else
		{
			if (!JFolder::create($dir))
				throw new Exception(__METHOD__ . ' - can not create folder: ' . $dir);

			$application->out('Checking out SVN repository...');

			passthru("cd $dir && svn co $version->url repository", $status);

			if (0 != $status)
				throw new Exception(__METHOD__ . ' - Checkout failed', $status);

			$application->out('Exporting out SVN repository...');

			passthru("svn export $dir/repository $dir/export", $status);

			if (0 != $status)
				throw new Exception(__METHOD__ . ' - Export failed', $status);
		}

		return $this;
	}

	/**
	 * @param                  $dir
	 * @param SimpleXMLElement $version
	 *
	 * @return JacliModelDownloader
	 * @throws Exception
	 */
	public function download($dir, SimpleXMLElement $version)
	{
		jimport('joomla.filesystem.file');

		if (JFolder::exists($dir))
			return $this;

		if (!JFolder::create($dir))
			throw new Exception(__METHOD__ . ' - can not create folder: ' . $dir);

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
