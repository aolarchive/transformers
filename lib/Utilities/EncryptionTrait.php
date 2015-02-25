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

	protected function encrypt($plain_text)
	{
		return $this->getCipher()->encrypt($plain_text);
	}

	protected function decrypt($encrypted_text)
	{
		return $this->getCipher()->decrypt($encrypted_text);
	}

	protected function defineEncrypted($app_name, $ext_name)
	{
		$this->define($app_name, $ext_name, [$this->getCipher(), 'decrypt'], [$this->cipher, 'encrypt']);
	}

	private function getCipher()
	{
		if (empty($this->cipher)) {
			throw new \RuntimeException(
				'You must supply a cipher before using encryption.'
				. 'See ' . get_class()
			);
		}

		return $this->cipher;
	}
}
