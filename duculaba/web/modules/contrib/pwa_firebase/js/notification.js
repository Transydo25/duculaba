(function ($,Drupal, drupalSettings, once) {
  Drupal.behaviors.pwa_firebase = {
    attach: function (context, settings) {
      const permissionDivId = 'pwa_firebase_notifications';
      const tokenDivId = 'pwa_firebase_remove_token';
      var  messaging = {};
      once('pwa_firebase', 'html', context).forEach( function (element) {
        int(element);
      });

      function int(element){
        if (typeof firebaseApp === "undefined") {
          const firebaseApp = firebase.initializeApp(settings.pwa_firebase.firebaseConfig);
          messaging = firebase.messaging();
          if(!isTokenSentToServer()){
            let isShowPopup = sessionStorage.getItem('pwa_firebase_popup');
            if(!isShowPopup){
              showPopup();
              sessionStorage.setItem('pwa_firebase_popup', true);
            }
          }
          messaging.onMessage(function (payload) {
            const title = payload.notification.title;
            const options = {
              body: payload.notification.body,
              icon: payload.notification.icon,
            };
            new Notification(title, options);
          });
        }
        // Enable notification from block
        $(".notification_manager #pwa_firebase_notifications").on("click", function () {
          requestPermission();
        });
        // Remove token from block.
        $(".notification_manager #pwa_firebase_remove_token").on("click", function () {
          deleteToken();
        });
      }

      function showPopup() {
        let dialog = '<div class="text-center dialog">' + Drupal.t("This application will send notification to your device") +
          '<div class=" ml-2 d-flex align-items-center justify-content-center g-2">' +
          '<button class="allow-button btn btn-success mr-1" id="' + permissionDivId + '">' + Drupal.t('Allow') + '</button> ' +
          '</div></div>';
        let notify = $.notify(dialog, {allow_dismiss: true});

        $(".dialog #" + permissionDivId).on("click", function () {
          requestPermission();
          notify.close();
        });
      }

      function resetUI() {
        //clearMessages();
        // Get registration token. Initially this makes a network call, once retrieved
        // subsequent calls to getToken will return from cache.
        messaging.getToken({vapidKey: settings.pwa_firebase.VapidKey}).then((currentToken) => {
          if (currentToken) {
            sendTokenToServer(currentToken);
          } else {
            // Show permission request.
            //console.log('No registration token available. Request permission to generate one.');
            setTokenSentToServer(false);
          }
        }).catch((err) => {
          //console.log('An error occurred while retrieving token. ', err);
          setTokenSentToServer(false);
        });
      }

      // Send the registration token your application server, so that it can:
      // - send messages back to this app
      // - subscribe/unsubscribe the token from topics
      function sendTokenToServer(currentToken) {
        if (!isTokenSentToServer()) {
          //console.log('Sending token to server...');
          $.post(settings.pwa_firebase.sendToken, {token: currentToken}, function (data) {
            if(!data){
              //console.info('Token already sent to server');
            }
          });
          sessionStorage.setItem('pwa_firebase_popup', false);
          setTokenSentToServer(true);
        } else {
          //console.log('Token already sent to server so won\'t send it again unless it changes');
        }
      }

      function isTokenSentToServer() {
        if (window.localStorage.getItem('sentToServer') == 1) {
          return true;
        }
        return false;
      }

      function setTokenSentToServer(sent) {
        if (sent) {
          window.localStorage.setItem('sentToServer', 1);
        } else {
          window.localStorage.setItem('sentToServer', 0);
        }
      }

      function requestPermission() {
        //console.log('Requesting permission...');
        Notification.requestPermission().then((permission) => {
          if (permission === 'granted') {
            //If notification is allowed
            navigator.serviceWorker.ready.then(p => {
              p.pushManager.getSubscription().then(subscription => {
                if (subscription === null) {
                  //If there is no notification subscription, register.
                  let re = p.pushManager.subscribe({userVisibleOnly: true})
                }
              })
            });
            resetUI();
          } else {
            //console.log('Unable to get permission to notify.');
          }
        });
      }

      function deleteToken() {
        // Delete registration token.
        messaging.getToken().then((currentToken) => {
          messaging.deleteToken(currentToken).then(() => {
            setTokenSentToServer(false);
            $.post(settings.pwa_firebase.sendToken, {token: currentToken, action: 'delete'}, function (data) {
              if (!data) {
                //console.log('Token deleted.');
              }
            });
            // Once token is deleted update UI.
            resetUI();
          }).catch((err) => {
            console.log('Unable to delete token. ', err);
          });
        }).catch((err) => {
          console.log('Error retrieving registration token. ', err);
        });
      }
    }
  };
})(jQuery, Drupal, drupalSettings, once);
