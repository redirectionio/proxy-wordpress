<?php

$user = 'rio';
$pass = 'rio';
$host = 'database';
$dbname = 'rio';
$activePlugins = serialize(['redirectionio/autoload.php']);
$defaultConnection = serialize([
    'connections' => [
        [
            'name' => 'my-agent',
            'host' => '192.168.64.2',
            'port' => '20301',
        ],
    ],
    'doNotRedirectAdmin' => true,
]);


try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);

    $db->exec("UPDATE wp_options SET option_value = '$activePlugins' WHERE option_name = 'active_plugins';");
    $db->exec("INSERT INTO wp_options(option_name, option_value) VALUES('redirectionio', '$defaultConnection');");
    
    $db = null;
} catch (PDOException $e) {
    print "Error: " . $e->getMessage() . "<br/>";
    die();
}
