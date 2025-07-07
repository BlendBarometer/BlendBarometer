const customQuestionForm = document.getElementById('custom_input');
const customQuestionBtn = document.getElementById('addCustomQuestionBtn');
const form = document.querySelector('form');

function addQuestion() {
    const txt = customQuestionForm.value.trim();
    if (!txt) return alert('Vul een vraag in!');

    // id and wrapper
    const uid = Date.now(); // unique ID based on timestamp, so no double IDs in html
    const idBase = 'custom_question_' + txt.toLowerCase().replace(/[^a-z0-9]+/g, '_') + '_' + uid;
    const wrapper = document.createElement('div');
    wrapper.classList.add('mb-5');

    const headerGroup = document.createElement('div');
    headerGroup.className = 'mb-2';
    wrapper.appendChild(headerGroup);

    // delete knop
    const button = document.createElement('button');
    button.className = 'btn btn-sm btn-danger me-2';
    button.addEventListener('click', function() {
        wrapper.remove();
    });

    // visuele prullenbak
    const trashBin = document.createElement('i');
    trashBin.className = 'bi bi-trash';
    button.appendChild(trashBin);
    headerGroup.appendChild(button);

    // question title
    const label = document.createElement('label');
    label.htmlFor = idBase;
    label.className = 'fw-semibold';
    label.textContent = txt;
    headerGroup.appendChild(label);
    
    // options container
    const row = document.createElement('div');
    row.className = 'row gap-4 mx-0';
    row.setAttribute('role', 'group');

    const options = [
        {
            id: 'nooit',
            emoji: {
                img: 'nooit.png',
                alt: 'Emoji met hand voor mond'
            },
            label: 'Nooit',
            value: 0
        },
        {
            id: 'af_en_toe',
            emoji: {
                img: 'af-en-toe.png',
                alt: 'Emoji met lichte glimlach'
            },
            label: 'Af en toe',
            value: 1
        },
        {
            id: 'vaak',
            emoji: {
                img: 'vaak.png',
                alt: 'Emoji met grote glimlach'
            },
            label: 'Vaak',
            value: 2
        },
    ];

    options.forEach(opt => {
        const inp = document.createElement('input');
        inp.type = 'radio';
        inp.className = 'form-check-input visually-hidden';
        inp.name = idBase;
        inp.id = `${idBase}_${opt.id}`;
        inp.value = opt.value;
        inp.required = true;
        inp.autocomplete = 'off';
        inp.setAttribute('aria-label', opt.label + " - " + txt);

        const lbl = document.createElement('label');
        lbl.className = 'form-check-label border-2 border rounded col py-4 d-flex flex-column justify-content-center align-items-center explicit-focus-visible';
        lbl.htmlFor = inp.id;
        lbl.innerHTML = `
            <img src="/images/emoji/${opt.emoji.img}" alt="${opt.emoji.alt}" style="width: 50px; height: 50px;">
            <span class="mt-2 text-nowrap">${opt.label}</span>
        `;

        row.append(inp, lbl);
    });

    // add the options to the wrapper
    wrapper.appendChild(row);
    document.getElementById('custom-question-container').appendChild(wrapper);

    // reset state
    customQuestionForm.value = '';
    customQuestionForm.focus();
}

document.getElementById('addCustomQuestionBtn').addEventListener('click', addQuestion);

let customInputSelected = false;

customQuestionBtn.addEventListener("focus", () => {
    customInputSelected = true;
});
customQuestionBtn.addEventListener("blur", () => {
    customInputSelected = false;
});

customQuestionForm.addEventListener("focus", () => {
    customInputSelected = true;
});
customQuestionForm.addEventListener("blur", () => {
    customInputSelected = false;
});

addEventListener("keydown", (e) => {
    if (e.key === "Enter") {
        if (customInputSelected) {
            addQuestion();
            e.preventDefault();
        } else if (form.reportValidity()) {
            form.submit();
        }
    }
});