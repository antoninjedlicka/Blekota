// js/onboarding.js


document.addEventListener('DOMContentLoaded', () => {
  const container = document.querySelector('.onboarding-container');
  const messages = document.querySelectorAll('.message, .messageB');
  const button = document.querySelector('.btn');

  // Přidáme třídu show, která aktivuje přechody
  setTimeout(() => {
    container.classList.add('show');
  }, 200);

  // Postupné zobrazování textů
  messages.forEach((message, index) => {
    setTimeout(() => {
      message.style.opacity = 1;
    }, 1500 + index * 1500);
  });

  // Nakonec tlačítko
  setTimeout(() => {
    button.style.opacity = 1;
    button.style.transform = 'scale(1)';
  }, 6000);
});


document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('go-admin');
  if (btn) {
    btn.addEventListener('click', () => {
      console.log('[Onboarding] Přesměrování na administraci...');
      window.location.href = 'index.php';
    });
  }
});
