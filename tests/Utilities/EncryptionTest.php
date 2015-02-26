<?php

namespace Aol\Transformers\Tests\Utilities;

use Aol\Transformers\CipherInterface;
use Aol\Transformers\Transformer;
use Aol\Transformers\Utilities\EncryptionTrait;

class EncryptionTest extends \PHPUnit_Framework_TestCase
{
	/** @var EncryptionTransformer */
	private $transformer;

	public function testEncryptedToApp()
	{
		$this->assertSame(
			'abc',
			$this->transformer->toApp('YWJj', 'ext_encrypted')
		);
	}

	public function testEncryptedToExt()
	{
		$this->assertSame(
			'YWJj',
			$this->transformer->toExt('abc', 'app_encrypted')
		);
	}

	public function testSerializedEncryptedToApp()
	{
		$this->assertSame(
			'abc',
			$this->transformer->toApp('czozOiJhYmMiOw==', 'ext_serialized_encrypted')
		);
	}

	public function testSerializedEncryptedToExt()
	{
		$this->assertSame(
			'czozOiJhYmMiOw==',
			$this->transformer->toExt('abc', 'app_serialized_encrypted')
		);
	}

	/**
	 * @expectedException \RuntimeException
	 * @expectedExceptionMessage You must supply a cipher before using encryption. See Aol\Transformers\Tests\Utilities\EncryptionTransformer
	 */
	public function testEncryptionWithoutCipherShouldThrowException()
	{
		(new EncryptionTransformer())->toApp('123', 'ext_encrypted');
	}

	protected function setUp()
	{
		$this->transformer = new EncryptionTransformer;
		$this->transformer->setCipher(new EncryptionCipher);
	}
}

class EncryptionTransformer extends Transformer
{
	use EncryptionTrait;

	protected function definitions()
	{
		$this->defineEncrypted('app_encrypted', 'ext_encrypted');
		$this->defineSerializedEncrypted('app_serialized_encrypted', 'ext_serialized_encrypted');
	}
}

class EncryptionCipher implements CipherInterface
{
	function encrypt($plain_text)
	{
		return base64_encode($plain_text);
	}

	function decrypt($encrypted_text)
	{
		return base64_decode($encrypted_text);
	}
}
