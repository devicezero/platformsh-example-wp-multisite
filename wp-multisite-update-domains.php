<?php

use Platformsh\ConfigReader\Config;

require __DIR__.'/vendor/autoload.php';

// Create a new config object to ease reading the Platform.sh environment variables.
// You can alternatively use getenv() yourself.
$config = new Config();

// if (!$config->isValidPlatform()) {
//     die("Not in a Platform.sh Environment.");
// }

# get primary domain
$primaryRouteArray = array_filter($config->routes(), function($k) {
	return $k['primary'] == true;
});

$primaryDomain = parse_url(key($primaryRouteArray), PHP_URL_HOST);

$credentials = $config->credentials('database');
$mysqli = new mysqli($credentials['host'], $credentials['username'], $credentials['password'], $credentials['path']);
$blogsQuery = $mysqli->query("SELECT blog_id, domain FROM wp_blogs");
$blogs = $blogsQuery->fetch_all();
print_r($blogs);

// "UPDATE wp_blogs SET domain = '{$blogDomain}' WHERE blog_id = {$blogId};
foreach ($blogs as $blog) {
	echo "UPDATE wp_blogs SET domain = '{$blog[1]}.{$primaryDomain}' WHERE blog_id = {$blog[0]};
}
