document.addEventListener('DOMContentLoaded', () => {
	// Select login and signup forms
	const signinForm = document.querySelector('.signin_form');
	const signupForm = document.querySelector('.signup_form');

	// Switch to signup form
	document.querySelector('.to_signup').addEventListener('click', (event) => {
		event.preventDefault();
		signinForm.style.display = 'none';
		signupForm.style.display = 'flex';
	});

	// Switch to login form
	document.querySelector('.to_signin').addEventListener('click', (event) => {
		event.preventDefault();
		signupForm.style.display = 'none';
		signinForm.style.display = 'flex';
	});

	// Handle form submission
	signinForm.addEventListener('submit', async (event) => {
		event.preventDefault(); // Prevent default form submission

		const formData = new FormData(signinForm); // Create FormData from the form

		try {
			// Send the form data via POST request
			const response = await fetch(signinForm.action, {
				method: 'POST',
				body: formData,
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			});

			if (!response.ok) { // Check if the response is okay
				throw new Error('Network response was not ok');
			}

			const data = await response.json(); // Parse JSON response

			if (data.success) {
				window.location.href = '/user'; // Redirect on success
			} else {
				showNotificationMessage(data.error, 'error'); // Show error message
			}
		} catch (error) {
			console.error('Fetch error:', error); // Log fetch error
		}
	});

	// Handle signup form submission
	signupForm.addEventListener('submit', async (event) => {
		event.preventDefault();

		const formData = new FormData(signupForm);

		try {
			const response = await fetch(signupForm.action, {
				method: 'POST',
				body: formData,
				headers: { 'X-Requested-With': 'XMLHttpRequest' }
			});

			if (!response.ok) {
				throw new Error('Network response was not ok');
			}

			const data = await response.json();

			if (data.success) {
				window.location.href = '/user';
			} else {
				showNotificationMessage(data.error, 'error');
			}
		} catch (error) {
			console.error('Fetch error:', error);
		}
	});
});