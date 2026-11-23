<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Consultations</h2>
            <p>Historique des consultations.</p>
        </div>
        <?php if (($user['role'] ?? '') === 'medecin'): ?>
            <a href="<?= e(BASE_URL) ?>/consultation/create" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter consultation
            </a>
        <?php endif; ?>
    </div>

    <?php if (($user['role'] ?? '') !== 'medecin'): ?>
        <p class="notification notification-success">Mode secretaire: consultation en lecture et gestion administrative.</p>
    <?php endif; ?>
</div>

<div class="panel reveal">
    <h3>Historique des consultations (timeline)</h3>
    <div class="timeline">
        <?php foreach ($consultations as $c): ?>
            <article class="timeline-item">
                <div class="timeline-dot"></div>
                <div class="timeline-card">
                    <header>
                        <strong><?= e($c['patient_name']) ?></strong>
                        <span><?= e(date('d/m/Y H:i', strtotime((string) $c['consultation_date']))) ?></span>
                    </header>
                    <p><strong>Diagnostic:</strong> <?= e($c['diagnosis']) ?></p>
                    <p><strong>Traitement:</strong> <?= e($c['treatment']) ?></p>
                    <p>
                        <span class="status-badge status-info">Enregistrée</span>
                    </p>

                    <div class="toolbar">
                        <a class="btn btn-outline" href="<?= e(BASE_URL) ?>/consultation/show/<?= (int) $c['id'] ?>">
                            <i class="fa-regular fa-file-lines"></i> Voir fiche
                        </a>
                        <?php if (($user['role'] ?? '') === 'medecin'): ?>
                            <form method="post" action="<?= e(BASE_URL) ?>/consultation/delete/<?= (int) $c['id'] ?>" onsubmit="return confirm('Supprimer cette consultation ?');">
                                <input type="hidden" name="_token" value="<?= e($csrf) ?>">
                                <button class="btn btn-danger" type="submit"><i class="fa-solid fa-trash"></i> Supprimer</button>
                            </form>
                        <?php endif; ?>
                    </div>

                </div>
            </article>
        <?php endforeach; ?>

        <?php if (empty($consultations)): ?>
            <p class="empty-cell">Aucune consultation enregistree.</p>
        <?php endif; ?>
    </div>
</div>


