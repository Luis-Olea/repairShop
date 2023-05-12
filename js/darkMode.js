const darkModeCheckbox = document.querySelector('#dark-mode');
const body = document.querySelector('body');

// Recuperar el estado del checkbox de localStorage
const darkModeChecked = JSON.parse(localStorage.getItem('dark-mode'));
if (darkModeChecked) {
    body.classList.add('dark-mode');
    darkModeCheckbox.checked = true;
}

darkModeCheckbox.addEventListener('change', () => {
    if (darkModeCheckbox.checked) {
        body.classList.add('dark-mode');
        localStorage.setItem('dark-mode', true);
    } else {
        body.classList.remove('dark-mode');
        localStorage.setItem('dark-mode', false);
    }
});