const forms = document.querySelectorAll('.form');
forms.forEach((form) => {
    const container = form.querySelector('.editor');
    form.insertAdjacentElement('beforebegin', container);

    const quill = new Quill(container, {
        modules: {
            toolbar: [
                [{header: [1, 2, false]}],
                ['bold', 'italic', 'underline', 'strike'],
                [{'list': 'ordered'}, {'list': 'bullet'}],
                [{'color': []}, {'background': []}],
                ['link', 'image'],
            ],
        },
        placeholder: 'Het volgende deel gaat over...',
        theme: 'snow',
    });

    const buttonContainer = document.createElement('div');
    buttonContainer.className = 'button-container mb-5 mt-2';
    buttonContainer.style.display = 'none';

    const resetButton = document.createElement('button');
    resetButton.type = 'reset';
    resetButton.className = 'btn btn-outline-primary me-2';
    resetButton.innerText = 'Annuleren';

    const saveButton = document.createElement('button');
    saveButton.type = 'submit';
    saveButton.className = 'btn btn-primary';
    saveButton.innerText = 'Opslaan';

    resetButton.addEventListener('click', () => reloadPage());

    form.addEventListener('change', () => {
        showButtons();
    });
    container.addEventListener('click', () => {
        showButtons();
    });
    container.addEventListener('keydown', () => {
        showButtons();
    });

    buttonContainer.append(resetButton, saveButton);
    form.append(buttonContainer);
    form.addEventListener('submit', () => {
        let input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'content';
        input.value = DOMPurify.sanitize(quill.getSemanticHTML());
        form.append(input);
    });
});

let toolbars = document.querySelectorAll('.ql-toolbar');
toolbars.forEach((toolbar) => {
    toolbar.style.backgroundColor = 'white';
});

function reloadPage() {
    if (confirm('Weet je zeker dat je de wijzigingen wilt annuleren?')) {
        window.location.reload();
    }
}

function showButtons() {
    const buttonContainers = document.querySelectorAll('.button-container');
    buttonContainers.forEach((container) => {
        container.style.removeProperty('display');
    });
}
