<?php
namespace Aol\Transformers\Utilities;

use Aol\Transformers\CipherInterface;

trait EncryptionTrait
{
	/** @var \Aol\Transformers\CipherInterface */
	private $cipher;

	/**
	 * This trait requires the define method.
	 *
	 * @see \Aol\Transformers\Transformer
	 */
	abstract public function define(
		$app_name,
		$ext_name,
		callable $app_func = null,
		callable $ext_func = null,
		$app_args = [],
		$ext_args = []
	);

	public function setCipher(CipherInterface $cipher)
	{
		$this->cipher = $cipher;
	}

	protected function encrypt($plain_string)
	{
		return $this->getCipher()->encrypt($plain_string);
	}

	protected function decrypt($encrypted_string)
	{
		return $this->getCipher()->decrypt($encrypted_string);
	}

	protected function defineEncrypted($app_name, $ext_name)
	{
		$to_app = function ($encrypted_string) {
			return $this->decrypt($encrypted_string);
		};
		$to_ext = function ($plain_string) {
			return $this->encrypt($plain_string);
		};
		$this->define($app_name, $ext_name, $to_app, $to_ext);
	}

	protected function defineSerializedEncrypted($app_name, $ext_name)
	{
		$to_app = function ($encrypted_string) {
			return unserialize($this->decrypt($encrypted_string));
		};
		$to_ext = function ($plain_string) {
			return $this->encrypt(serialize($plain_string));
		};
		$this->define($app_name, $ext_name, $to_app, $to_ext);
	}

	private function getCipher()
	{
		if (empty($this->cipher)) {
			throw new \RuntimeException(
				'You must supply a cipher before using encryption. '
				. 'See ' . get_class()
			);
		}

		return $this->cipher;
	}
}
