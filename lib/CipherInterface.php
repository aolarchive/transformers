<?php

namespace Aol\Transformers;

interface CipherInterface
{
	/**
	 * Encrypt the given text.
	 *
	 * @param string $plain_text The text to encrypt.
	 *
	 * @return string The encrypted text.
	 */
	function encrypt($plain_text);

	/**
	 * Decrypt the given text.
	 *
	 * @param string $encrypted_text The text to decrypt.
	 *
	 * @return string The decrypted text.
	 */
	function decrypt($encrypted_text);
}
