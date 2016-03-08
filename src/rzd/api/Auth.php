<?php

namespace rzd\api;

class Auth {
	/**
	 * @var Transport
	 */
	private $transport;

	public function __construct(Transport $transport) {
		$this->transport = $transport;
	}

	public function authenticate($login, $pass) {
		$args = [
			'j_username' => $login,
			'j_password' => $pass,
		];
		$res = $this->transport->performSimpleCall(
			'timetable/j_security_check/ru',
			[], $args
		);

		// TODO: придумать, как абстрагироваться от базового пути https://pass.rzd.ru/
		// TODO: возможно, лучше проверять установку новой куки
		switch ($res->redirect) {
		case 'https://pass.rzd.ru/timetable/logonErr':
			throw new TransportException('Invalid credentials');
		case 'https://pass.rzd.ru/timetable/j_security_check/':
			return;
		default:
			throw new TransportException(sprintf(
					'Unexpected redirect to: "%s"',
					$res->redirect
			));
		}
	}
}
