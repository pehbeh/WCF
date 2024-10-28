<?php

@\header('Service-Worker-Allowed: /');
@\header('Content-Type: text/javascript; charset=utf-8');
?>
/**
 * @author      Olaf Braun
 * @copyright   2001-2024 WoltLab GmbH
 * @license     GNU Lesser General Public License <http://opensource.org/licenses/lgpl-license.php>
 */

self.addEventListener("push", (event) => {
	if (!(self.Notification && self.Notification.permission === "granted")) {
		return;
	}
	if (!event.data) {
		return;
	}

	const payload = event.data.json();

	getLastNotificationTimestamp().then(lastNotificationTimestamp => {
    if (!lastNotificationTimestamp || payload.time < lastNotificationTimestamp) {
      return;
    }

    event.waitUntil(
      removeOldNotifications(payload.notificationID, payload.time).then(() =>
        self.registration.showNotification(payload.title, {
          body: payload.message,
          icon: payload.icon,
          timestamp: payload.time * 1000,
          tag: payload.notificationID,
          data: {
            url: payload.url,
            time: payload.time,
          },
        }),
      ).then(() => {
        sendToClients(payload);
      }),
    );
  });
});

self.addEventListener("notificationclick", (event) => {
	event.notification.close();

	event.waitUntil(self.clients.openWindow(event.notification.data.url));
});

self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SAVE_LAST_NOTIFICATION_TIMESTAMP') {
    saveLastNotificationTimestamp(event.data.timestamp);
  }
});

async function sendToClients(payload){
	const allClients = await self.clients.matchAll({
		includeUncontrolled: true,
		type: "window",
	});
	for (const client of allClients) {
		if (!client.url.startsWith(self.origin)) {
			continue;
		}
		client.postMessage(payload);
	}
}

async function removeOldNotifications(notificationID, time) {
	const notifications = await self.registration.getNotifications({ tag: notificationID });
	// Close old notifications
	notifications
		.filter((notifications) => {
			if (!notifications.data || !notifications.data.time) {
				return false;
			}
			return notifications.data.time <= time;
		})
		.forEach((notification) => {
			notification.close();
		});
}

/**
 * IndexedDB functions to store the last notification timestamp.
 */
function openDatabase() {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open('WOLTLAB_SUITE_CORE', 1);

    request.onupgradeneeded = (event) => {
      const db = event.target.result;
      if (!db.objectStoreNames.contains('notifications')) {
        db.createObjectStore('notifications');
      }
    };

    request.onsuccess = (event) => {
      resolve(event.target.result);

      if (!db.objectStoreNames.contains('notifications')) {
        db.createObjectStore('notifications');
      }
    };

    request.onerror = (event) => {
      reject('Database error: ' + event.target.errorCode);
    };
  });
}

function saveLastNotificationTimestamp(timestamp) {
  if (!timestamp || timestamp <= 0) {
    return;
  }

  openDatabase().then(db => {
    const tx = db.transaction('notifications', 'readwrite');
    const store = tx.objectStore('notifications');
    const getRequest = store.get('lastNotification');

    getRequest.onsuccess = () => {
      const storedTimestamp = getRequest.result;

      // Check if the new timestamp is greater than the stored timestamp
      if (storedTimestamp === undefined || timestamp > storedTimestamp) {
        store.put(timestamp, 'lastNotification');
      }
    };

    getRequest.onerror = () => {
      console.error('Failed to retrieve timestamp', getRequest.error);
    };

    tx.onerror = () => {
      console.error('Transaktionsfehler', tx.error);
    };
  }).catch(err => console.error('Failed to open database', err));
}

function getLastNotificationTimestamp() {
  return openDatabase().then(db => {
    return new Promise((resolve, reject) => {
      const tx = db.transaction('notifications', 'readonly');
      const store = tx.objectStore('notifications');
      const request = store.get('lastNotification');

      request.onsuccess = () => {
        resolve(request.result);
      };
      request.onerror = () => {
        reject('Failed to retrieve timestamp');
      };
    });
  });
}
