<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Fiche de consultation</h2>
            <p>Détail complet de la consultation sélectionnée.</p>
        </div>
        <a href="<?= e(BASE_URL) ?>/consultation/index" class="btn btn-outline">
            <i class="fa-solid fa-arrow-left"></i> Retour consultations
        </a>
    </div>

    <div class="form-grid" style="grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px;">
        <div class="panel">
            <strong>Patient</strong>
            <p><?= e($consultation['patient_name']) ?></p>
            <small>CIN: <?= e($consultation['cin'] ?? '-') ?></small><br>
            <small>Téléphone: <?= e($consultation['phone'] ?? '-') ?></small>
        </div>
        <div class="panel">
            <strong>Médecin</strong>
            <p><?= e($consultation['doctor_name']) ?></p>
            <small>Date: <?= e(date('d/m/Y H:i', strtotime((string) $consultation['consultation_date']))) ?></small>
        </div>
    </div>

    <div class="panel" style="margin-top: 12px;">
        <h3>Symptômes</h3>
        <p><?= nl2br(e($consultation['symptoms'] ?? '')) ?></p>

        <h3>Diagnostic</h3>
        <p><?= nl2br(e($consultation['diagnosis'] ?? '')) ?></p>

        <h3>Traitement</h3>
        <p><?= nl2br(e($consultation['treatment'] ?? '')) ?></p>
    </div>
</div>