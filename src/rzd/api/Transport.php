<?php

namespace rzd\api;

class Transport {
	/**
	 * @var http\Session
	 */
	private $session;

	/**
	 * Базовый адрес (префикс) для запросов
	 * @var string
	 */
	private $base_url;

	public function __construct(\rzd\http\Session $session, $base_url) {
		$this->session = $session;
		$this->base_url = $base_url;
	}

	/**
	 * @param string $uri
	 * @param array $get
	 * @param array|null $post
	 * @return array
	 */
	public function performRidCall($uri, $get = [], $post = null) {
		$url = $this->base_url . $uri;
		do {
			$response = $this->session->request($url, $get, $post);
			if ($response->redirect) {
				throw new \Exception('Unexpected redirect');
			}

			$json = json_decode($response->contents, true);
			if (is_null($json)) {
				throw new \Exception('Response is not a valid json string');
			}
			$result = $json['result'];
			switch ($result) {
			case 'OK':
				return $json;
			case 'RID':
				$get = ['rid' => $json['rid']];
				$post = null;
				usleep(200000);
				break;
			case 'Error':
				throw new \Exception(sprintf(
						'Call failed: %s',
						$json['message']
				));
			default:
				throw new \Exception(sprintf(
						'Unexpected call result: %s',
						$result
				));
			}
		} while(true);
	}

	public function formatDate(Date $date) {
		return sprintf(
				'%2d.%2d.%4d',
				$date->day, $date->month, $date->year
		);
	}

	/**
	 * @param string $uri
	 * @param array $get
	 * @param array|null $post
	 * @return string
	 */
	public function performSimpleCall($uri, $get = [], $post = null) {
		$url = $this->base_url . $uri;

		return $this->session->request($url, $get, $post);
	}
}
