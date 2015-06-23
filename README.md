#MySQLTunnel

To use **mysqltunnel**, first we need to include the **Mysql_tunnel_client** class file, create an array with the **database** params and then assign the class to a new variable.

```
include 'tunnel.class.php';

$cfg = array('tunnel_url' => 'http://localhost/mysqltunnel/remote.tunnel.php',
		'db_data' => array(
			'host' => 'localhost',
			'user' => 'root',
			'pass' => 'lolxampp',
			'db_name' => 'l2j',
			'port' => '3306'));

$mysql = new Mysql_tunnel_client($cfg);
```

The **$cfg** array consists of the following data:  

- **tunnel_url**: the url of the remote tunnel.php file
- **db_data**
  - **host**: the host of mysql in the remote machine (default is localhost)
  - **user**: username of remote mysql
  - **pass**: password of remote mysql
  - **db_name**: the database name to use

