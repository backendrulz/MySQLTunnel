#MySQLTunnel
A http MySQL remote tunnel  

##Local use
Open index.php and put your database params and tunnel. 
```
include 'tunnel.class.php';

$cfg = array('tunnel_url' => 'http://localhost/MySQLTunnel/remote.tunnel.php',
		'db_data' => array(
			'host' => 'localhost',//default is localhost
			'user' => 'YOUR USER', 
			'pass' => 'YOUR PASS',
			'db_name' => 'YOUR DATABASE',
			'port' => '3306'));//default mysql port is 3306


$mysql = new Mysql_tunnel_client($cfg);
```
Write a new query for the database  
```
$query = 'SELECT * FROM characters WHERE level = 85 LIMIT 5';
$q = $mysql->query($query);

```
##Remote Use
Check your ip address and add it in the list of allowed ips in remote.tunnel.php like this.  
```
class Mysql_tunnel_server{
        private $db, $link;
        public $errors = array();
        public $ips = array('127.0.0.1','2.87.9090.244'); //Add here allowed ips
````

Upload remote.tunnel.php on your domain.
Insert the link of remote tunnel
```
$cfg = array('tunnel_url' => 'http://www.yourdomain.com/MySQLTunnel/remote.tunnel.php',
```
Have fun!

##Security
Databases credential are transmitted via http.  
Only if you are on the list of allowed ips you can fetch the query.


##Mysql_tunnel_client class
To use **mysqltunnel**, first we need to include the **Mysql_tunnel_client** class file, create an array with the **database** params and then assign the class to a new variable.
The **$cfg** array consists of the following data:  

- **tunnel_url**: the url of the remote tunnel.php file
- **db_data**
  - **host**: the host of mysql in the remote machine (default is localhost)
  - **user**: username of remote mysql
  - **pass**: password of remote mysql
  - **db_name**: the database name to use  
  - **port**: port to connect to mysql database default is 3306
