<style>
    .rx-page {
        width: 210mm;
        min-height: 297mm;
        margin: 12px auto;
        background: #fff;
        border: 1px solid #d8e3ea;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.14);
        position: relative;
        display: flex;
        flex-direction: column;
        color: #0f172a;
        font-family: 'Manrope', sans-serif;
    }

    .rx-toolbar {
        max-width: 210mm;
        margin: 24px auto 10px;
        text-align: right;
    }

    .rx-header {
        padding: 18mm 14mm 8mm;
        border-bottom: 2px solid #6fc0cc;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: start;
        gap: 12px;
        font-size: 12px;
    }

    .rx-head-block {
        line-height: 1.45;
    }

    .rx-head-block.right {
        text-align: right;
    }

    .rx-head-title {
        font-weight: 800;
    }

    .rx-head-icon {
        text-align: center;
        color: #61beca;
        font-size: 34px;
        line-height: 1;
        margin-top: 2px;
    }

    .rx-body {
        position: relative;
        padding: 8mm 14mm 20mm;
        flex: 1;
    }

    .rx-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        text-decoration: underline;
        margin: 0 0 10mm;
    }

    .rx-meta {
        display: grid;
        gap: 10px;
        font-size: 13px;
        margin-bottom: 10mm;
    }

    .rx-line {
        display: grid;
        grid-template-columns: auto 1fr auto 1fr;
        align-items: center;
        gap: 8px;
    }

    .rx-blank {
        border-bottom: 1px dotted #6b7280;
        min-height: 20px;
        display: flex;
        align-items: center;
        padding: 0 4px;
    }

    .rx-list {
        margin-top: 12px;
        display: grid;
        gap: 12px;
    }

    .rx-item {
        border-bottom: 1px dashed #d1d5db;
        padding-bottom: 8px;
        line-height: 1.5;
        font-size: 13px;
    }

    .rx-notes {
        margin-top: 10mm;
        font-size: 13px;
        white-space: pre-wrap;
    }

    .rx-watermark {
        position: absolute;
        inset: 0;
        pointer-events: none;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #89cdd6;
        opacity: 0.12;
        font-size: 220px;
        z-index: 0;
    }

    .rx-content {
        position: relative;
        z-index: 1;
    }

    .rx-footer {
        border-top: 2px solid #6fc0cc;
        background: #72c7d1;
        color: #083344;
        padding: 8px 14mm;
        text-align: center;
        font-size: 11px;
        line-height: 1.4;
    }

    @page {
        size: A4;
        margin: 0;
    }

    @media print {
        body.auth-body {
            background: #fff !important;
            padding: 0 !important;
        }

        #globalLoader,
        .rx-toolbar {
            display: none !important;
        }

        .rx-page {
            margin: 0;
            border: none;
            box-shadow: none;
            width: 210mm;
            min-height: 297mm;
        }
    }
</style>

<div class="rx-toolbar">
    <button class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimer</button>
</div>

<article class="rx-page">
    <header class="rx-header">
        <div class="rx-head-block">
            <div class="rx-head-title">Dr. <?= e($prescription['doctor_name']) ?></div>
            <div>Medecin general</div>
            <div>Tel: 0522 000 000</div>
            <div>Adresse: cabinet@email.com</div>
        </div>

        <div class="rx-head-icon"><i class="fa-solid fa-staff-snake"></i></div>

        <div class="rx-head-block right">
            <div class="rx-head-title"><?= e(APP_NAME) ?></div>
            <div>Adresse: Votre adresse ici</div>
            <div>Tel: 0522 000 000</div>
        </div>
    </header>

    <section class="rx-body">
        <div class="rx-watermark"><i class="fa-solid fa-staff-snake"></i></div>

        <div class="rx-content">
            <h1 class="rx-title">Ordonnance Medicale</h1>

            <div class="rx-meta">
                <div class="rx-line">
                    <strong>Fait a:</strong>
                    <span class="rx-blank">Casablanca</span>
                    <strong>Le:</strong>
                    <span class="rx-blank"><?= e(date('d / m / Y', strtotime((string) $prescription['consultation_date']))) ?></span>
                </div>
                <div class="rx-line">
                    <strong>Nom & Prenom:</strong>
                    <span class="rx-blank" style="grid-column: span 3;"><?= e($prescription['patient_name']) ?></span>
                </div>
            </div>

            <div class="rx-list">
                <?php foreach ($medications as $m): ?>
                    <div class="rx-item">
                        <strong><?= e($m['name'] ?? '') ?></strong><br>
                        Posologie: <?= e($m['dosage'] ?? '-') ?><br>
                        Frequence: <?= e($m['frequency'] ?? '-') ?>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($medications)): ?>
                    <div class="rx-item">Aucun medicament enregistre.</div>
                <?php endif; ?>
            </div>

            <?php if (!empty($prescription['notes'])): ?>
                <div class="rx-notes"><strong>Notes:</strong> <?= e($prescription['notes']) ?></div>
            <?php endif; ?>
        </div>
    </section>

    <footer class="rx-footer">
        Adresse: 123, Avenue la presse, ville - Tel: 0522 000 000 - 0522 000 000<br>
        cabinet@email.com
    </footer>
</article>
