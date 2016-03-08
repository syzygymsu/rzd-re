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
	 * @param string $departure Дата отправления
	 * @param int $types Типы маршрутов, комбинация флагов TRAIN_*
	 * @param bool $show_unavailable Также искать маршруты без доступных билетов
	 * @return array
	 */
	public function searchTransfer($from, $to, $departure, $types = self::TRAIN_ANY, $show_unavailable = false) {
		$args = [
			'dir' => 0,
			'tfl' => $types,
			'checkSeats' => $show_unavailable ? 0 : 1,
			'code0' => $from,
			'dt0' => $departure,
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
	 * @param string $departure Дата отправления
	 * @param string $return Дата возвращения
	 * @param int $types Типы маршрутов, комбинация флагов TRAIN_*
	 * @param bool $show_unavailable Также искать маршруты без доступных билетов
	 * @return array
	 */
	public function searchTransferAndReturn($from, $to, $departure, $return, $types = self::TRAIN_ANY, $show_unavailable = false) {
		$args = [
			'dir' => 1,
			'tfl' => $types,
			'checkSeats' => $show_unavailable ? 0 : 1,
			'code0' => $from,
			'dt0' => $departure,
			'code1' => $to,
			'dt1' => $return,
		];

		$res = $this->transport->performRidCall(
				'timetable/public/ru?STRUCTURE_ID=735&layer_id=5371',
				[], $args
		);

		return array($res['tp'][0]['list'], $res['tp'][1]['list']);
	}

	/**
	 * @param string $from
	 * @param string $to
	 * @param string $date
	 * @param string $time
	 * @param string $number
	 */
	public function getTransferDetails($from, $to, $date, $time, $number) {
		$args = [
			'dir' => 0, // просто ноль, другие значения не проходят
			'code0' => $from,
			'code1' => $to,
			'dt0' => $date,
			'time0' => $time,
			'tnum0' => $number
		];

		$res = $this->transport->performRidCall(
				'timetable/public/ru?STRUCTURE_ID=735&layer_id=5373',
				$args
		);

		return $res;
	}
}
