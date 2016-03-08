<?php

namespace rzd\test;

class TicketSearchTest extends \PHPUnit_Framework_TestCase {
	public function testTicketSearch() {
		$api = new \rzd\api\API();

		$tomorrow_dt = (new \DateTime())->add(new \DateInterval('P1D'));
		$tomorrow = \rzd\api\Transport::formatDate($tomorrow_dt);

		$from = 2000000; // Москва
		$to = 2004000; // Санкт-Петербург



		$fwd1 = $api->ticketSearch()->searchTransfer(
				$from,
				$to,
				$tomorrow
		);

		$back1 = $api->ticketSearch()->searchTransfer(
				$to,
				$from,
				$tomorrow
		);

		list($fwd2, $back2) = $api->ticketSearch()->searchTransferAndReturn(
				$from,
				$to,
				$tomorrow,
				$tomorrow
		);

		$this->assertEquals($fwd1, $fwd2);
		$this->assertEquals($back1, $back2);

		$transfer = $fwd1[0];
		$details = $api->ticketSearch()->getTransferDetails(
				$from,
				$to,
				$transfer['date0'],
				$transfer['time0'],
				$transfer['number']
		);

		$api->auth()->authenticate(RZD_LOGIN, RZD_PASS);
	}
}
