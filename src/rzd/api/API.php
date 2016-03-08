<?php

namespace rzd\api;

class API {
	/**
	 * @var \rzd\http\Session
	 */
	private $session;

	/**
	 * @var Transport
	 */
	private $transport;

	/**
	 * @var TicketSearch
	 */
	private $ticket_search;

	/**
	 * @var Auth
	 */
	private $auth;

	public function __construct() {
		$base_url = 'https://pass.rzd.ru/';

		$this->session = new \rzd\http\Session();

		$this->transport = new Transport($this->session, $base_url);

		$this->ticket_search = new TicketSearch($this->transport);
		$this->auth = new Auth($this->transport);
	}

	/**
	 * @return TicketSearch
	 */
	public function ticketSearch() {
		return $this->ticket_search;
	}

	/**
	 * @return Auth
	 */
	public function auth() {
		return $this->auth;
	}
}
