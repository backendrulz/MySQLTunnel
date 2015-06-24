<?php
require 'tunnel.class.php';

$cfg = array('tunnel_url' => 'http://localhost/mysqltunnel/remote.tunnel.php',
		'db_data' => array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => 'lolxampp',
			'db_name' => 'l2j',
			'port' => '3306'));//Default mysql port is 3306

$mysql = new Mysql_tunnel_client($cfg);

//~ $query = 'SELECT * FROM characters WHERE char_name = \'[ADM]-Snake\'';
//~ $query = 'SELECT * FROM characters';
$query = 'SELECT * FROM characters WHERE level = 85 LIMIT 5';
//~ $query = "UPDATE characters SET level = '85', face = '0' WHERE char_name = '[ADM]-Snake'";
//~ $query = "SELECT COUNT(*) AS total FROM characters";

//~ $q = $mysql->get('characters', 2);
//~ $q = $mysql->update('characters', array('level' => 80));
$q = $mysql->query($query);

echo '<pre>';
print_r($q->row());
echo '</pre>';

?>
