const gate = document.getElementById('ageGate');
if (gate && localStorage.getItem('idadeConfirmada') !== 'sim') {
  gate.classList.add('show');
  gate.setAttribute('aria-hidden', 'false');
}
document.getElementById('ageYes')?.addEventListener('click', () => {
  localStorage.setItem('idadeConfirmada', 'sim');
  gate.classList.remove('show');
});
document.getElementById('ageNo')?.addEventListener('click', () => {
  alert('Este conteúdo é destinado somente a maiores de 18 anos.');
});
document.querySelector('.menu-toggle')?.addEventListener('click', () => {
  document.querySelector('.nav-links')?.classList.toggle('open');
});
