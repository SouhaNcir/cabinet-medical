<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Liste des patients</h2>
            <p><?= e((string) $total) ?> patient(s) trouves</p>
        </div>
        <button class="btn btn-primary" data-open-modal="patientCreateModal">
            <i class="fa-solid fa-plus"></i> Nouveau patient
        </button>
    </div>

    <form method="get" action="<?= e(BASE_URL) ?>/patient/index" class="toolbar">
        <input type="hidden" name="url" value="patient/index">
        <div class="search-wrap">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input id="patientSearch" type="text" name="search" value="<?= e($search) ?>" placeholder="Rechercher par nom, CIN ou telephone...">
        </div>
        <button class="btn btn-outline" type="submit">Rechercher</button>
    </form>

    <div class="table-wrap">
        <table class="modern-table" id="patientsTable">
            <thead>
            <tr>
                <th>Nom complet</th>
                <th>CIN</th>
                <th>Telephone</th>
                <th>Email</th>
                <th>Genre</th>
                <th>Date naissance</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($patients as $patient): ?>
                <tr>
                    <td><?= e($patient['full_name']) ?></td>
                    <td><?= e($patient['cin']) ?></td>
                    <td><?= e($patient['phone']) ?></td>
                    <td><?= e($patient['email'] ?? '-') ?></td>
                    <td>
                        <span class="status-badge <?= $patient['gender'] === 'M' ? 'status-info' : 'status-ok' ?>">
                            <?= e($patient['gender']) ?>
                        </span>
                    </td>
                    <td><?= e($patient['birth_date']) ?></td>
                    <td class="actions">
                        <a class="btn-table" href="<?= e(BASE_URL) ?>/patient/edit/<?= e((string) $patient['id']) ?>"><i class="fa-solid fa-pen"></i></a>

                        <?php if (($user['role'] ?? '') === 'secretaire'): ?>
                            <form method="post" action="<?= e(BASE_URL) ?>/patient/delete/<?= e((string) $patient['id']) ?>" onsubmit="return confirm('Confirmer la suppression ?');">
                                <input type="hidden" name="_token" value="<?= e($csrf) ?>">
                                <button type="submit" class="btn-table btn-danger"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($patients)): ?>
                <tr>
                    <td colspan="7" class="empty-cell">Aucun patient trouve.</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a class="page-link <?= $i === $page ? 'active' : '' ?>"
               href="<?= e(BASE_URL) ?>/patient/index?search=<?= urlencode($search) ?>&page=<?= $i ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
</div>

<div class="modal" id="patientCreateModal" aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nouveau patient</h3>
            <button type="button" class="btn-icon" data-close-modal="patientCreateModal"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form method="post" action="<?= e(BASE_URL) ?>/patient/store" class="form-grid modal-form">
            <input type="hidden" name="_token" value="<?= e($csrf) ?>">

            <label>Nom complet *</label>
            <input type="text" name="full_name" required>

            <label>CIN *</label>
            <input type="text" name="cin" required>

            <label>Telephone *</label>
            <input type="text" name="phone" required>

            <label>Email</label>
            <input type="email" name="email">

            <label>Genre *</label>
            <select name="gender" required>
                <option value="">Selectionner...</option>
                <option value="M">Homme</option>
                <option value="F">Femme</option>
            </select>

            <label>Date de naissance *</label>
            <input type="date" name="birth_date" required>

            <label>Adresse</label>
            <textarea name="address" rows="3"></textarea>

            <button type="submit" class="btn btn-primary btn-full">
                <i class="fa-solid fa-floppy-disk"></i> Enregistrer
            </button>
        </form>
    </div>
</div>
