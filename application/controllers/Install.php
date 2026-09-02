<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Install extends CI_Controller
{
    public function index()
    {
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'db_besmindo_reminder';
        $dbPort = 3306;

        $status = array(
            'can_connect_server' => false,
            'database_exists'    => false,
            'tables_created'     => false,
            'message'            => ''
        );

        try {
            $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, '', (int)$dbPort);
            if ($mysqli->connect_error) {
                $status['message'] = "Gagal menghubungkan ke MySQL: " . $mysqli->connect_error . " (Pastikan MySQL di XAMPP sudah di-Start).";
                return $this->load->view('install/index', array('status' => $status, 'config' => array('hostname' => $dbHost, 'database' => $dbName, 'port' => $dbPort)));
            }
            $status['can_connect_server'] = true;

            $res = $mysqli->query("SHOW DATABASES LIKE '{$dbName}'");
            if ($res && $res->num_rows > 0) {
                $status['database_exists'] = true;
                $mysqli->select_db($dbName);
                $tablesRes = $mysqli->query("SHOW TABLES LIKE 'meetings'");
                if ($tablesRes && $tablesRes->num_rows > 0) {
                    $status['tables_created'] = true;
                }
            }
        } catch (Exception $e) {
            $status['message'] = $e->getMessage();
        }

        $this->load->view('install/index', array(
            'status' => $status,
            'config' => array('hostname' => $dbHost, 'database' => $dbName, 'port' => $dbPort)
        ));
    }

    public function run()
    {
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'db_besmindo_reminder';
        $dbPort = 3306;

        try {
            $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, '', (int)$dbPort);
            if ($mysqli->connect_error) {
                return $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array(
                        'success' => false,
                        'message' => 'Koneksi MySQL gagal: ' . $mysqli->connect_error . '. Pastikan MySQL di XAMPP sudah di-start.'
                    )));
            }

            // 1. Create DB
            $mysqli->query("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $mysqli->select_db($dbName);

            // 2. Read schema file
            $sqlFile = FCPATH . 'database.sql';
            if (file_exists($sqlFile)) {
                $sqlContent = file_get_contents($sqlFile);
                $queries = explode(';', $sqlContent);
                foreach ($queries as $q) {
                    $q = trim($q);
                    if (!empty($q)) {
                        $mysqli->query($q);
                    }
                }
            }

            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => true,
                    'message' => 'Struktur Database berhasil diinisialisasi dalam keadaan bersih!'
                )));
        } catch (Exception $e) {
            return $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode(array(
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                )));
        }
    }
}
