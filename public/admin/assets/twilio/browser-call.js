// import $ from "jquery";
// const { Device } = require('twilio-client');
/**
 * Twilio Client configuration for the browser-calls-laravel
 * example application.
 */

 const Device = Twilio.Device;

// Store some selectors for elements we'll reuse
let callStatus = $("#call-status");
let answerButton = $("#answer-button");
let callButton = $(".call-button");
let hangUpButton = $(".hangup-button");
let callCustomerButtons = $(".call-customer-button");
let To = '';
let outgoing_caller_id ='';

var device = null;
answerButton.css("cssText", "display: none !important");


/* Helper function to update the call status bar */
function updateCallStatus(status) {
    callStatus.attr('placeholder', status);
}

function setupHandlers(device) {
    device.on('ready', function (_device) {
        updateCallStatus("Ready to call");
    });

    /* Report any errors to the call status display */
    device.on('error', function (error) {
        updateCallStatus("ERROR: " + error.message);
    });

    /* Callback for when Twilio Client initiates a new connection */
    device.on('connect', function (connection) {
        // Enable the hang up button and disable the call buttons
        callButton.css("cssText", "display: none !important");
        
        updateCallStatus("Call Started");
    });

    /* Callback for when a call ends */
    device.on('disconnect', function(connection) {
        // Disable the hangup button and enable the call buttons
        callButton.css("cssText", "display: inline-block !important");
        answerButton.css("cssText", "display: none !important");
        $('#callModal').modal('hide');
        updateCallStatus("Ready to call");
    });

    /* Callback for when Twilio Client receives a new incoming call */
    device.on('incoming', function(connection) {
        updateCallStatus("Incoming call");

        answerButton.css("cssText", "display: inline-block !important");
        callButton.css("cssText", "display: none !important");
        $('#callModal').modal('show');
        // Set a callback to be executed when the connection is accepted
        connection.accept(function() {
            updateCallStatus("Call started");
        });

        // Set a callback on the answer button and enable it
        answerButton.click(function() {
            connection.accept();
        });
    });
};

window.setupTwilioClient=function(token, ref_to, ref_outgoing_caller_id){
    outgoing_caller_id = ref_outgoing_caller_id;
    To = ref_to;

    device = new Device();
    // device.setup(token);
    device.setup(token, { debug: true });

    setupHandlers(device);
};

window.callModal = function(){
    answerButton.css("cssText", "display: none !important");
    $('#callModal').modal('show');
}

window.callProvider = function() {
    updateCallStatus("Calling provider...");
    callButton.css("cssText", "display: none !important");
    
    var params = {
      To:To,
      outgoing_caller_id:outgoing_caller_id
    };
    device.connect(params);
};

window.callUser = function() {
    updateCallStatus("Calling user...");
    callButton.css("cssText", "display: none !important");

    var params = {
      To:To,
      outgoing_caller_id:outgoing_caller_id
    };
    device.connect(params);
};

/* End a call */
window.hangUp = function() {
    device.disconnectAll();
    callButton.css("cssText", "display: inline-block !important");
    answerButton.css("cssText", "display: none !important");
};
