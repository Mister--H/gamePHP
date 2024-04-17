    <div id="map" style="width: 100%; height: 100vh"></div>
    <style>
    #chatButtons {
        position: absolute;
        bottom: -70px;
        left: 50%;
        transform: translateX(-60%);
        background-color: #fff;
        border-radius: 10px;
        padding: 8px 50px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        justify-content: center;
    }
    #chatButtons button, #chatButtons a {
        width: 60px;
        height: 60px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    #chatContainer {
        position: absolute;
        bottom: -70px;
        left: 58%;
        right: 0;
        background-color: #fff;
        border-radius: 10px;
      
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        display: none;
        width:30%;
    }
    </style>

    <div id="chatButtons" class="d-flex gap-2">
        <button id="chatButton" onclick="showChat()" class="btn btn-white border rounded-circle">
            <i class="bi bi-chat" style="font-size: 24px; color: black;"></i>
        </button>
        </button>
        <button id="mineCoinButton" class="btn btn-white border rounded-circle" style="background-color: gold;">
            <i class="bi bi-coin" style="font-size: 24px; color: black;"></i>
        </button>
        <a id="profileButton" class="btn btn-white border rounded-circle" href="https://game.metans.de/start/settings" target="_blank">
            <i class="bi bi-person" style="font-size: 24px; color: black;"></i>
        </a>
        </button>
    </div>

    <div id="chatContainer">
        <div id="chatHeader" class="d-flex justify-content-between align-items-center p-2 rounded" style="background-color: #f8f9fa;">
            <h5 class="m-0">Chat</h5>
            <button id="closeChat" onclick="closeChat()" class="btn btn-sm btn-danger">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <div id="chatMessages" class="p-2" style="height: 200px; overflow-y: auto;"></div>
        <div id="chatInput" class="d-flex p-2">
            <form action="" id="form">
                <input type="text" id="message" class="form-control" placeholder="Type a message...">
                <button type="submit" id="sendMessage" class="btn btn-primary">Send</button>
            </form>
        </div>
    </div>

    <div id="arrowKeys" class="d-lg-none d-sm-flex justify-content-center" style="position: absolute;bottom: -80px;left: 50px;width: 150px;background: #ffffff30;border-radius: 50%;border: 1px solid #fff;">
    <button id="upButton" class="btn btn-white border rounded-circle">
        <i class="bi bi-arrow-up" style="font-size: 24px; color: black;"></i>
    </button>
    <div style="height: 50px;width: 150px;display: flex;justify-content: space-around;">
        <button id="leftButton" class="btn btn-white border rounded-circle">
            <i class="bi bi-arrow-left" style="font-size: 24px; color: black;"></i>
        </button>
        <button id="rightButton" class="btn btn-white border rounded-circle">
            <i class="bi bi-arrow-right" style="font-size: 24px; color: black;"></i>
        </button>
    </div>
    <button id="downButton" class="btn btn-white border rounded-circle">
        <i class="bi bi-arrow-down" style="font-size: 24px; color: black;"></i>
    </button>
</div>

<style>
    @media only screen and (max-width: 991px) {
        #arrowKeys {
            display: flex;
            flex-direction: column;
            align-items: center;
            bottom: 30px !important;
            width: 90px !important;
            left: 70px !important;
        }
        #arrowKeys i {
            font-size:16px !important
        }
        #arrowKeys div {
            height: 35px !important;
            width: 90px !important;
        }
       
    }
</style>


    <script>
        function showChat(){
            document.getElementById('chatContainer').style.display = 'block';
        }
        function closeChat(){
            document.getElementById('chatContainer').style.display = 'none';
        }
        const form = document.getElementById('form');
        const input = document.getElementById('message');
        const messages = document.getElementById('chatMessages');

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            if (input.value) {
            socket.emit('chat message', input.value);
            input.value = '';
            }
        });
        socket.on('chat message', (msg) => {
            const item = document.createElement('li');
            item.textContent = msg['userId'] + ': ' + msg['message'];
            messages.appendChild(item);
            form.scrollTo(0, document.body.scrollHeight);
        });
    </script>