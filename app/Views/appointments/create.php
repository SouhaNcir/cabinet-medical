<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Ajouter un rendez-vous</h2>
            <p>Créer un nouveau rendez-vous depuis un formulaire dédié.</p>
        </div>
        <a href="<?= e(BASE_URL) ?>/appointment/index" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Retour planning
        </a>
    </div>

    <form method="post" action="<?= e(BASE_URL) ?>/appointment/store" class="form-grid panel" style="margin-top: 10px;">
        <input type="hidden" name="_token" value="<?= e($csrf) ?>">

        <label>Patient *</label>
        <select name="patient_id" required>
            <option value="">Selectionner...</option>
            <?php foreach ($patients as $patient): ?>
                <option value="<?= (int) $patient['id'] ?>">
                    <?= e($patient['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Medecin *</label>
        <select name="doctor_id" required>
            <option value="">Selectionner...</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?= (int) $doctor['id'] ?>">
                    <?= e($doctor['full_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Date et heure *</label>
        <input type="datetime-local" name="appointment_at" value="<?= e(date('Y-m-d\TH:i')) ?>" required>

        <label>Duree (minutes)</label>
        <input type="number" name="duration_minutes" min="10" value="30">

        <label>Statut</label>
        <select name="status">
            <option value="planifie">Planifie</option>
            <option value="termine">Termine</option>
            <option value="annule">Annule</option>
        </select>

        <label>Notes</label>
        <textarea name="notes" rows="2"></textarea>

        <button type="submit" class="btn btn-primary btn-full">
            <i class="fa-solid fa-calendar-plus"></i> Enregistrer le rendez-vous
        </button>
    </form>
</div>