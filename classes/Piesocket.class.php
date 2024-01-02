<?php

class Piesocket
{
    function server_init()
    {
        $curl = curl_init();

        $post_fields = [
            "key" => "uVdP9q7l86MNlZ79Nk8rzKzKgxDEiS8M5lWFhwQX", //Demo key,  get yours at https://piesocket.com
            "secret" => "AS4EqeDmuvYXpnwpoBpnmKLo91RRymOB", //Demo secret, get yours at https://piesocket.com
            "roomId" => "1",
            "message" => ["text" => "Hello world!"]
        ];
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://free.blr2.piesocket.com/api/publish",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($post_fields),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        print_r($response);
    }
    function front_init()
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
}
