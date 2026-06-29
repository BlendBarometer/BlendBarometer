document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.module-info-active-toggle').forEach((toggle) => {
        const syncActiveState = () => {
            const id = toggle.id.replace('field-active-', '');
            const label = document.getElementById(`field-active-label-${id}`);

            if (label) {
                label.textContent = toggle.checked ? 'Actief' : 'Inactief';
            }
        };

        toggle.addEventListener('change', () => {
            syncActiveState();
        });

        syncActiveState();
    });

    document.querySelectorAll('.module-info-delete-button').forEach((button) => {
        button.addEventListener('click', () => {
            const confirmed = window.confirm(
                'Weet je zeker dat je dit veld wilt verwijderen?\n\nTip: je kunt dit veld ook uitschakelen met de Actief-schakelaar.'
            );

            if (!confirmed) {
                return;
            }

            const csrfTokenInput = document.querySelector('input[name="_token"]');
            const csrfToken = csrfTokenInput ? csrfTokenInput.value : '';

            if (!csrfToken || !button.dataset.deleteUrl) {
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = button.dataset.deleteUrl;

            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = csrfToken;

            const method = document.createElement('input');
            method.type = 'hidden';
            method.name = '_method';
            method.value = 'DELETE';

            form.appendChild(csrf);
            form.appendChild(method);
            document.body.appendChild(form);
            form.submit();
        });
    });
});
