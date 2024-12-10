const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const mysql = require('mysql');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

const db = mysql.createConnection({
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'freelancing_portal'
});

io.on('connection', (socket) => {
    console.log('A user connected');

    // Listen for a new message
    socket.on('sendMessage', (data) => {
        const { sender_id, receiver_id, message } = data;

        // Save message to MySQL
        const query = 'INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)';
        db.query(query, [sender_id, receiver_id, message], (err, result) => {
            if (err) throw err;

            // Emit message to the receiver
            io.to(receiver_id).emit('receiveMessage', { sender_id, message });
        });
    });

    // Disconnect event
    socket.on('disconnect', () => {
        console.log('A user disconnected');
    });
});

server.listen(3000, () => {
    console.log('Server is running on port 3000');
});
