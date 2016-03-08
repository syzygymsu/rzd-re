<?php

namespace rzd\api;

class TicketSearch {
	const TRAIN_ANY = 0; // Любого типа
	const TRAIN_MAINLINE = 1; // Поезд дальнего следования
	const TRAIN_SUBURBAN = 2; // Электричка

	/**
	 * @var Transport
	 */
	private $transport;

	public function __construct(Transport $transport) {
		$this->transport = $transport;
	}

	/**
	 * @param string $from Идентификатор станции отправления
	 * @param string $to Идентификатор станции прибытия
	 * @param \rzd\api\Date $from_date Дата отправления
	 * @param int $types Типы маршрутов, комбинация флагов TRAIN_*
	 * @param bool $show_unavailable Также искать маршруты без доступных билетов
	 * @return array
	 */
	public function searchTransfer($from, $to, Date $from_date, $types = self::TRAIN_ANY, $show_unavailable = false) {
		$args = [
			'dir' => 0,
			'tfl' => $types,
			'checkSeats' => $show_unavailable ? 0 : 1,
			'code0' => $from,
			'dt0' => $this->transport->formatDate($from_date),
			'code1' => $to,
		];

		$res = $this->transport->performRidCall(
				'timetable/public/ru?STRUCTURE_ID=735&layer_id=5371',
				[], $args
		);

		return $res['tp'];
	}
}
