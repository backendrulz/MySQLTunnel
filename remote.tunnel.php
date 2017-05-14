<?php
error_reporting(0);

class MysqlTunnelServer
{
    private $db;
    private $link;
    public $errors = array();
    public $ips = array('127.0.0.1'); //Add here allowed ip

    public function __construct()
    {
        $input = file_get_contents('php://input');

        if (!in_array($this->get_ip(), $this->ips) or empty($input)) {
            $this->show_404();
        }

        try {
            $data = json_decode($input);
            $this->db = $data->db;
            $this->query = base64_decode($data->query);
        } catch (Exception $e) {
            $this->errors[] = $e->getMessage();
        }

        $this->tunnel_connect();
        $this->get_result();
    }

    private function tunnel_connect()
    {
        $this->link = new mysqli($this->db->host, $this->db->user, $this->db->pass, $this->db->db_name, $this->db->port);

        if ($this->link->connect_error) {
            $this->errors[] = $this->link->connect_errno . ' - '. $this->link->connect_error;
            return false;
        }
    }

    private function do_query()
    {
        $result = $this->link->query($this->query);

        if ($result) {
            $num_rows = $result->num_rows;

            $new_result = array();
            $new_result['num_rows'] = $num_rows;

            if ($num_rows > 1) {
                while ($row = $result->fetch_object()) {
                    $new_result['result'][] = $row;
                }
            } else {
                $new_result['result'] = $result->fetch_object();
            }
            $result->close();

            return json_encode($new_result);
        } else {
            $this->errors[] = $this->link->error;
            return false;
        }
    }

    public function get_result()
    {
        $result = $this->do_query();

        if (count($this->errors) > 0) {
            echo $this->get_errors();
        } else {
            echo $result;
        }
    }

    private function get_errors()
    {
        return json_encode(array('errors' => array_unique($this->errors)));
    }

    private function get_ip()
    {
        if ($_SERVER['HTTP_HOST'] == 'localhost') {
            $ip = '127.0.0.1';
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
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

new MysqlTunnelServer;
