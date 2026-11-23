<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::check()) {
            $this->redirect('dashboard/index');
        }

        $this->render('auth/login', [
            'title' => 'Connexion',
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
        ], 'auth');
    }

    public function authenticate(): void
    {
        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('auth/login');
        }

        $attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'first_try_at' => time()];
        if ((time() - (int) $attempts['first_try_at']) > 600) {
            $attempts = ['count' => 0, 'first_try_at' => time()];
        }
        if ((int) $attempts['count'] >= 5) {
            Auth::flash('error', 'Trop de tentatives. Reessayez dans quelques minutes.');
            $this->redirect('auth/login');
        }

        $email = filter_var((string) $this->input('email'), FILTER_VALIDATE_EMAIL);
        $password = (string) $this->input('password');

        if (!$email || $password === '') {
            Auth::flash('error', 'Email et mot de passe sont obligatoires.');
            $this->redirect('auth/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['login_attempts'] = [
                'count' => (int) $attempts['count'] + 1,
                'first_try_at' => (int) $attempts['first_try_at'],
            ];
            Auth::flash('error', 'Identifiants invalides.');
            $this->redirect('auth/login');
        }

        unset($_SESSION['login_attempts']);
        Auth::login($user);
        Auth::flash('success', 'Bienvenue ' . $user['full_name'] . '.');
        $this->redirect('dashboard/index');
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
}
