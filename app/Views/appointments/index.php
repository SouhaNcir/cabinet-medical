<?php $role = $role ?? ($user['role'] ?? 'guest'); ?>
<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Planning des rendez-vous</h2>
            <p><?= $role === 'medecin' ? 'Consultation simple des rendez-vous et des disponibilites.' : 'Ajout et annulation des rendez-vous pour le secretariat.' ?></p>
        </div>
        <?php if ($role === 'secretaire'): ?>
            <a href="<?= e(BASE_URL) ?>/appointment/create" class="btn btn-primary">
                <i class="fa-solid fa-plus"></i> Ajouter un rendez-vous
            </a>
        <?php endif; ?>
    </div>

    <form method="get" action="<?= e(BASE_URL) ?>/appointment/index" class="toolbar">
        <label for="dateFilter">Filtrer par date</label>
        <input id="dateFilter" type="date" name="date" value="<?= e($selectedDate) ?>">
        <button class="btn btn-outline" type="submit">Filtrer</button>
        <a class="btn btn-outline" href="<?= e(BASE_URL) ?>/appointment/index">Reinitialiser</a>
    </form>

    <?php if ($role === 'medecin'): ?>
        <div class="panel role-readonly-banner" style="margin-top: 10px;">
            <strong>Mode consultation</strong>
            <p>Le medecin peut seulement consulter le planning des rendez-vous.</p>
        </div>
    <?php endif; ?>
</div>

<div class="panel reveal">
    <h3>Calendrier / Liste rendez-vous</h3>
    <div class="table-wrap">
        <table class="modern-table">
            <thead>
            <tr>
                <th>Date</th>
                <th>Patient</th>
                <th>Medecin</th>
                <th>Duree</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($appointments as $row): ?>
                <tr>
                    <td><?= e(date('d/m/Y H:i', strtotime((string) $row['appointment_at']))) ?></td>
                    <td><?= e($row['patient_name']) ?></td>
                    <td><?= e($row['doctor_name']) ?></td>
                    <td><?= (int) $row['duration_minutes'] ?> min</td>
                    <td>
                        <span class="status-badge <?= $row['status'] === 'annule' ? 'notification-error' : ($row['status'] === 'termine' ? 'status-ok' : 'status-info') ?>">
                            <?= e(ucfirst($row['status'])) ?>
                        </span>
                    </td>
                    <td class="actions">
                        <?php if ($role === 'secretaire'): ?>
                            <form method="post" action="<?= e(BASE_URL) ?>/appointment/delete/<?= (int) $row['id'] ?>" onsubmit="return confirm('Annuler ce rendez-vous ?');">
                                <input type="hidden" name="_token" value="<?= e($csrf) ?>">
                                <button type="submit" class="btn-table btn-danger"><i class="fa-solid fa-ban"></i></button>
                            </form>
                        <?php else: ?>
                            <span class="table-readonly">Lecture seule</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($appointments)): ?>
                <tr><td colspan="6" class="empty-cell">Aucun rendez-vous disponible.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
