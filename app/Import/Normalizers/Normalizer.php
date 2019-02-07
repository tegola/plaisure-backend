<?php

namespace App\Import\Normalizers;

use JsonSerializable;

class Normalizer implements JsonSerializable
{
	protected $source = [];

	/**
	 * Create a new Normalizer instance.
	 *
	 * @param  mixed  $source
	 * @return void
	 */
	public function __construct($source = [])
	{
		$this->source = (object) $source;
	}

	/*
	protected function findPostcode(string $address)
	{
		return preg_match('/[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z][A-Z]/', $address);
	}
	*/

	public function normalize()
	{
		return $this->source;
	}

	/**
	 * Specify data which should be serialized to JSON.
	 *
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 *
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 *               which is a value of any type other than a resource.
	 *
	 * @since 5.4.0
	 */
	public function jsonSerialize()
	{
		if (!$this->source) return [];

		return $this->normalize();
	}
}