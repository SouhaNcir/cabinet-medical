<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Patient;

class PatientController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $search = trim((string) $this->input('search', ''));
        $page = max(1, (int) $this->input('page', 1));

        $patientModel = new Patient();
        $result = $patientModel->paginate($page, PER_PAGE, $search);

        $this->render('patients/index', [
            'title' => 'Gestion Patients',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'patients' => $result['data'],
            'search' => $search,
            'page' => $result['page'],
            'totalPages' => $result['totalPages'],
            'total' => $result['total'],
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('patient/index');
        }

        $payload = $this->validateInput();

        if ($payload['error'] !== null) {
            Auth::flash('error', $payload['error']);
            $this->redirect('patient/index');
        }

        $patientModel = new Patient();
        $patientModel->create($payload['data']);

        Auth::flash('success', 'Patient ajoute avec succes.');
        $this->redirect('patient/index');
    }

    public function edit(int $id): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $patientModel = new Patient();
        $patient = $patientModel->findById($id);

        if (!$patient) {
            Auth::flash('error', 'Patient introuvable.');
            $this->redirect('patient/index');
        }

        $this->render('patients/edit', [
            'title' => 'Modifier Patient',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'patient' => $patient,
        ]);
    }

    public function update(int $id): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('patient/index');
        }

        $payload = $this->validateInput();

        if ($payload['error'] !== null) {
            Auth::flash('error', $payload['error']);
            $this->redirect('patient/edit/' . $id);
        }

        $patientModel = new Patient();
        $patientModel->update($id, $payload['data']);

        Auth::flash('success', 'Patient modifie avec succes.');
        $this->redirect('patient/index');
    }

    public function delete(int $id): void
    {
        Auth::requireRole(['secretaire']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('patient/index');
        }

        $patientModel = new Patient();
        $patientModel->delete($id);

        Auth::flash('success', 'Patient supprime avec succes.');
        $this->redirect('patient/index');
    }

    private function validateInput(): array
    {
        $fullName = trim((string) $this->input('full_name'));
        $cin = trim((string) $this->input('cin'));
        $phone = trim((string) $this->input('phone'));
        $emailRaw = trim((string) $this->input('email'));
        $gender = trim((string) $this->input('gender'));
        $birthDate = trim((string) $this->input('birth_date'));
        $address = trim((string) $this->input('address'));

        if ($fullName === '' || $cin === '' || $phone === '' || $gender === '' || $birthDate === '') {
            return ['error' => 'Les champs obligatoires doivent etre remplis.', 'data' => null];
        }

        if (!in_array($gender, ['M', 'F'], true)) {
            return ['error' => 'Genre invalide.', 'data' => null];
        }

        $email = null;
        if ($emailRaw !== '') {
            $email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
            if ($email === false) {
                return ['error' => 'Adresse email invalide.', 'data' => null];
            }
        }

        return [
            'error' => null,
            'data' => [
                'full_name' => $fullName,
                'cin' => $cin,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'birth_date' => $birthDate,
                'address' => $address,
            ],
        ];
    }
}
