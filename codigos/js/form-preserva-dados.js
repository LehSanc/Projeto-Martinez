document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form.needs-validation');

    if (form) {
        // Identificador único para a página/formulário
        const pageId = form.getAttribute('data-page-id');

        // Função para obter chave única para o localStorage com base no pageId
        const getStorageKey = () => `formData-${pageId}`;

        // Captura o evento de submit do formulário
        form.addEventListener('submit', event => {
            // Verifica se o formulário é válido
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Cria um objeto para armazenar os dados do formulário
            const formData = {};
            const inputs = form.querySelectorAll('input, select, textarea');

            // Itera sobre cada campo do formulário e armazena no objeto formData
            inputs.forEach(input => {
                // Verifica o tipo de campo e decide se deve ser armazenado no local Storage
                if (input.type !== 'file') {
                    if (input.type === 'radio') {
                        if (input.checked) {
                            const auxNumber = input.value;
                            if (auxNumber !== '0') {
                                formData[`aux${auxNumber}`] = form.querySelector(`#aux${auxNumber}`).value;
                            }
                        }
                    } else {
                        formData[input.name] = input.value;
                    }
                }
            });

            // Salva o objeto formData no localStorage como JSON
            localStorage.setItem(getStorageKey(), JSON.stringify(formData));
        });

        // Restaura os dados do formulário ao carregar a página
        const storedFormData = localStorage.getItem(getStorageKey());
        if (storedFormData) {
            const formData = JSON.parse(storedFormData);
            Object.keys(formData).forEach(key => {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type !== 'file') {
                        if (input.type === 'radio') {
                            const auxNumber = input.value;
                            if (auxNumber !== '0' && formData[key] !== undefined) {
                                input.checked = true;
                                form.querySelector(`#aux${auxNumber}`).value = formData[key];
                            }
                        } else {
                            input.value = formData[key];
                        }
                    }
                }
            });
        }
    }
});
