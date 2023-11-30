// 'use strict';
// require('dotenv').config();
// var app = require('express')();
// var server = require('http').Server(app);
// var io = require('socket.io')(server, { cors: { origin: '*', methods: ['GET', 'POST', 'OPTIONS'] } });
// // var redis = require('redis');
// var Redis = require('ioredis');

// var $redis = new Redis();

// $redis.subscribe('message-channel');

// $redis.on('message', function (channel, message) {
//     message = JSON.parse(message);
//     console.log(channel, message.event, message);
//     io.emit(`${channel}:${message.event}`, message.data);
// });

// server.listen(3000, function () {
//     console.log('Socket server is running.  3000');
// });




// server.listen(6002, '0.0.0.0', function () {
//     console.log('Socket server is running.  6002');
// });

// io.on('connection', function (socket) {

//     console.log("client connected");
//     var redisClient = redis.createClient();
//     redisClient.subscribe('message');

//     redisClient.on("message", function (channel, data) {
//         socket.emit(channel, data);
//     });

//     socket.on('disconnect', function () {
//         redisClient.quit();
//     });

// });