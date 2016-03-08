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
	 * @param \rzd\api\Date $departure Дата отправления
	 * @param int $types Типы маршрутов, комбинация флагов TRAIN_*
	 * @param bool $show_unavailable Также искать маршруты без доступных билетов
	 * @return array
	 */
	public function searchTransfer($from, $to, Date $departure, $types = self::TRAIN_ANY, $show_unavailable = false) {
		$args = [
			'dir' => 0,
			'tfl' => $types,
			'checkSeats' => $show_unavailable ? 0 : 1,
			'code0' => $from,
			'dt0' => $this->transport->formatDate($departure),
			'code1' => $to,
		];

		$res = $this->transport->performRidCall(
				'timetable/public/ru?STRUCTURE_ID=735&layer_id=5371',
				[], $args
		);

		return $res['tp'][0]['list'];
	}

	/**
	 * @param string $from Идентификатор станции отправления
	 * @param string $to Идентификатор станции прибытия
	 * @param \rzd\api\Date $departure Дата отправления
	 * @param \rzd\api\Date $return Дата возвращения
	 * @param int $types Типы маршрутов, комбинация флагов TRAIN_*
	 * @param bool $show_unavailable Также искать маршруты без доступных билетов
	 * @return array
	 */
	public function searchTransferAndReturn($from, $to, Date $departure, Date $return, $types = self::TRAIN_ANY, $show_unavailable = false) {
		$args = [
			'dir' => 1,
			'tfl' => $types,
			'checkSeats' => $show_unavailable ? 0 : 1,
			'code0' => $from,
			'dt0' => $this->transport->formatDate($departure),
			'code1' => $to,
			'dt1' => $this->transport->formatDate($return),
		];

		$res = $this->transport->performRidCall(
				'timetable/public/ru?STRUCTURE_ID=735&layer_id=5371',
				[], $args
		);

		return array($res['tp'][0]['list'], $res['tp'][1]['list']);
	}
}
