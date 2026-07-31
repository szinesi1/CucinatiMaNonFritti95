document.addEventListener('DOMContentLoaded', () => {

    const modal = document.getElementById('deleteModal');
    const closeBtn = document.getElementById('closeModal');

    const modalIngrediente = document.getElementById('modalIngrediente');
    const inputId = document.getElementById('inputIdIngrediente');
    const inputNumero = document.getElementById('inputNumero');

    document.querySelectorAll('.js-delete-btn').forEach(btn => {

        btn.addEventListener('click', (e) => {
            e.preventDefault();

            const id = btn.dataset.id;
            const ingrediente = btn.dataset.ingrediente;
            const numero = btn.dataset.numero;

            modalIngrediente.textContent = ingrediente;
            inputId.value = id;
            inputNumero.value = numero;

            modal.classList.remove('hidden');
        });

    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.classList.add('hidden');
        });
    }

});