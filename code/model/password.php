<?php
/**
 * User: elkuku
 * Date: 03.05.12
 * Time: 16:21
 */

/**
 *
 */
class AcliModelPassword extends JModelBase
{
	/**
	 * Formats a password using the current encryption.
	 *
	 * @param string $plaintext The plaintext password to encrypt.
	 * @param string $salt      The salt to use to encrypt the password. []
	 *
	 * @return string The encrypted password.
	 */
	public static function getCryptedPassword($plaintext, $salt = '')
	{
		return md5($plaintext . $salt);
	}

	/**
	 * Generate a random password.
	 *
	 * @param    int        $length    Length of the password to generate
	 *
	 * @return    string            Random Password
	 */
	public static function genRandomPassword($length = 8)
	{
		$salt = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$len = strlen($salt);
		$makepass = '';

		$stat = @stat(__FILE__);

		if (empty($stat) || !is_array($stat))
			$stat = array(php_uname());

		mt_srand(crc32(microtime() . implode('|', $stat)));

		for ($i = 0; $i < $length; $i++)
		{
			$makepass .= $salt[mt_rand(0, $len - 1)];
		}

		return $makepass;
	}

}
