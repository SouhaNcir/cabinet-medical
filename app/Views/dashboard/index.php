<?php $role = $user['role'] ?? 'guest'; ?>

<section class="dashboard-hero reveal">
    <div>
        <p class="dashboard-kicker"><?= $role === 'medecin' ? 'Espace clinique' : 'Espace administratif' ?></p>
        <h2>Dashboard</h2>
        <p class="dashboard-subtitle">Bienvenue, Dr. Yassine Alaoui</p>
    </div>
</section>

<section class="stats-grid">
    <article class="stat-card reveal">
        <div class="stat-icon stat-icon-primary"><i class="fa-solid fa-users"></i></div>
        <div class="stat-copy">
            <p>Total patients</p>
            <h3><?= e((string) ($stats['total'] ?? 0)) ?></h3>
            <span class="stat-trend">Vue d'ensemble du registre patient</span>
        </div>
    </article>

    <article class="stat-card reveal">
        <div class="stat-icon stat-icon-success"><i class="fa-solid fa-mars"></i></div>
        <div class="stat-copy">
            <p>Patients hommes</p>
            <h3><?= e((string) ($stats['men'] ?? 0)) ?></h3>
            <span class="stat-trend">Répartition actuelle</span>
        </div>
    </article>

    <article class="stat-card reveal">
        <div class="stat-icon stat-icon-danger"><i class="fa-solid fa-venus"></i></div>
        <div class="stat-copy">
            <p>Patients femmes</p>
            <h3><?= e((string) ($stats['women'] ?? 0)) ?></h3>
            <span class="stat-trend">Répartition actuelle</span>
        </div>
    </article>
</section>

<section class="dashboard-card reveal activity-card">
    <header class="dashboard-card-header activity-header">
        <div>
            <h3>Activité récente</h3>
            <p>Dernières consultations enregistrées dans le cabinet.</p>
        </div>

        <a href="<?= e(BASE_URL) ?>/consultation/index" class="activity-view-all">Voir tout</a>
    </header>

    <div class="activity-table-wrap">
        <table class="activity-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Type</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (($consultations_recentes ?? []) as $consultation): ?>
                    <tr>
                        <td><?= e(date('d/m/Y H:i', strtotime((string) $consultation['consultation_date']))) ?></td>
                        <td><?= e($consultation['patient_name']) ?></td>
                        <td>Consultation</td>
                        <td>
                            <?php
                                // Payment removed: show neutral 'Enregistrée' status
                                $statusLabel = 'Enregistrée';
                                $statusClass = 'badge-info';
                            ?>
                            <span class="activity-badge <?= e($statusClass) ?>"><?= e($statusLabel) ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($consultations_recentes)): ?>
                    <tr>
                        <td colspan="4" class="activity-empty">Aucune activité récente.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
