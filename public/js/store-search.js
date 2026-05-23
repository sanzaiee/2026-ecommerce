(function () {
  const forms = document.querySelectorAll('[data-store-search]');
  if (!forms.length) return;

  const inputs = Array.from(document.querySelectorAll('[data-store-search-input]'));

  function syncInputs(source) {
    const value = source.value;
    inputs.forEach((input) => {
      if (input !== source) input.value = value;
    });
  }

  inputs.forEach((input) => {
    input.addEventListener('input', () => syncInputs(input));
  });

  forms.forEach((form) => {
    form.addEventListener('submit', (event) => {
      const input = form.querySelector('[data-store-search-input]');
      if (!input) return;

      const query = input.value.trim();
      if (query === '') {
        event.preventDefault();
        window.location.href = form.action;
        return;
      }

      input.value = query;
      syncInputs(input);
    });
  });
})();
