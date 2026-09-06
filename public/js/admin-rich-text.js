(function () {
    function initRichText() {
        if (typeof tinymce === 'undefined') {
            return;
        }

        tinymce.init({
            selector: 'textarea.rich-text',
            license_key: 'gpl',
            height: 420,
            menubar: false,
            plugins: 'lists link autolink code table',
            toolbar:
                'undo redo | blocks | bold italic underline | bullist numlist | link table | removeformat code',
            branding: false,
            promotion: false,
            content_style: 'body { font-family: system-ui, sans-serif; font-size: 14px; }',
            block_formats: 'Paragraph=p; Heading 2=h2; Heading 3=h3; Heading 4=h4',
            relative_urls: false,
            remove_script_host: true,
        });

        document.querySelectorAll('form[data-rich-text]').forEach(function (form) {
            // Sync before native constraint validation (required fields on hidden textareas).
            form.addEventListener('click', function (event) {
                var submitter = event.target.closest('button[type="submit"], input[type="submit"], button:not([type])');

                if (! submitter || ! form.contains(submitter)) {
                    return;
                }

                tinymce.triggerSave();
            });

            form.addEventListener('submit', function () {
                tinymce.triggerSave();
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initRichText);
    } else {
        initRichText();
    }
})();
