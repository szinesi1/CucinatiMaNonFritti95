document.addEventListener('DOMContentLoaded', function () {
    const toggleButton = document.getElementById('toggleFilters');
    const filterPanel = document.getElementById('advancedFilters');

    if (toggleButton && filterPanel) {
        const updateFilterButton = function () {
            const isOpen = filterPanel.classList.contains('open');
            toggleButton.textContent = isOpen
                ? toggleButton.dataset.openLabel
                : toggleButton.dataset.closedLabel;
            toggleButton.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        toggleButton.addEventListener('click', function () {
            filterPanel.classList.toggle('open');
            updateFilterButton();
        });
        updateFilterButton();
    }

    const modal = document.getElementById('deleteModal');
    const closeButton = document.getElementById('closeModal');
    const ingredientName = document.getElementById('modalIngrediente');
    const ingredientId = document.getElementById('inputIdIngrediente');
    const recipeNumber = document.getElementById('inputNumero');

    if (modal && ingredientName && ingredientId && recipeNumber) {
        document.querySelectorAll('.js-delete-btn').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                ingredientName.textContent = button.dataset.ingrediente;
                ingredientId.value = button.dataset.id;
                recipeNumber.value = button.dataset.numero;
                modal.classList.remove('hidden');
            });
        });
    }

    if (modal && closeButton) {
        closeButton.addEventListener('click', function () {
            modal.classList.add('hidden');
        });
    }
});
