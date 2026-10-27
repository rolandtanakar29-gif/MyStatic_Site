document.addEventListener('DOMContentLoaded', () => {
  const toggle = document.getElementById('themeToggle');
  const root = document.documentElement;
  if (toggle) toggle.addEventListener('click', () => {
    const next = root.dataset.theme === 'dark' ? 'light' : 'dark';
    root.dataset.theme = next;
    localStorage.setItem('theme', next);
  });
  document.querySelectorAll('input[type=file]').forEach(input => {
    input.addEventListener('change', () => {
      const label = input.closest('label') || input.parentElement;
      if (input.files[0] && label) label.dataset.file = input.files[0].name;
    });
  });
  const toast=document.querySelector('.toast');
  if(toast) setTimeout(()=>toast.remove(),4200);
});
