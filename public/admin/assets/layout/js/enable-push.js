'use strict';

const swReady = navigator.serviceWorker.ready;

document.addEventListener('DOMContentLoaded', function () {
    initSW();
});

function initSW() {
    if (!"serviceWorker" in navigator) {
        //service worker isn't supported
        return;
    }

    //don't use it here if you use service worker
    //for other stuff.
    if (!"PushManager" in window) {
        //push isn't supported
        return;
    }

    //register the service worker
    navigator.serviceWorker.register('../service-worker.js')
        .then(() => {
            initPush();
        })
        .catch((err) => {
            console.log(err)
        });
}

function initPush() {
    if (!swReady) {
        return;
    }
    new Promise(function (resolve, reject) {
        const permissionResult = Notification.requestPermission(function (result) {
            resolve(result);
        });

        if (permissionResult) {
            permissionResult.then(resolve, reject);
        }
    })
    .then((permissionResult) => {
        if (permissionResult !== 'granted') {
            throw new Error('We weren\'t granted permission.');
        }
        subscribeUser();
    });
}

/**
 * Subscribe the user to push
 */
function subscribeUser() {

    swReady.then((registration) => {
            const subscribeOptions = {
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(
                    window.VAPID_PUBLIC_KEY
                )
            };

            return registration.pushManager.subscribe(subscribeOptions);
        })
        .then((pushSubscription) => {
            //console.log('Received PushSubscription: ', JSON.stringify(pushSubscription));
            storePushSubscription(pushSubscription);
        })
        .catch((err) => {
            console.log(err)
        });
}

/**
 * send PushSubscription to server with AJAX.
 * @param {object} pushSubscription
 */
function storePushSubscription(pushSubscription) {
    
    const SERVER_URL = getBaseUrl() + "/admin/push-subscription";

    fetch(SERVER_URL, {
        method: 'POST',
        body: JSON.stringify(pushSubscription),
        headers: {
            "Content-Type": "application/json",
            "Authorization": "Bearer "+ getToken("admin")
        }
    })
    .then((res) => {
        return res.json();
    })
    .then((res) => {
        //console.log(res)
    })
    .catch((err) => {
        console.log(err)
    });
}

/**
 * urlBase64ToUint8Array
 * 
 * @param {string} base64String a public vapid key
 */
function urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding)
    .replace(/\-/g, "+")
    .replace(/_/g, "/");
    const rawData = atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) {
        outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
}