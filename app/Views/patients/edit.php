<div class="panel reveal">
    <div class="panel-header">
        <div>
            <h2>Modifier patient</h2>
            <p>Mise a jour des informations patient</p>
        </div>
        <a class="btn btn-outline" href="<?= e(BASE_URL) ?>/patient/index"><i class="fa-solid fa-arrow-left"></i> Retour</a>
    </div>

    <form method="post" action="<?= e(BASE_URL) ?>/patient/update/<?= e((string) $patient['id']) ?>" class="form-grid">
        <input type="hidden" name="_token" value="<?= e($csrf) ?>">

        <label>Nom complet *</label>
        <input type="text" name="full_name" value="<?= e($patient['full_name']) ?>" required>

        <label>CIN *</label>
        <input type="text" name="cin" value="<?= e($patient['cin']) ?>" required>

        <label>Telephone *</label>
        <input type="text" name="phone" value="<?= e($patient['phone']) ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= e($patient['email']) ?>">

        <label>Genre *</label>
        <select name="gender" required>
            <option value="M" <?= $patient['gender'] === 'M' ? 'selected' : '' ?>>Homme</option>
            <option value="F" <?= $patient['gender'] === 'F' ? 'selected' : '' ?>>Femme</option>
        </select>

        <label>Date de naissance *</label>
        <input type="date" name="birth_date" value="<?= e($patient['birth_date']) ?>" required>

        <label>Adresse</label>
        <textarea name="address" rows="4"><?= e($patient['address']) ?></textarea>

        <button type="submit" class="btn btn-primary btn-full"><i class="fa-solid fa-rotate"></i> Mettre a jour</button>
    </form>
</div>
