(function () {
  document.querySelectorAll('[data-password-toggle]').forEach((btn) => {
    const inputId = btn.getAttribute('data-password-toggle');
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');

    if (!input || !icon) {
      return;
    }

    btn.addEventListener('click', () => {
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      icon.classList.toggle('bi-eye', !show);
      icon.classList.toggle('bi-eye-slash', show);
      btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
    });
  });
})();
