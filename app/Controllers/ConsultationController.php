<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;

class ConsultationController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $consultationModel = new Consultation();

        $this->render('consultations/index', [
            'title' => 'Consultations',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'consultations' => $consultationModel->latest(30),
        ]);
    }

    public function create(): void
    {
        Auth::requireRole(['medecin']);

        $patientModel = new Patient();
        $userModel = new User();

        $this->render('consultations/create', [
            'title' => 'Ajouter consultation',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'patients' => $patientModel->allForSelect(),
            'doctors' => $userModel->doctors(),
        ]);
    }

    public function show(int $id): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $consultation = (new Consultation())->findDetailedById($id);
        if (!$consultation) {
            Auth::flash('error', 'Consultation introuvable.');
            $this->redirect('consultation/index');
        }

        $this->render('consultations/show', [
            'title' => 'Fiche consultation',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'consultation' => $consultation,
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['medecin']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('consultation/index');
        }

        $payload = $this->validatePayload();
        if ($payload['error'] !== null) {
            Auth::flash('error', $payload['error']);
            $this->redirect('consultation/create');
        }

        (new Consultation())->create($payload['data']);
        Auth::flash('success', 'Consultation enregistree avec succes.');
        $this->redirect('consultation/index');
    }

    public function updatePayment(int $id): void
    {
        // Payment handling removed: endpoint deprecated.
        Auth::flash('error', 'Action non disponible.');
        $this->redirect('consultation/index');
    }

    public function delete(int $id): void
    {
        Auth::requireRole(['medecin']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('consultation/index');
        }

        (new Consultation())->delete($id);
        Auth::flash('success', 'Consultation supprimee avec succes.');
        $this->redirect('consultation/index');
    }

    private function validatePayload(): array
    {
        $patientId = (int) $this->input('patient_id');
        $doctorId = (int) $this->input('doctor_id');
        $symptoms = trim((string) $this->input('symptoms'));
        $diagnosis = trim((string) $this->input('diagnosis'));
        $treatment = trim((string) $this->input('treatment'));
        // payment fields removed
        $consultationDate = trim((string) $this->input('consultation_date'));

        if ($patientId <= 0 || $doctorId <= 0 || $symptoms === '' || $diagnosis === '' || $consultationDate === '') {
            return ['error' => 'Les champs obligatoires de consultation sont incomplets.', 'data' => null];
        }

        return [
            'error' => null,
            'data' => [
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'symptoms' => $symptoms,
                'diagnosis' => $diagnosis,
                'treatment' => $treatment,
                'consultation_date' => $consultationDate,
            ],
        ];
    }
}
