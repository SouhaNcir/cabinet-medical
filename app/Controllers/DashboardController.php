<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Consultation;
use App\Models\Patient;

class DashboardController extends Controller
{
    public function index(): void
    {
        Auth::requireAuth();

        $patientModel = new Patient();
        $consultationModel = new Consultation();
        $stats = $patientModel->getGenderStats();

        $this->render('dashboard/index', [
            'title' => 'Dashboard',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'stats' => $stats,
            'consultations_recentes' => $consultationModel->latest(5),
            'csrf' => Auth::csrfToken(),
        ]);
    }
}
