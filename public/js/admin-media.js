document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-remove-url]');

    if (!button) {
        return;
    }

    const message = button.dataset.confirm || 'Remove this image?';

    if (!window.confirm(message)) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = button.dataset.removeUrl;
    form.style.display = 'none';

    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = button.dataset.csrf || '';
    form.appendChild(token);

    const method = document.createElement('input');
    method.type = 'hidden';
    method.name = '_method';
    method.value = 'DELETE';
    form.appendChild(method);

    document.body.appendChild(form);
    form.submit();
});
