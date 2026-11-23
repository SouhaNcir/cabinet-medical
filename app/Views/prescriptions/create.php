<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Creer une ordonnance</h2>
            <p>Associer des medicaments et posologies a une consultation</p>
        </div>
        <a href="<?= e(BASE_URL) ?>/prescription/index" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Retour ordonnances
        </a>
    </div>

    <?php if (($user['role'] ?? '') === 'medecin'): ?>
        <form method="post" action="<?= e(BASE_URL) ?>/prescription/store" class="form-grid" id="prescriptionForm">
            <input type="hidden" name="_token" value="<?= e($csrf) ?>">

            <label>Consultation *</label>
            <select name="consultation_id" required>
                <option value="">Selectionner...</option>
                <?php foreach ($consultations as $c): ?>
                    <option value="<?= (int) $c['id'] ?>">
                        <?= e($c['patient_name']) ?> - <?= e(date('d/m/Y', strtotime((string) $c['consultation_date']))) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <div id="medicationRows" class="form-grid">
                <div class="med-row">
                    <input type="text" name="med_name[]" placeholder="Medicament" required>
                    <input type="text" name="dosage[]" placeholder="Posologie ex: 1 cp matin/soir">
                    <input type="text" name="frequency[]" placeholder="Frequence / duree">
                </div>
            </div>

            <button type="button" class="btn btn-outline" id="addMedicationRow">
                <i class="fa-solid fa-plus"></i> Ajouter un medicament
            </button>

            <label>Notes</label>
            <textarea name="notes" rows="3"></textarea>

            <button type="submit" class="btn btn-primary btn-full">
                <i class="fa-solid fa-file-prescription"></i> Enregistrer ordonnance
            </button>
        </form>
    <?php else: ?>
        <p class="notification notification-error">Seul le medecin peut creer une ordonnance.</p>
    <?php endif; ?>
</div>
