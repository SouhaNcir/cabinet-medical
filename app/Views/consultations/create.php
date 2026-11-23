<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Ajouter consultation</h2>
            <p>Remplissez les informations medicales pour enregistrer une nouvelle consultation.</p>
        </div>
        <a href="<?= e(BASE_URL) ?>/consultation/index" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Retour consultations
        </a>
    </div>

    <form method="post" action="<?= e(BASE_URL) ?>/consultation/store" class="form-grid">
        <input type="hidden" name="_token" value="<?= e($csrf) ?>">

        <label>Patient *</label>
        <select name="patient_id" id="consultationPatient" required>
            <option value="">Selectionner...</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?= (int) $patient['id'] ?>"><?= e($patient['full_name']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Medecin *</label>
        <select name="doctor_id" required>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= (int) $doctor['id'] ?>" <?= (int) ($user['id'] ?? 0) === (int) $doctor['id'] ? 'selected' : '' ?>>
                    <?= e($doctor['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Date consultation *</label>
        <input type="datetime-local" id="consultationDate" name="consultation_date" value="<?= e(date('Y-m-d\TH:i')) ?>" required>

        <label>Symptomes *</label>
        <textarea name="symptoms" rows="3" required></textarea>

        <label>Diagnostic *</label>
        <textarea name="diagnosis" rows="3" required></textarea>

        <label>Traitement</label>
        <textarea name="treatment" rows="3"></textarea>

        <!-- Payment fields removed -->

        <button type="submit" class="btn btn-primary btn-full"><i class="fa-solid fa-stethoscope"></i> Enregistrer la consultation</button>
    </form>
</div>