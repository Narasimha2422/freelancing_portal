<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <script src="https://cdn.socket.io/4.0.0/socket.io.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            background-color: #f9f9f9;
        }
        #chatContainer {
            width: 400px;
            margin-top: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        #messages {
            height: 300px;
            overflow-y: auto;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        #typingIndicator {
            padding: 5px;
            font-style: italic;
            color: gray;
        }
        #messageInputContainer {
            display: flex;
            padding: 10px;
        }
        #messageInput {
            flex: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        #sendMessageButton {
            margin-left: 10px;
            padding: 8px 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        #sendMessageButton:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div id="chatContainer">
        <div id="messages"></div>
        <div id="typingIndicator"></div>
        <div id="messageInputContainer">
            <input type="text" id="messageInput" placeholder="Type your message here">
            <button id="sendMessageButton">Send</button>
        </div>
    </div>

    <script>
        // Connect to the WebSocket server
        const socket = io('http://localhost:3000'); // Replace with your server URL

        const messagesDiv = document.getElementById('messages');
        const messageInput = document.getElementById('messageInput');
        const sendMessageButton = document.getElementById('sendMessageButton');
        const typingIndicator = document.getElementById('typingIndicator');

        // Handle sending messages
        sendMessageButton.addEventListener('click', () => {
            const message = messageInput.value.trim();
            if (message !== '') {
                const senderId = 1; // Replace with logged-in user's ID
                const receiverId = 2; // Replace with the recipient's ID

                // Send message to the server
                socket.emit('sendMessage', { sender_id: senderId, receiver_id: receiverId, message });

                // Add message to the chat box for the sender
                const messageDiv = document.createElement('div');
                messageDiv.textContent = `You: ${message}`;
                messagesDiv.appendChild(messageDiv);

                // Clear input field
                messageInput.value = '';
                messagesDiv.scrollTop = messagesDiv.scrollHeight; // Scroll to the bottom
            }
        });

        // Receive messages from the server
        socket.on('receiveMessage', (data) => {
            const messageDiv = document.createElement('div');
            messageDiv.textContent = `User ${data.sender_id}: ${data.message}`;
            messagesDiv.appendChild(messageDiv);

            // Scroll to the bottom
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });

        // Show typing indicator
        messageInput.addEventListener('input', () => {
            const receiverId = 2; // Replace with the recipient's ID
            socket.emit('typing', { receiver_id: receiverId });
        });

        socket.on('typing', (data) => {
            typingIndicator.textContent = `User ${data.sender_id} is typing...`;
            setTimeout(() => {
                typingIndicator.textContent = '';
            }, 2000); // Clear typing indicator after 2 seconds
        });
    </script>
</body>
</html>
