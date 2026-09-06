(() => {
  const desktopForm = document.getElementById('blogFiltersDesktop');
  const mobileForm = document.getElementById('blogFiltersMobile');

  const bindAutoSubmit = (form) => {
    if (!form) {
      return;
    }

    form.addEventListener('change', (event) => {
      const target = event.target;

      if (!(target instanceof HTMLInputElement)) {
        return;
      }

      if (target.type === 'radio' || target.type === 'checkbox') {
        form.requestSubmit();
      }
    });

    const searchInput = form.querySelector('input[type="search"]');

    if (searchInput instanceof HTMLInputElement) {
      let debounceTimer;

      searchInput.addEventListener('input', () => {
        window.clearTimeout(debounceTimer);
        debounceTimer = window.setTimeout(() => {
          form.requestSubmit();
        }, 400);
      });
    }
  };

  bindAutoSubmit(desktopForm);
  bindAutoSubmit(mobileForm);
})();
