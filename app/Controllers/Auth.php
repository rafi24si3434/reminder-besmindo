<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $session;

    public function __construct()
    {
        $this->session = session();
    }

    public function login()
    {
        if ($this->session->get('logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('auth/login', [
            'title' => 'Login Manager - Besmindo Reminder'
        ]);
    }

    public function attemptLogin()
    {
        $username = trim($this->request->getPost('username') ?? '');
        $password = trim($this->request->getPost('password') ?? '');

        if (empty($username) || empty($password)) {
            return redirect()->back()->withInput()->with('error', 'Username dan Password wajib diisi.');
        }

        try {
            $userModel = new UserModel();
            $user = $userModel->findByUsername($username);

            if ($user) {
                // Verify password hash or fallback default
                if (password_verify($password, $user['password']) || ($username === 'admin' && $password === 'admin123')) {
                    $this->session->set([
                        'user_id'   => $user['id'],
                        'username'  => $user['username'],
                        'name'      => $user['name'],
                        'role'      => $user['role'],
                        'logged_in' => true
                    ]);

                    return redirect()->to(base_url('dashboard'))->with('success', 'Selamat datang kembali, ' . $user['name']);
                }
            }
        } catch (\Throwable $e) {
            // If table doesn't exist yet, offer easy redirect to installer
            return redirect()->to(base_url('install'))->with('error', 'Database belum disiapkan. Silakan klik tombol inisialisasi di bawah.');
        }

        return redirect()->back()->withInput()->with('error', 'Username atau Password salah.');
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('auth/login'))->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
