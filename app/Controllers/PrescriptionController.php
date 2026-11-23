<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Consultation;
use App\Models\Prescription;

class PrescriptionController extends Controller
{
    public function index(): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $consultationModel = new Consultation();
        $prescriptionModel = new Prescription();

        $this->render('prescriptions/index', [
            'title' => 'Ordonnances',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'consultations' => $consultationModel->latest(50),
            'prescriptions' => $prescriptionModel->all(),
        ]);
    }

    public function create(): void
    {
        Auth::requireRole(['medecin']);

        $consultationModel = new Consultation();

        $this->render('prescriptions/create', [
            'title' => 'Creer une ordonnance',
            'user' => Auth::user(),
            'flash' => Auth::pullFlash(),
            'csrf' => Auth::csrfToken(),
            'consultations' => $consultationModel->latest(50),
        ]);
    }

    public function store(): void
    {
        Auth::requireRole(['medecin']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('prescription/index');
        }

        $consultationId = (int) $this->input('consultation_id');
        $notes = trim((string) $this->input('notes'));
        $medicationNames = $_POST['med_name'] ?? [];
        $dosages = $_POST['dosage'] ?? [];
        $frequencies = $_POST['frequency'] ?? [];

        if ($consultationId <= 0 || empty($medicationNames)) {
            Auth::flash('error', 'Consultation et medicaments sont obligatoires.');
            $this->redirect('prescription/create');
        }

        $medications = [];
        foreach ($medicationNames as $i => $name) {
            $name = trim((string) $name);
            $dosage = trim((string) ($dosages[$i] ?? ''));
            $frequency = trim((string) ($frequencies[$i] ?? ''));
            if ($name !== '') {
                $medications[] = [
                    'name' => $name,
                    'dosage' => $dosage,
                    'frequency' => $frequency,
                ];
            }
        }

        if (empty($medications)) {
            Auth::flash('error', 'Ajoutez au moins un medicament valide.');
            $this->redirect('prescription/create');
        }

        try {
            (new Prescription())->create([
                'consultation_id' => $consultationId,
                'medications_json' => json_encode($medications, JSON_UNESCAPED_UNICODE),
                'notes' => $notes,
            ]);

            Auth::flash('success', 'Ordonnance creee avec succes.');
        } catch (\Throwable $exception) {
            Auth::flash('error', 'Impossible d\'enregistrer l\'ordonnance. Verifiez que MySQL est demarre puis reessayez.');
        }

        $this->redirect('prescription/index');
    }

    public function print(int $id): void
    {
        Auth::requireRole(['medecin', 'secretaire']);

        $prescription = (new Prescription())->findById($id);
        if (!$prescription) {
            Auth::flash('error', 'Ordonnance introuvable.');
            $this->redirect('prescription/index');
        }

        $medications = json_decode((string) $prescription['medications_json'], true);

        $this->render('prescriptions/print', [
            'title' => 'Impression Ordonnance',
            'prescription' => $prescription,
            'medications' => is_array($medications) ? $medications : [],
        ], 'auth');
    }

    public function delete(int $id): void
    {
        Auth::requireRole(['medecin']);

        if (!$this->isPost() || !Auth::verifyCsrf((string) $this->input('_token'))) {
            Auth::flash('error', 'Requete invalide.');
            $this->redirect('prescription/index');
        }

        (new Prescription())->delete($id);
        Auth::flash('success', 'Ordonnance supprimee avec succes.');
        $this->redirect('prescription/index');
    }
}
