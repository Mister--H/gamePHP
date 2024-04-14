const express = require('express');
const { createServer } = require('node:http');
const { Server } = require('socket.io');
const axios = require('axios');
const app = express();
const server = createServer(app);
const io = new Server(server);


io.on('connection', (socket) => {
  const userId = socket.handshake.query.userId;
  console.log('User Connected:', userId);

  socket.on('sPosition', async (position) => {
    const posData = JSON.parse(position);
    updatePositionViaPHP(userId, posData);
    socket.broadcast.emit('updatePosition', { id: userId, position: posData });
    // Fetch nearby players' positions and emit to this socket
    fetchPlayersNearby(posData, userId);
    
  });

  socket.on('disconnect', () => {
    console.log('User disconnected:', userId);
  });
});

async function fetchPlayersNearby(position, userId) {
  try {

    const response = await axios.post('https://game.metans.de/api/getNearbyPlayersPosition', {
        userId: userId,
        lat: position.lat,
        lng: position.lng,
    },{
      headers: {
        'Content-Type': 'application/json'
      }
  });

    if (response.data) {
      console.log('Nearby players:', response.data.data);
      io.emit('nearbyPlayers', response.data.data);
    } else {
      console.log('No data received from getNearbyPlayers API');
    }
  } catch (error) {
    console.error('Error fetching nearby players:', error);
    if (error.response) {
      // Handle response error
      console.log('Response error:', error.response.data);
    }
  }
}


async function updatePositionViaPHP(userId, position) {
  try {
    const response = await axios.post('https://game.metans.de/api/setPosition', {
      user_id: userId, 
      lng: position.lng,
      lat: position.lat
    });

    if (response.data) {
      console.log('Position updated response:', response.data);
    } else {
      console.log('No data received from setPosition API');
    }
  } catch (error) {
    console.error('Error updating position:', error);
    if (error.response) {
      // Handle response error
      console.log('Response error:', error.response.data);
    }
  }
}


server.listen(3000, () => {
  console.log('server running at http://localhost:3000');
});