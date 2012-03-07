<?php
error_reporting(E_ALL);
//~ error_reporting(0);

class Mysql_tunnel_server{
	private $db, $link;
	public $errors = array();
	public $ips = array('127.0.0.1');

	function __construct()
	{
		$input = file_get_contents('php://input');

		if(!in_array($this->get_ip(), $this->ips) OR empty($input))
		{
			$this->show_404();
		}

		try{
			$data			= json_decode($input);
			$this->db		= $data->db;
			$this->query	= base64_decode($data->query);
		}catch (Exception $e){
			$this->errors[] = $e->getMessage();
		}

		$this->tunnel_connect();
		$this->get_result();

	}

	private function tunnel_connect()
	{
		$this->link = mysql_connect($this->db->host.':'.$this->db->port, $this->db->user, $this->db->pass);
		if(!$this->link)
		{
			$this->errors[] = mysql_error();
			return false;
		}

		if(strlen($this->db->db_name) != 0)
		{
			mysql_select_db($this->db->db_name);
		}

	}

	private function do_query()
	{
		$query = mysql_query($this->query, $this->link);

		if($query)
		{
			$num_rows = mysql_num_rows($query);

			$result = array();
			$result['num_rows'] = $num_rows;

			if($num_rows > 1)
			{
				while($row = mysql_fetch_object($query))
				{
					$result['result'][] = $row;
				}

			}else{
				$result['result'] = mysql_fetch_object($query);

			}

			return json_encode($result);

			mysql_free_result($query);
		}
		else
		{
			$this->errors[] = mysql_error();
			return false;
		}

	}

	public function get_result()
	{
		$result = $this->do_query();

		if(count($this->errors) > 0)
		{
			echo $this->get_errors();

		}else
		{
			echo $result;

		}

	}

	private function get_errors()
	{
		return json_encode(array('errors' => array_unique($this->errors)));
	}

	private function get_ip()
	{
		if(isset($_SERVER['HTTP_CLIENT_IP']))
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];

		}
		elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];

		}
		else
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;

	}

	private function show_404()
	{
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		die;
	}

}

new Mysql_tunnel_server;

/* end of file */