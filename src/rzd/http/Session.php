<?php

namespace rzd\http;

/**
 * Сессионный HTTP-клиент на основе cURL.
 * Позволяет производить запросы и сохраняет выдаваемые куки на время существования объекта.
 */
class Session {
	/**
	 * @var resource
	 */
	private $curl;

	public function __construct() {
		$this->curl = curl_init();

		\curl_setopt_array($this->curl, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_COOKIEFILE => '', // включаем cookie
		]);
	}

	public function __destruct() {
		\curl_close($this->curl);
	}

	/**
	 * Произвести HTTP-запрос
	 * @param string $url Базовый адрес
	 * @param array $get Дополнительные параметры для адреса запроса
	 * @param array|null $post Параметры POST-запроса. Если null - выполняется GET-запрос.
	 * @return Response
	 */
	public function request($url, $get = [], $post = null) {
		\curl_setopt($this->curl, CURLOPT_URL, $this->buildUrl($url, $get));
		if (is_array($post)) {
			\curl_setopt($this->curl, CURLOPT_POST, true);
			\curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($post));
		} else {
			\curl_setopt($this->curl, CURLOPT_POST, false);
		}

		$contents = \curl_exec($this->curl);
		if (false === $contents) {
			throw new \Exception(sprintf(
					'Curl failed with %d: "%s"',
					\curl_errno($this->curl), \curl_error($this->curl)
			));
		}

		$response = new Response();
		$response->contents = $contents;
		$response->code = \curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		$response->redirect = \curl_getinfo($this->curl, CURLINFO_REDIRECT_URL);

		if (!in_array($response->code, [200, 302])) {
			throw new \Exception(sprintf(
					'Unexpected HTTP code: %d',
					$response->code
			));
		}

		return $response;
	}

	/**
	 * @param string $url
	 * @param array $args
	 * @return string
	 */
	private function buildUrl($url, array $args = []) {
		if (empty($args)) {
			return $url;
		}

		if (false === strpos($url, '?')) {
			$url .= '?';
		} else {
			$url .= '&';
		}

		return $url . http_build_query($args);
	}
}
