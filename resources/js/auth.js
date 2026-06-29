
const inputs = document.querySelectorAll('input.rectangle');

inputs.forEach((input, index) => {
    input.addEventListener('input', () => {
        const reg = /\D/;
        if (reg.test(input.value)) {
            input.value = '';
            return;
        }
        else if (input.value.length === 1 && index < inputs.length - 1) {
            inputs[index + 1].focus();
        }
    });

    input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && input.value.length === 0 && index > 0) {
            inputs[index - 1].focus();
        }
    });

    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = e.clipboardData.getData('text');

        const reg = /\D/g;
        const digits = paste.replace(reg, '');

        for (i = 0; i < inputs.length && i < digits.length; i++) {
            inputs[i].value = digits[i];
        }
    });
});

document.getElementById('codeForm').addEventListener('submit', () => {
    let code = '';
    inputs.forEach((input) => {
        code += input.value;
    })

    const form = document.getElementById('codeForm');
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'code';
    input.value = code;

    form.append(input);
});
