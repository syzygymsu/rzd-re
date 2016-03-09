<?php

namespace rzd\test;

class CustomerDetails extends \PHPUnit_Framework_TestCase {
	public function testCustomerDetails() {
		$api = new \rzd\api\API();

		$api->auth()->authenticate(RZD_LOGIN, RZD_PASS);

		$details = $api->auth()->getCustomerDetails();

		$tmp = $details['FIRST_NAME'];
		$details['FIRST_NAME'] = $details['LAST_NAME'];
		$details['LAST_NAME'] = $tmp;

		$api->auth()->saveCustomerDetails($details);
	}
}
