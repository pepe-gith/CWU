const fs = require('fs');
const path = require('path');

function copyFile(src, dest) {
    fs.mkdirSync(path.dirname(dest), { recursive: true });
    fs.copyFileSync(src, dest);
    console.log('  copiado:', dest);
}

function copyDir(src, dest) {
    fs.mkdirSync(dest, { recursive: true });
    for (const file of fs.readdirSync(src)) {
        const s = path.join(src, file);
        const d = path.join(dest, file);
        fs.statSync(s).isDirectory() ? copyDir(s, d) : copyFile(s, d);
    }
}

const nm = 'node_modules';

console.log('\nCopiando librerias a libs/...');

// Bootstrap
copyFile(`${nm}/bootstrap/dist/css/bootstrap.min.css`,      'libs/bootstrap/css/bootstrap.min.css');
copyFile(`${nm}/bootstrap/dist/js/bootstrap.bundle.min.js`, 'libs/bootstrap/js/bootstrap.bundle.min.js');

// Bootstrap Icons
copyFile(`${nm}/bootstrap-icons/font/bootstrap-icons.min.css`, 'libs/bootstrap-icons/font/bootstrap-icons.min.css');
copyDir(`${nm}/bootstrap-icons/font/fonts`,                    'libs/bootstrap-icons/font/fonts');

// Flatpickr
copyFile(`${nm}/flatpickr/dist/flatpickr.min.css`, 'libs/flatpickr/dist/flatpickr.min.css');
copyFile(`${nm}/flatpickr/dist/flatpickr.min.js`,  'libs/flatpickr/dist/flatpickr.min.js');
copyFile(`${nm}/flatpickr/dist/l10n/es.js`,        'libs/flatpickr/dist/l10n/es.js');

// FullCalendar
copyFile(`${nm}/fullcalendar/index.global.min.js`, 'libs/fullcalendar-6.1.20/dist/index.global.min.js');

console.log('\nLibrerias instaladas correctamente en libs/\n');
