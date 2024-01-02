<?php

class Chat_front
{
    function init()
    { ?>
     <div id="output"></div>
        <script>
            const socket = new WebSocket('wss://free.blr2.piesocket.com/v3/1?api_key=i7RXtDu0SNnm9uv6oNSId9vU0IdsVyr4V7rxQekC&notify_self=1');

            // Connection opened
            socket.addEventListener('open', (event) => {
                console.log('WebSocket connection opened:', event);

                // Send a message to the server
                socket.send('Hello, Server!');
            });

            // Listen for messages from the server
            socket.addEventListener('message', (event) => {
                console.log('Message from server:', event.data);

                // Display the received message on the page
                document.getElementById('output').innerHTML = `Message from server: ${event.data}`;
            });

            // Connection closed
            socket.addEventListener('close', (event) => {
                console.log('WebSocket connection closed:', event);
            });

            // Handle errors
            socket.addEventListener('error', (event) => {
                console.error('WebSocket error:', event);
            });
        </script>
    <?php }
    function init3()
    { ?>
        <style>
            div {
                margin: 10px;
            }

            #chatFormContainer {
                text-align: center;
                position: fixed;
                bottom: 5px;
                left: 5px;
                right: 5px;
            }

            #chatFormContainer input {
                display: inline-block;
                width: 90%;
                padding: 20px;
            }
        </style>
        <div id="chatLog">

        </div>
        <div id="chatFormContainer">
            <form id="chatForm">
                <input id="chatMessage" placeholder="Type  message and press enter..." type="text">
            </form>
        </div>
        <script>
            var username = "user_" + (Math.floor(Math.random() * 1000));
            var chatLog = document.getElementById('chatLog');
            var chatForm = document.getElementById('chatForm');
            chatForm.addEventListener("submit", sendMessage);

            var piesocket = new PieSocket({
                clusterId: 'free.blr2',
                apiKey: 'uVdP9q7l86MNlZ79Nk8rzKzKgxDEiS8M5lWFhwQX',
                notifySelf: true,
                presence: true,
                userId: username
            });

            var channel;
            piesocket.subscribe("chat-room").then(ch => {
                channel = ch;

                channel.listen("system:member_joined", function(data) {
                    if (data.member.user == username) {
                        data.member.user = "<b>You</b>";
                    }

                    chatLog.insertAdjacentHTML('afterend', `<div> ${data.member.user} joined the chat<div>`);
                })

                channel.listen("new_message", function(data, meta) {
                    if (data.sender == username) {
                        data.sender = "<b>You</b>";
                    }

                    chatLog.insertAdjacentHTML('afterend', `<div> ${data.sender}: ${data.text} <div>`);
                })

            });

            function sendMessage(e) {
                e.preventDefault();

                if (!channel) {
                    alert("Channel is not connected yet, wait a sec");
                    return;
                }

                var message = document.getElementById('chatMessage').value;

                channel.publish("new_message", {
                    sender: username,
                    text: message
                });
            }
        </script>
    <?php
    }
    function init2($link = "wss://free.blr2.piesocket.com/v3/1?api_key=i7RXtDu0SNnm9uv6oNSId9vU0IdsVyr4V7rxQekC&notify_self=1")
    { ?>
        <style>
            #chat-container {
                position: fixed;
                bottom: 100px;
                right: 20px;
                display: flex;
                flex-direction: column;
                align-items: flex-end;
                transition: height 0.3s ease-in-out;
                height: 300px;
                /* Set an initial height */
            }

            #user-list {
                width: 300px;
            }

            #user-list,
            #chat {
                list-style-type: none;
                padding: 0;
                margin: 0;
                border: 1px solid #ccc;
                border-radius: 5px;
                overflow-y: auto;
                max-height: 300px;
            }

            #user-list li,
            #chat li {
                padding: 10px;
                border-bottom: 1px solid #eee;
            }

            #msg-field {
                display: flex;
                flex-direction: row;
                justify-items: center;
                justify-content: space-between !important;
                align-items: center;
                width: 100%;
            }

            #message {
                width: 100%;
                padding: 8px;
                box-sizing: border-box;
            }

            #msg-field button {
                width: 100%;
                padding: 8px;
                cursor: pointer;
                background-color: #3498db;
                color: #fff;
                border: none;
                border-radius: 5px;
            }


            #toggle-button {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: #3498db;
                color: #fff;
                border: none;
                padding: 10px;
                cursor: pointer;
                border-radius: 5px;
            }
        </style>


        <div id="chat-container">
            <button onclick="toggleChat()" id="expand-button">-</button>
            <ul id="user-list"></ul>
            <ul id="chat"></ul>
            <div id="msg-field">
                <input type="text" id="message" placeholder="Type your message">
                <button onclick="sendMessage()">Send</button>
            </div>


        </div>
        <script>
            function toggleChat() {
                const chatContainer = document.getElementById('chat-container');
                const expandButton = document.getElementById('expand-button');
                const message = document.getElementById('msg-field');

                if (chatContainer.style.height === '0px' || chatContainer.style.height === '') {
                    chatContainer.style.height = '300px';
                    expandButton.textContent = '-';
                    message.style.display = "block";
                } else {
                    chatContainer.style.height = '0px';
                    expandButton.textContent = '+';
                    message.style.display = "none";
                }
            }
            toggleChat();
            // Your WebSocket and other JavaScript code goes here
        </script>


        <script>
            const conn = new WebSocket(`<?= $link; ?>`);

            conn.onopen = function(event) {
                // console.log('WebSocket connection opened:', event);
            };

            conn.onmessage = function(event) {
                const data = JSON.parse(event.data);
                console.log(event);
                if (data.clients) {
                    updateClientList(data.clients);
                } else {
                    console.log(event.data);
                    const chat = document.getElementById('chat');
                    const listItem = document.createElement('li');
                    listItem.textContent = `User ${data.from}: ${data.message}`;
                    chat.appendChild(listItem);

                    // Automatically scroll to the bottom of the chat area
                    chat.scrollTop = chat.scrollHeight;
                }
            };

            function updateClientList(clients) {
                const userList = document.getElementById('user-list');
                userList.innerHTML = '';

                clients.forEach(client => {
                    const listItem = document.createElement('li');
                    listItem.textContent = `User ${client.id}`;
                    userList.appendChild(listItem);
                });
            }

            function sendMessage() {
                const messageInput = document.getElementById('message');
                const toUser = prompt('Enter user ID to send the message to:');
                const message = {
                    to: toUser,
                    message: messageInput.value,
                };
                conn.send(JSON.stringify(message));
                messageInput.value = '';
            }
        </script>
<?php }
}
