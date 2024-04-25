import { io } from 'socket.io-client';

export const socket = io('http://localhost:3000', {
    transports: ['websocket'],
});

socket.on('connect', function () {
    console.log('conectado con el servidor');
    
});

