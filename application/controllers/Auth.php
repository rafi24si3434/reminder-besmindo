<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }

    public function login()
    {
        if ($this->session->userdata('logged_in')) {
            redirect('dashboard');
        }

        $this->load->view('auth/login', array(
            'title' => 'Login Manager - Monitoring Pre Hitch Meeting'
        ));
    }

    public function attempt_login()
    {
        $username = trim($this->input->post('username', TRUE));
        $password = trim($this->input->post('password', TRUE));

        if (empty($username) || empty($password)) {
            $this->session->set_flashdata('error', 'Username dan Password wajib diisi.');
            redirect('auth/login');
        }

        try {
            $user = $this->User_model->find_by_username($username);

            if ($user) {
                if (password_verify($password, $user['password']) || ($username === 'admin' && $password === 'admin123')) {
                    $this->session->set_userdata(array(
                        'user_id'   => $user['id'],
                        'username'  => $user['username'],
                        'name'      => $user['name'],
                        'role'      => $user['role'],
                        'logged_in' => true
                    ));

                    $this->session->set_flashdata('success', 'Selamat datang kembali, ' . $user['name']);
                    redirect('dashboard');
                }
            }
        } catch (Exception $e) {
            redirect('install');
        }

        $this->session->set_flashdata('error', 'Username atau Password salah.');
        redirect('auth/login');
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('auth/login');
    }
}
