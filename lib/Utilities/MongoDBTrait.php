<?php

namespace Aol\Transformers\Utilities;

use Aol\Transformers\AbstractDefinitionTrait;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectID;

/**
 * For use with the latest pecl-mongodb driver.
 * @see http://php.net/manual/en/book.mongodb.php
 */
trait MongoDBTrait
{
	use AbstractDefinitionTrait;

	/**
	 * Defines a date property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	protected function defineDate($app_name, $ext_name)
	{
		$app_callable = function ($date) {
			return $date instanceof UTCDateTime ? $date->toDateTime() : null;
		};

		$ext_callable = function ($date) {
			if (empty($date)) {
				return null;
			}

			$date = $date instanceof \DateTime ? $date->getTimestamp() : strtotime($date);

			return new UTCDateTime($date * 1000);
		};

		$this->define($app_name, $ext_name, $app_callable, $ext_callable);
	}

	/**
	 * Defines an ID property.
	 *
	 * @param string $app_name Property name in application context.
	 * @param string $ext_name Property name storage context.
	 */
	protected function defineId($app_name, $ext_name)
	{
		$ext_callable = function ($id) {
			return empty($id) ? null : new ObjectId($id);
		};

		$this->define($app_name, $ext_name, 'strval', $ext_callable);
	}

	//---------------------------------
	// Utility methods
	//---------------------------------

	/**
	 * Get created DateTime from a ObjectID object.
	 *
	 * @param ObjectID $id
	 * @return \DateTime
	 */
	protected function getDateFromMongoId(ObjectID $id)
	{
		$timestamp = intval(substr((string)$id, 0, 8), 16);
		$date_time = new \DateTime('@' . $timestamp);
		$date_time->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		return $date_time;
	}

	/**
	 * Build a new ObjectID instance from a given date.
	 *
	 * @see http://jwage.com/post/55617183676/mongodb-php-mongodate-tricks
	 *
	 * @param mixed $date Date to use to generate ObjectID; can be unix timestamp, DateTime, or date string
	 * @return ObjectID
	 */
	protected function getMongoIdFromDate($date)
	{
		static $inc = 0;
		if ($date instanceof \DateTime) {
			$timestamp = $date->getTimestamp();
		} else if (is_int($date)) {
			$timestamp = $date;
		} else {
			$timestamp = strtotime($date);
		}
		if (empty($timestamp)) {
			return null;
		}
		$ts = pack('N', $timestamp);
		$m = substr(md5(gethostname()), 0, 3);
		$pid = pack('n', posix_getpid());
		$trail = substr(pack('N', $inc++), 1, 3);
		$bin = sprintf('%s%s%s%s', $ts, $m, $pid, $trail);
		$id = '';
		for ($i = 0; $i < 12; $i++ ) {
			$id .= sprintf('%02x', ord($bin[$i]));
		}
		return new ObjectId($id);
	}

	/**
	 * Convert all reserved Mongo characters in a single key name, to safe unicode characters.
	 * Converts the following characters:
	 * '$' -> '＄'
	 * '.' -> '．'
	 *
	 * @see http://docs.mongodb.org/manual/faq/developers/#faq-dollar-sign-escaping
	 *
	 * @param  string The key to escape
	 * @return string The escaped key
	 */
	public function escapeMongoKey($key)
	{
		if (is_string($key)) {
			// Convert '$' -> '＄', '.' -> '．'
			if (strpos($key, '$') !== false || strpos($key, '.') !== false) {
				$key = str_replace(['$', '.'], [json_decode('"\uFF04"'), json_decode('"\uFF0E"')], $key);
			}
		}
		return $key;
	}

	/**
	 * Reverts all converted safe unicode characters in a single key name, back to the original characters.
	 * Reverts the following characters:
	 * '＄' -> '$'
	 * '．' -> '.'
	 *
	 * @see http://docs.mongodb.org/manual/faq/developers/#faq-dollar-sign-escaping
	 *
	 * @param  string The key to unescape
	 * @return string The unescaped key
	 */
	public function unescapeMongoKey($key)
	{
		if (is_string($key)) {
			// Convert '＄' -> '$', '．' -> '.'
			if (mb_strpos($key, json_decode('"\uFF04"')) !== false
				|| mb_strpos($key, json_decode('"\uFF0E"')) !== false) {

				$key = str_replace([json_decode('"\uFF04"'), json_decode('"\uFF0E"')], ['$', '.'], $key);
			}
		}
		return $key;
	}

	/**
	 * Convert all reserved Mongo characters in all the given array's keys, to safe unicode characters.
	 * Converts the following characters:
	 * '$' -> '＄'
	 * '.' -> '．'
	 *
	 * @see http://docs.mongodb.org/manual/faq/developers/#faq-dollar-sign-escaping
	 *
	 * @param array The array to escape
	 * @return array The escaped array
	 */
	public function escapeMongoKeys($data)
	{
		if (is_array($data)) {
			foreach($data as $key => $value) {
				if (is_string($key)) {
					// Convert '$' -> '＄', '.' -> '．'
					$new_key = $this->escapeMongoKey($key);

					if ($new_key !== $key) {
						$data[$new_key] = $value;
						unset($data[$key]);
						$key = $new_key;
					}
				}
				if (is_array($value)) {
					$data[$key] = $this->escapeMongoKeys($value);
				}
			}
		}
		return $data;
	}

	/**
	 * Reverts all converted safe unicode characters in all the given array's keys, back to the original characters.
	 * Reverts the following characters:
	 * '＄' -> '$'
	 * '．' -> '.'
	 *
	 * @see http://docs.mongodb.org/manual/faq/developers/#faq-dollar-sign-escaping
	 *
	 * @param array The array to unescape
	 * @return array The unescaped array
	 */
	public function unescapeMongoKeys($data)
	{
		if (is_array($data)) {
			foreach($data as $key => $value) {
				if (is_string($key)) {
					// Convert '＄' -> '$', '．' -> '.'
					$new_key = $this->unescapeMongoKey($key);

					if ($new_key !== $key) {
						$data[$new_key] = $value;
						unset($data[$key]);
						$key = $new_key;
					}
				}
				if (is_array($value)) {
					$data[$key] = $this->unescapeMongoKeys($value);
				}
			}
		}
		return $data;
	}
}
