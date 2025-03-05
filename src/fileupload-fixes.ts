document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.mod-form-field').forEach((container) => {
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        mutation.addedNodes.forEach((node) => {
          if (node instanceof HTMLInputElement && node.type === 'file') {
            const hiddenInput = node;
            /*
            Helsingborg’s styleguide’s script hides the file input by setting
            style to display:none. This doesn’t work well with Safari since it
            cannot focus hidden elements. The c-fileinput__input class already
            visually hides it so we don’t need to do anything else than remove
            the style attribute.
            */
            hiddenInput.removeAttribute('style');
          }
        });
      });
    });
    observer.observe(container, { childList: true, subtree: true });
  });
});
