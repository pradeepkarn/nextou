<?php

class Chat_front
{
    function init($link = 'ws://' . MY_DOMAIN . ':' . WS_PORT)
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
                overflow-y: scroll;
                max-height: 300px;
            }

            #user-list li,
            #chat li {
                padding: 10px;
                border-bottom: 1px solid #eee;
                border: 1px solid #ccc;
                border-radius: 5px;
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
        </div>
        <div id="msg-field">
            <div class="history-box">

            </div>
            <input type="text" id="message" placeholder="Type your message">
            <button onclick="sendMessage()">Send</button>
        </div>
        <script>
            function openChatPopup() {
                const message = document.getElementById('msg-field');
                message.style.display = "block";
                message.style.display = "none";
            }

            function toggleChat() {
                const chatContainer = document.getElementById('chat-container');
                const expandButton = document.getElementById('expand-button');


                if (chatContainer.style.height === '0px' || chatContainer.style.height === '') {
                    chatContainer.style.height = '300px';
                    expandButton.textContent = '-';

                } else {
                    chatContainer.style.height = '0px';
                    expandButton.textContent = '+';

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
