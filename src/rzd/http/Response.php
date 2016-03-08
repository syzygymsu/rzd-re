<?php

namespace rzd\http;

class Response {
	/**
	 * @var int
	 */
	public $code;

	/**
	 * @var string
	 */
	public $contents;

	/**
	 * @var null|string
	 */
	public $redirect = null;
}
