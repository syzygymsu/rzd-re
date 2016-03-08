<?php

require __DIR__ . '/../src/autoload.php';

$api = new rzd\api\API();

$tomorrow_dt = (new DateTime())->add(new DateInterval('P1D'));
$tomorrow = rzd\api\Date::fromDateTime($tomorrow_dt);

$a1 = $api->ticketSearch()->searchTransfer(
		2000000, // Москва
		2004000, // Санкт-Петербург
		$tomorrow
);


list($a2, $b2) = $api->ticketSearch()->searchTransferAndReturn(
		2000000, // Москва
		2004000, // Санкт-Петербург
		$tomorrow,
		$tomorrow
);

// При поиске туда и обратно результат "туда" должен быть таким же, как и при поиске в одну сторону.
var_dump($a1 == $a2);
