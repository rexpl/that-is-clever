<?php 

namespace Clever\Library\App;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Exception\EnvironmentIsBrokenException;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;

class Encryption
{
	private $encryptionKey;

	/**
	 * If user already login.
	 *
	 * @param string $key
	 * 
	 * @return void
	 */
	public function __construct($key=null)
	{
		if ($key) $this->encryptionKey = Key::loadFromAsciiSafeString($key);
	}


	/**
	 * Makes a protected by password key.
	 * 
	 * @param string $password
	 * 
	 * @return protected key (string)
	 */
	public function makeProtectedKey($password) {

		$protected_key = KeyProtectedByPassword::createRandomPasswordProtectedKey($password);
		$this->encryptionKey = $protected_key->unlockKey($password);
		return $protected_key->saveToAsciiSafeString();
	}


	/**
	 * Unlocks the protected key.
	 * 
	 * @param string $protected_key
	 * @param string $password
	 * 
	 * @return encryption key
	 */
	public function unlockKey($protected_key, $password)
	{
		$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key);
		$this->encryptionKey = $protected_key->unlockKey($password);

		return $this->encryptionKey->saveToAsciiSafeString();
	}


	/**
	 * Change the password of the given protected key.
	 * 
	 * @param string $password
	 * @param string $newPassword
	 * 
	 * @return Updated protected key (string)
	 */
	public function updateProtectedKey($password, $newPassword)
	{
		$this->encryptionKey->changePassword($password, $newPassword);

		return $this->encryptionKey->saveToAsciiSafeString();
	}


	/**
	 * Encrypt string with the key unlocked in unlockKey().
	 * 
	 * @param string $string
	 * 
	 * @return encrypted string (string)
	 */
	public function encryptString($string) {

		try {

			return Crypto::encrypt($string, $this->encryptionKey);
		}
		catch (EnvironmentIsBrokenException $error) {

			return "[Unexpected error (Error: E1007)]";
		}
	}


	/**
	 * Decrypt string with the key unlocked in unlockKey().
	 * 
	 * @param string $string
	 * 
	 * @return decrypted string (string)
	 */
	public function decryptString($string) {

		try {

			return Crypto::decrypt($string, $this->encryptionKey);
		}
		catch (WrongKeyOrModifiedCiphertextException $error) {

			return "[Unexpected error (Error: E1008)]";
		}
	}
}