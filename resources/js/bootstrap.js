import 'bootstrap';

import axios from 'axios';
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const appKey   = import.meta.env.VITE_REVERB_APP_KEY;
const wsHost   = import.meta.env.VITE_REVERB_HOST || window.location.hostname;
const wsPort   = import.meta.env.VITE_REVERB_PORT ?? 8080;
const scheme   = import.meta.env.VITE_REVERB_SCHEME ?? 'http';

console.log('Echo config →', { appKey, wsHost, wsPort, scheme });

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: appKey,
    wsHost: wsHost,
    wsPort: wsPort,
    wssPort: wsPort,
    forceTLS: scheme === 'https',
    enabledTransports: ['ws'], // con http puro, sólo ws
    disableStats: true,
});

// Debug conexión
window.Echo.connector.pusher.connection.bind('connected', () => {
    console.log('Conectado a Reverb WebSocket');
});

window.Echo.connector.pusher.connection.bind('error', (err) => {
    console.error('Error de conexión Reverb:', err);
});