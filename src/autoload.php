<?php

spl_autoload_register(function($class) {
	$prefix = 'rzd\\';

	$class = ltrim($class, '\\');

	if (0 !== substr_compare($class, $prefix, 0, strlen($prefix))) {
		return;
	}

	$path = __DIR__ . DIRECTORY_SEPARATOR
			. str_replace('\\', DIRECTORY_SEPARATOR, $class)
			. '.php';

	if (is_readable($path)) {
		require $path;
	}
});
