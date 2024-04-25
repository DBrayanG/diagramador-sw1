import express from 'express';
import { Server } from 'socket.io';
import http from 'http';

const app = express();
const server = http.createServer(app);
const io = new Server(server);

io.on("connection",socket => {
    console.log("conectado")

    socket.on("envio",(mensaje)=>{
        //console.log(mensaje)
        io.sockets.emit("recivido",mensaje);
    })

    socket.on("linea",(linea)=>{
        io.sockets.emit("nuevaLinea",linea);
    });

    socket.on("link:final",(newact)=>{
        io.sockets.emit("link:final",newact);
    })

    socket.on("link",(link)=>{
        io.sockets.emit("nuevoLink",link);
    })

    socket.on("eliminar",(selectedNode)=>{
        io.sockets.emit("eliminarObjeto",selectedNode);
    })

    socket.on('movimientoPartes', data => {
        console.log('Evento movimientoPartes recibido:', data);
        io.emit('movimientoPartes', data); // Esta línea retransmite los datos a todos los clientes conectados
    });

    socket.on("disconnet",(socket)=>{
        console.log("desconectado");
    })
})


server.listen(3000, (err) => {
    if (err) throw new Error(err);
    console.log('listening on *:3000');
});