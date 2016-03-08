<?php

namespace rzd\api;

class Date {
	/**
	 * @var int
	 */
	public $year;

	/**
	 * @var int
	 */
	public $month;

	/**
	 * @var int
	 */
	public $day;

	public static function fromDateTime(\DateTime $dt) {
		$res = new Date();
		$res->year = (int)$dt->format('Y');
		$res->month = (int)$dt->format('n');
		$res->day = (int)$dt->format('j');
		return $res;
	}
}
