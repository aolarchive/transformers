<?php

namespace Amp\Transformers\DataStore;

interface UtilityInterface
{
	/**
	 * @param mixed $id ID.
	 * @return mixed
	 */
	public function idToApp($id);

	/**
	 * @param mixed $id ID.
	 * @return mixed
	 */
	public function idToExt($id);

	/**
	 * @param mixed $date Date.
	 * @return \DateTime|null
	 */
	public function dateToApp($date);

	/**
	 * @param \DateTime|null $date Date object.
	 * @return mixed
	 */
	public function dateToExt($date);

	/**
	 * @param mixed $date Date.
	 * @return \DateTime|null
	 */
	public function dateTimeToApp($date);

	/**
	 * @param \DateTime $date|null Date object.
	 * @return mixed
	 */
	public function dateTimeToExt($date);
}
