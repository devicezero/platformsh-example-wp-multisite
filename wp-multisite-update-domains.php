<?php

use Platformsh\ConfigReader\Config;

require __DIR__.'/vendor/autoload.php';

// Create a new config object to ease reading the Platform.sh environment variables.
// You can alternatively use getenv() yourself.
$config = new Config();

if (!$config->isValidPlatform()) {
    die("Not in a Platform.sh Environment.");
}

$credentials = $config->credentials('database');

# get primary domain
$primaryRouteArray = array_filter($config->routes(), function($k) {
	return $k['primary'] == true;
});

$primaryDomain = parse_url(key($primaryRouteArray), PHP_URL_HOST);

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s', $credentials['host'], $credentials['port'], $credentials['path']);
$connection = new \PDO($dsn, $credentials['username'], $credentials['password'], [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
		\PDO::MYSQL_ATTR_FOUND_ROWS => TRUE,
]);


$blogsQuery = "SELECT blog_id, domain FROM wp_blogs";
$blogs = $connection->query($blogsQuery);
print_r($blogs);
