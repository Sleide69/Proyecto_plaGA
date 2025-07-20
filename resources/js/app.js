import './bootstrap';

console.log('Antes de inicializar Echo');

import Echo from 'laravel-echo';

window.Echo = new Echo({
    broadcaster: 'reverb',
    host: 'ws://localhost:8080'
});

console.log('Después de inicializar Echo');

window.Echo.channel('detecciones')
    .listen('.NuevaDeteccion', (e) => {
        console.log('Nueva detección recibida:', e);
    });