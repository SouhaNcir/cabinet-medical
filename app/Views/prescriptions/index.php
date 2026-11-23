<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Creer une ordonnance</h2>
            <p>Associer des medicaments et posologies a une consultation</p>
        </div>
        <?php if (($user['role'] ?? '') === 'medecin'): ?>
            <a href="<?= e(BASE_URL) ?>/prescription/create" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Creer une ordonnance
            </a>
        <?php endif; ?>
    </div>

    <?php if (($user['role'] ?? '') === 'medecin'): ?>
        <p class="notification notification-success">Cliquez sur le bouton pour ouvrir le formulaire de création.</p>
    <?php else: ?>
        <p class="notification notification-error">Seul le medecin peut creer une ordonnance.</p>
    <?php endif; ?>
</div>

<div class="panel reveal">
    <h3>Ordonnances recentes</h3>
    <div class="table-wrap">
        <table class="modern-table">
            <thead>
            <tr>
                <th>ID</th>
                <th>Patient</th>
                <th>Medecin</th>
                <th>Date consultation</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($prescriptions as $pr): ?>
                <tr>
                    <td>#<?= (int) $pr['id'] ?></td>
                    <td><?= e($pr['patient_name']) ?></td>
                    <td><?= e($pr['doctor_name']) ?></td>
                    <td><?= e(date('d/m/Y', strtotime((string) $pr['consultation_date']))) ?></td>
                    <td>
                        <div class="actions">
                            <a class="btn-table" target="_blank" href="<?= e(BASE_URL) ?>/prescription/print/<?= (int) $pr['id'] ?>">
                                <i class="fa-solid fa-print"></i>
                            </a>
                            <?php if (($user['role'] ?? '') === 'medecin'): ?>
                                <form method="post" action="<?= e(BASE_URL) ?>/prescription/delete/<?= (int) $pr['id'] ?>" onsubmit="return confirm('Supprimer cette ordonnance ?');">
                                    <input type="hidden" name="_token" value="<?= e($csrf) ?>">
                                    <button type="submit" class="btn-table btn-danger"><i class="fa-solid fa-trash"></i></button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($prescriptions)): ?>
                <tr><td colspan="5" class="empty-cell">Aucune ordonnance disponible.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
