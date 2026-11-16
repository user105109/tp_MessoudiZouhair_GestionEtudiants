document.addEventListener('DOMContentLoaded', () => {
    // 1. Confirmation avant suppression
    const deleteLinks = document.querySelectorAll('.btn-delete');
    deleteLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            if (!confirm('Voulez-vous vraiment supprimer cet étudiant ?')) {
                e.preventDefault();
            }
        });
    });

    // 2. Recherche instantanée dans le tableau sans recharger la page
    const searchInput = document.getElementById('search-input');
    const rows = document.querySelectorAll('.ligne-etudiant');

    if (searchInput && rows.length > 0) {
        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase().trim();

            rows.forEach(row => {
                const nom = row.querySelector('.col-nom')?.textContent.toLowerCase() || '';
                const prenom = row.querySelector('.col-prenom')?.textContent.toLowerCase() || '';

                if (nom.includes(query) || prenom.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
});
