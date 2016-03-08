<?php

require __DIR__ . '/../src/autoload.php';

$api = new rzd\api\API();

var_dump($api->ticketSearch()->searchTransfer(
		2000000, // Москва
		2004000, // Санкт-Петербург
		rzd\api\Date::fromDateTime(new DateTime())
));
