document.addEventListener('DOMContentLoaded', () => {
    const formatCurrentDatetimeLocal = () => {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    };

    const loader = document.getElementById('globalLoader');
    if (loader) {
        setTimeout(() => loader.classList.add('hidden'), 260);
    }

    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });
    }

    document.querySelectorAll('[data-open-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-open-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('active');
                modal.setAttribute('aria-hidden', 'false');
            }
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-close-modal');
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });

    document.querySelectorAll('.modal').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.classList.remove('active');
                modal.setAttribute('aria-hidden', 'true');
            }
        });
    });

    const searchInput = document.getElementById('patientSearch');
    const table = document.getElementById('patientsTable');

    if (searchInput && table) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            table.querySelectorAll('tbody tr').forEach((row) => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(q) ? '' : 'none';
            });
        });
    }

    document.querySelectorAll('[data-auto-dismiss="true"]').forEach((el) => {
        setTimeout(() => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 220);
        }, 3200);
    });

    const addMedicationRowBtn = document.getElementById('addMedicationRow');
    const medicationRows = document.getElementById('medicationRows');

    if (addMedicationRowBtn && medicationRows) {
        addMedicationRowBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'med-row';
            row.innerHTML = `
                <input type="text" name="med_name[]" placeholder="Medicament" required>
                <input type="text" name="dosage[]" placeholder="Posologie ex: 1 cp matin/soir">
                <input type="text" name="frequency[]" placeholder="Frequence / duree">
                <button type="button" class="btn-inline-remove">Supprimer</button>
            `;
            medicationRows.appendChild(row);
        });

        medicationRows.addEventListener('click', (event) => {
            const target = event.target;
            if (target instanceof HTMLElement && target.classList.contains('btn-inline-remove')) {
                const row = target.closest('.med-row');
                if (row) {
                    row.remove();
                }
            }
        });
    }

    const consultationPatient = document.getElementById('consultationPatient');
    const consultationDate = document.getElementById('consultationDate');

    if (consultationDate instanceof HTMLInputElement && consultationDate.value.trim() === '') {
        consultationDate.value = formatCurrentDatetimeLocal();
    }

    if (consultationPatient instanceof HTMLSelectElement && consultationDate instanceof HTMLInputElement) {
        consultationPatient.addEventListener('change', () => {
            if (consultationPatient.value !== '') {
                consultationDate.value = formatCurrentDatetimeLocal();
            }
        });
    }

    const passwordInput = document.getElementById('password');
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordToggleIcon = document.getElementById('passwordToggleIcon');

    if (passwordInput instanceof HTMLInputElement && passwordToggle instanceof HTMLButtonElement) {
        passwordToggle.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';
            passwordToggle.setAttribute('aria-label', isPassword ? 'Masquer le mot de passe' : 'Afficher le mot de passe');

            if (passwordToggleIcon instanceof HTMLElement) {
                passwordToggleIcon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            }
        });
    }
});
