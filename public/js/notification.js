document.addEventListener('DOMContentLoaded', () => {
	let isMobile; // Indicates if the device is mobile
	let maxNotifications; // Maximum number of notifications allowed

	// Define notification types with their icon paths
	const Type = {
		error: { name: 'error', iconPath: '/images/svg/error-outline.svg' },
		info: { name: 'info', iconPath: '/images/svg/info-outline.svg' }
	};

	// Create and append the notification container to the body
	const notificationContainer = document.createElement('div');
	notificationContainer.className = 'notification_container';
	document.body.appendChild(notificationContainer);

	// Update settings based on screen size
	function updateNotificationSettings() {
		isMobile = window.matchMedia('(max-width: 767px)').matches;
		maxNotifications = isMobile ? 3 : 5;
	}

	updateNotificationSettings(); // Set initial values

	// Update settings when screen size changes
	window.addEventListener('resize', updateNotificationSettings);

	// Load an SVG icon from the given path
	async function loadSvgIcon(iconPath) {
		const response = await fetch(iconPath);
		const svg = await response.text();
		const div = document.createElement('div');
		div.innerHTML = svg;
		return div.firstChild;
	}

	// Show a notification message
	async function showNotificationMessage(message, type) {
		if (!Type[type]) {
			console.error(`Unknown notification type: ${type}`);
			return;
		}

		// Remove the oldest notification if at max capacity
		if (notificationContainer.children.length >= maxNotifications) {
			notificationContainer.removeChild(notificationContainer.firstChild);
		}

		// Create and configure the new notification
		const notification = document.createElement('div');
		notification.className = 'notification';

		const icon = await loadSvgIcon(Type[type].iconPath);
		if (icon) {
			icon.classList.add(Type[type].name);
			notification.appendChild(icon);
		}

		const text = document.createElement('span');
		text.innerText = message;
		notification.appendChild(text);

		notificationContainer.appendChild(notification);

		// Fade out and remove the notification after some time
		setTimeout(() => {
			if (notificationContainer.contains(notification)) {
				notification.classList.add('fade-out');
				setTimeout(() => {
					if (notificationContainer.contains(notification)) {
						notificationContainer.removeChild(notification);
					}
				}, 500);
			}
		}, 1500);
	}

	// Expose the showNotificationMessage function globally
	window.showNotificationMessage = showNotificationMessage;
});