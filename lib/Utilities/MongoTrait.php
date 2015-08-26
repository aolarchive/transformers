<?php

namespace Aol\Transformers\Utilities;

use Aol\Transformers\AbstractDefinitionTrait;
use MongoDate;
use MongoId;

trait MongoTrait
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
			return $date instanceof MongoDate ? new \DateTime('@' . $date->sec) : null;
		};

		$ext_callable = function ($date) {
			if (empty($date)) {
				return null;
			}

			$date = $date instanceof \DateTime ? $date->getTimestamp() : strtotime($date);

			return new MongoDate($date);
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
			return empty($id) ? null : new MongoId($id);
		};

		$this->define($app_name, $ext_name, 'strval', $ext_callable);
	}

	//---------------------------------
	// Utility methods
	//---------------------------------

	/**
	 * Get created DateTime from a MongoId object.
	 *
	 * @param MongoId $id
	 * @return \DateTime
	 */
	protected function getDateFromMongoId(MongoId $id)
	{
		$date_time = new \DateTime('@' . $id->getTimestamp());
		$date_time->setTimezone(new \DateTimeZone(date_default_timezone_get()));
		return $date_time;
	}

	/**
	 * Build a new MongoId instance from a given date.
	 *
	 * @see http://jwage.com/post/55617183676/mongodb-php-mongodate-tricks
	 *
	 * @param mixed $date Date to use to generate MongoId; can be unix timestamp, DateTime, or date string
	 * @return MongoId
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
		return new \MongoId($id);
	}

	/**
	 * Convert all reserved Mongo characters in the given array's keys, to safe unicode characters.
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
					$new_key = null;

					// Convert '$' -> '＄', '.' -> '．'
					if (strpos($key, '$') !== false || strpos($key, '.') !== false) {
						$new_key = str_replace(['$', '.'], [json_decode('"\uFF04"'), json_decode('"\uFF0E"')], $key);

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
	 * Reverts all converted safe unicode characters in the given array's keys, back to the original characters.
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
					$new_key = null;

					// Convert '＄' -> '$', '．' -> '.'
					if (mb_strpos($key, json_decode('"\uFF04"')) !== false
						|| mb_strpos($key, json_decode('"\uFF0E"')) !== false) {
						$new_key = str_replace([json_decode('"\uFF04"'), json_decode('"\uFF0E"')], ['$', '.'], $key);

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
