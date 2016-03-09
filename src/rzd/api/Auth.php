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

	/**
	 * @return array
	 */
	public function getCustomerDetails() {
		$res = $this->transport->performSimpleCall(
				'selfcare/editProfile/ru'
		);

		$data = [];

		$dom = new \DOMDocument();
		@$dom->loadHTML($res->contents);

		$xpath = new \DOMXPath($dom);
		foreach ($xpath->query('//form[@class="selfcareForm selfcareForm-profile"]//input') as $elem) {
			$name = $elem->getAttribute('name');
			$value = $elem->getAttribute('value');
			$data[$name] = $value;
		}
		foreach (['userpassword', 'userpassword_CONFIRM', 'DATA'] as $key) {
			unset($data[$key]);
		}

		foreach (['GENDER_ID', 'QUESTION_ID'] as $select) {
			$option = $xpath->query(
					'//form[@class="selfcareForm selfcareForm-profile"]' .
					'//select[@name="'.$select.'"]//option[@selected="true"]'
			);
			$data[$select] = $option->item(0)->getAttribute('value');
		}

		return $data;
	}

	public function saveCustomerDetails($details) {
		$args = $details;

		$res = $this->transport->performSimpleCall(
				'selfcare/editProfile/ru',
				[], $args
		);

		$dom = new \DOMDocument();
		@$dom->loadHTML($res->contents);
		$xpath = new \DOMXPath($dom);

		$warning = $xpath->query('//*[@class="warningBlock"]');
		$message = $warning->item(0)->textContent;

		if ($message !== 'Профиль пользователя успешно изменен') {
			throw new \Exception('Failed to save customer details: ' . $message);
		}
	}
}
