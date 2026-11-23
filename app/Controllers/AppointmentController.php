<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\User;

class AppointmentController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $date = trim((string) $this->input('date', ''));
        $editId = (int) $this->input('edit', 0);

        $appointmentModel = new Appointment();
        $appointments = $appointmentModel->allByDate($date !== '' ? $date : null);
        $editing = $editId > 0 ? $appointmentModel->findById($editId) : null;

        $patientModel = new Patient();
        $userModel = new User();

        $this->render('appointments/index', [
            'title' => 'Rendez-vous',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'role' => Auth::user()['role'] ?? 'guest',
            'appointments' => $appointments,
            'patients' => $patientModel->allForSelect(),
            'doctors' => $userModel->doctors(),
            'selectedDate' => $date,
            'editing' => $editing,
        ]);
    }

    public function create(): void
    {
        Auth::requireRole(['secretaire']);

        $patientModel = new Patient();
        $userModel = new User();

        $this->render('appointments/create', [
            'title' => 'Ajouter rendez-vous',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'patients' => $patientModel->allForSelect(),
            'doctors' => $userModel->doctors(),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['secretaire']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('appointment/index');
        }

        $payload = $this->validatePayload();
        if ($payload['error'] !== null) {
            Auth::flash('error', $payload['error']);
            $this->redirect('appointment/create');
        }

        $model = new Appointment();
        $data = $payload['data'];

        if ($model->hasConflict((int) $data['doctor_id'], $data['appointment_at'], (int) $data['duration_minutes'])) {
            Auth::flash('error', 'Conflit d\'horaire detecte pour ce medecin.');
            $this->redirect('appointment/index');
        }

        $model->create($data);
        Auth::flash('success', 'Rendez-vous ajoute avec succes.');
        $this->redirect('appointment/index');
    }

    public function update(int $id): void
    {
        Auth::requireRole(['secretaire']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('appointment/index');
        }

        $payload = $this->validatePayload();
        if ($payload['error'] !== null) {
            Auth::flash('error', $payload['error']);
            $this->redirect('appointment/index');
        }

        $model = new Appointment();
        $data = $payload['data'];

        if ($model->hasConflict((int) $data['doctor_id'], $data['appointment_at'], (int) $data['duration_minutes'], $id)) {
            Auth::flash('error', 'Conflit d\'horaire detecte pour ce medecin.');
            $this->redirect('appointment/index');
        }

        $model->update($id, $data);
        Auth::flash('success', 'Rendez-vous modifie avec succes.');
        $this->redirect('appointment/index');
    }

    public function delete(int $id): void
    {
        Auth::requireRole(['secretaire']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('appointment/index');
        }

        (new Appointment())->delete($id);
        Auth::flash('success', 'Rendez-vous supprime avec succes.');
        $this->redirect('appointment/index');
    }

    private function validatePayload(): array
    {
        $patientId = (int) $this->input('patient_id');
        $doctorId = (int) $this->input('doctor_id');
        $appointmentAt = trim((string) $this->input('appointment_at'));
        $duration = max(10, (int) $this->input('duration_minutes', 30));
        $status = trim((string) $this->input('status', 'planifie'));
        $notes = trim((string) $this->input('notes', ''));

        if ($patientId <= 0 || $doctorId <= 0 || $appointmentAt === '') {
            return ['error' => 'Patient, medecin et date sont obligatoires.', 'data' => null];
        }

        if (!in_array($status, ['planifie', 'termine', 'annule'], true)) {
            return ['error' => 'Statut invalide.', 'data' => null];
        }

        return [
            'error' => null,
            'data' => [
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'appointment_at' => $appointmentAt,
                'duration_minutes' => $duration,
                'status' => $status,
                'notes' => $notes,
            ],
        ];
    }
}
