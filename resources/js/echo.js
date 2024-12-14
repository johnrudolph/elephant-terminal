import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
Pusher.logToConsole = true;

// Debug the environment variables
console.log('Environment Variables:', {
    key: import.meta.env.VITE_REVERB_APP_KEY,
    host: import.meta.env.VITE_REVERB_HOST,
    port: import.meta.env.VITE_REVERB_PORT,
    scheme: import.meta.env.VITE_REVERB_SCHEME
});

const config = {
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: parseInt(import.meta.env.VITE_REVERB_PORT),  // Ensure port is a number
    wssPort: parseInt(import.meta.env.VITE_REVERB_PORT), // Ensure port is a number
    forceTLS: import.meta.env.VITE_REVERB_SCHEME === 'https',
    encrypted: import.meta.env.VITE_REVERB_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    cluster: null,
    authEndpoint: '/broadcasting/auth'
};

// Debug the final config
console.log('Final Echo Config:', config);

window.Echo = new Echo(config);

// Debug the Echo instance
console.log('Echo Instance:', window.Echo);
