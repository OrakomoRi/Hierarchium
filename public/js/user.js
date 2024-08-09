document.addEventListener('DOMContentLoaded', function () {
	const container = document.querySelector('.container');

	const modalContainer = document.createElement('div');
	modalContainer.className = 'modal_container';

	/**
	 * Show the modal for creating or editing a section
	 * @param {Array} parentOptions - Array of parent section options
	 * @param {Object|null} editSection - Section data for editing or null for creating a new section
	 */
	const showModal = (parentOptions = [], editSection = null) => {		
		modalContainer.innerHTML = `
			<div class="modal">
				<h1>${editSection ? 'Edit Section' : 'Create Section'}</h1>
				<form class="modal_form">
					<label for="title">Title:</label>
					<input type="text" class="title" name="title" maxlength="40" required value="${editSection ? editSection.title : 'section'}">
					<label for="description">Description:</label>
					<textarea class="description" name="description" maxlength="400" required>${editSection ? editSection.description : 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Sapiente, labore?'}</textarea>
					${parentOptions.length ? `
						<label>Parent:</label>
						<div class="parent_id custom_select">
							<span class="custom_select_selected" data-value="${editSection && editSection.parent_id ? editSection.parent_id : 'none'}">
								${editSection && editSection.parent_id 
									? (() => {
										// Find the option with the matching ID
										var option = parentOptions.find(function(option) {
											return option.id === editSection.parent_id;
										});
										// Return the option name or fallback to '&lt;none&gt;'
										return option ? `<span class="option_name">${option.name}</span>&nbsp;<span>#${option.id}</span>` : '&lt;none&gt;';
									})()
									: '&lt;none&gt;'}
							</span>
							<div class="custom_select_options">
								<span data-value="none">&lt;none&gt;</span>
								${parentOptions
									.filter(option => !editSection || option.id !== editSection.id)
									.map(option => `
										<span data-value="${option.id}" ${editSection && editSection.parent_id === option.id ? 'class="selected"' : ''}>
											<span class="option_name">${option.name}</span>&nbsp;<span>#${option.id}</span>
										</span>
									`)
									.join('')}
							</div>
						</div>
					` : ''}
					<button type="submit">${editSection ? 'Save' : 'Create'}</button>
				</form>
			</div>
		`;

		document.body.classList.add('no-scroll');
		document.body.appendChild(modalContainer);

		const submitButton = modalContainer.querySelector('.modal button');
        if (submitButton) {
            submitButton.focus();
        }

		// Handle custom select dropdown
		const customSelect = modalContainer.querySelector('.custom_select');
		if (customSelect) {
			const selected = customSelect.querySelector('.custom_select_selected');
			const optionsContainer = customSelect.querySelector('.custom_select_options');
			const options = optionsContainer.querySelectorAll('span');

			selected.addEventListener('click', () => {
				optionsContainer.classList.toggle('show');
				updateCustomSelectHeight();
			});

			options.forEach(option => {
				option.addEventListener('click', () => {
					selected.innerHTML = option.innerHTML;
					selected.dataset.value = option.dataset.value;
					optionsContainer.classList.remove('show');
					options.forEach(opt => opt.classList.remove('selected'));
					option.classList.add('selected');
				});
			});

			window.addEventListener('click', (e) => {
				if (!customSelect.contains(e.target)) {
					optionsContainer.classList.remove('show');
				}
			});
		}

		// Handle form submission
		modalContainer.querySelector('.modal_form').addEventListener('submit', (event) => {
			event.preventDefault();
			const formData = new FormData(event.target);

			if (customSelect) {
				const selected = customSelect.querySelector('.custom_select_selected');
				formData.append('parent_id', selected.dataset.value === 'none' ? null : selected.dataset.value);
			} else {
				formData.append('parent_id', null);
			}

			if (editSection) {
				updateSection(editSection, formData);
			} else {
				createSection(formData);
			}
		});
	};

	/**
	 * Update the height of the custom select dropdown
	 */
	const updateCustomSelectHeight = () => {
		const customSelects = modalContainer.querySelectorAll('.custom_select');
		customSelects.forEach(customSelect => {
			const optionsContainer = customSelect.querySelector('.custom_select_options');
			const options = optionsContainer.querySelectorAll('span');

			if (options.length === 0) return;

			optionsContainer.style.maxHeight = 'none'; // Reset for recalculation
			const optionHeight = options[0].offsetHeight;
			const maxVisibleOptions = 4;
			const totalHeight = Math.min(options.length, maxVisibleOptions) * optionHeight;

			optionsContainer.style.maxHeight = `${totalHeight}px`;
		});
	};

	/**
	 * Close all open custom selects when the window is resized
	 */
	const handleWindowResize = () => {
		const openSelects = modalContainer.querySelectorAll('.custom_select_options.show');
		openSelects.forEach(optionsContainer => {
			optionsContainer.classList.remove('show');
		});
	};

	// Add event listener for window resize
	window.addEventListener('resize', handleWindowResize);

	/**
	 * Create a new section via API call
	 * @param {FormData} formData - The form data to send to the server
	 */
	const createSection = (formData) => {
		fetch('/sections/create', {
			method: 'POST',
			body: formData,
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
		.then(response => response.text())
		.then(text => {
			try {
				const data = JSON.parse(text);
				if (data.success) {
					if (data.section) {
						// Remove '.no-sections' element if it exists
						var noSectionsElement = document.querySelector('.no-sections');
						if (noSectionsElement) {
							noSectionsElement.remove();
						}
						const parentId = formData.get('parent_id');

						if (parentId === 'null') {
							renderSections([data.section], container);
						} else {
							const parentSectionElement = container.querySelector(`.section[data-id="${parentId}"]`);
							if (parentSectionElement) {
								let parentSubsectionsContainer = parentSectionElement.querySelector('.subsections');
								if (!parentSubsectionsContainer) {
									parentSubsectionsContainer = document.createElement('div');
									parentSubsectionsContainer.className = 'subsections';
									parentSectionElement.querySelector('.section_body').appendChild(parentSubsectionsContainer);
								}
								renderSections([data.section], parentSubsectionsContainer);
							} else {
								showNotificationMessage('Parent section not found', 'error');
							}
						}

						document.body.removeChild(modalContainer);
					} else {
						showNotificationMessage('Section data is missing', 'error');
					}
				} else {
					showNotificationMessage(data.error, 'error');
				}
			} catch (error) {
				showNotificationMessage('Failed to parse JSON: ' + error.message, 'error');
			}
		})
		.catch(error => showNotificationMessage('Error: ' + error.message, 'error'));
	};

	/**
	 * Update an existing section via API call
	 * @param {number} id - The ID of the section to update
	 * @param {FormData} formData - The form data to send to the server
	 */
	const updateSection = (section, formData) => {
		formData.append('id', section.id);
		fetch('/sections/update', {
			method: 'POST',
			body: formData,
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
		.then(response => response.text())
		.then(text => {
			try {
				const data = JSON.parse(text);
				if (data.success) {
					const sectionElement = container.querySelector(`.section[data-id="${section.id}"]`);
					if (sectionElement) {
						// Update the section's title and description
						const titleElement = sectionElement.querySelector('.section_header h3');
						const descriptionElement = sectionElement.querySelector('.section_body p');

						// Preserve the section_id span and only update the title and description
						const sectionIdSpan = titleElement.querySelector('.section_id');
						titleElement.innerHTML = `${formData.get('title')} `;
						if (sectionIdSpan) {
							titleElement.appendChild(sectionIdSpan);
						}
						descriptionElement.innerText = formData.get('description');

						// Get new and old parent IDs
						const newParentId = formData.get('parent_id');
						const oldParentId = sectionElement.dataset.parent_id;

						if (oldParentId !== newParentId) {
							// Remove section from old parent's subsections
							if (oldParentId) {
								const oldParent = container.querySelector(`.section[data-id="${oldParentId}"] .subsections`);
								if (oldParent) {
									oldParent.removeChild(sectionElement);
								}
							}

							// Move section to new parent's subsections
							if (newParentId) {
								const newParent = container.querySelector(`.section[data-id="${newParentId}"]`);
								if (newParent) {
									let subsectionsContainer = newParent.querySelector('.subsections');
									if (!subsectionsContainer) {
										subsectionsContainer = document.createElement('div');
										subsectionsContainer.className = 'subsections';
										newParent.querySelector('.section_body').appendChild(subsectionsContainer);
									}
									subsectionsContainer.appendChild(sectionElement);
								}
							} else {
								container.appendChild(sectionElement);
							}

							// Update section's parent_id attribute
							sectionElement.dataset.parent_id = newParentId;
						}

						// Close the modal
						document.body.removeChild(modalContainer);
						document.body.classList.remove('no-scroll');

						// Update the section
						newParentId ? section.parent_id = newParentId : null;
						section.title = formData.get('title');
						section.description = formData.get('description');
					}
				} else {
					showNotificationMessage(data.error, 'error');
				}
			} catch (error) {
				showNotificationMessage('Failed to parse JSON: ' + error.message, 'error');
			}
		})
		.catch(error => showNotificationMessage('Error: ' + error.message, 'error'));
	};

	/**
	 * Get the parent section options for the custom select
	 * @returns {Array} - Array of parent section options
	 */
	const getParentOptions = () => {
		return Array.from(container.querySelectorAll('.section')).map(section => ({
			id: section.dataset.id,
			name: section.querySelector('.section_header h3').innerText
		}));
	};

	// Show modal on clicking the create button
	document.querySelector('.create').addEventListener('click', () => {
		const parentOptions = getParentOptions();
		showModal(parentOptions);
	});

	// Event listener to close modal on clicking outside
	window.addEventListener('mousedown', function (event) {
		if (event.target === modalContainer) {
			document.body.removeChild(modalContainer);
			document.body.classList.remove('no-scroll');
		}
	});

	// Function to load and render sections from the server
	const loadSections = () => {
		fetch('/sections/get', {
			method: 'GET',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				renderSections(data.sections);
			} else {
				showNotificationMessage(data.error, 'error');
			}
		})
		.catch(error => showNotificationMessage('Error: ' + error.message, 'error'));
	};

	/**
	 * Recursively counts all subsections within a given section element
	 * @param {HTMLElement} sectionElement - The section element to count subsections in
	 * @returns {number} - The total count of subsections
	 */
	const countSubsections = (sectionElement) => {
		let count = 0;
		const subsections = sectionElement.querySelectorAll('.section');
		count += subsections.length;
		subsections.forEach(subsection => {
			count += countSubsections(subsection);
		});
		return count;
	};

	/**
	 * Renders sections and their subsections into the provided parent element
	 * @param {Array} sections - Array of section objects to render
	 * @param {HTMLElement} [parent=container] - The parent element to append sections to
	 */
	const renderSections = (sections, parent = container) => {
		sections.forEach(section => {
			const existingSection = parent.querySelector(`.section[data-id="${section.id}"]`);
			if (existingSection) return;

			// Create section element
			const sectionElement = document.createElement('div');
			sectionElement.className = 'section';
			sectionElement.dataset.id = section.id;
			sectionElement.dataset.parent_id = section.parent_id;
			sectionElement.innerHTML = `
				<div class="section_header">
					<span class="toggle">
						<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M55.215 0v512l401.569-256z"/></svg>
					</span>
					<h3>${section.title}&nbsp;</h3>
					<div class="action_buttons">
						<button class="edit">
							<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 405.333V512h106.667l314.302-314.305L314.304 91.031zm503.469-290.136c11.375-11.374 11.375-28.445 0-39.82L436.622 8.531c-11.374-11.375-28.445-11.375-39.82 0l-52.624 52.625L450.844 167.82z"/></svg>
						</button>
						<button class="delete">
							<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M85.334 455.239c0 31.222 25.597 56.761 56.889 56.761h227.556c31.291 0 56.888-25.539 56.888-56.761V128H85.334zm384-412.572H362.667L326.954 0H185.048l-35.714 42.667H42.667v42.666h426.667z"/></svg>
						</button>
					</div>
					<span class="section_id">#${section.id}</span>
				</div>
				<div class="section_body" style="display: none;">
					<p>${section.description}</p>
				</div>
			`;
			parent.appendChild(sectionElement);

			// Render subsections if present
			if (section.subsections && section.subsections.length) {
				const subsectionContainer = document.createElement('div');
				subsectionContainer.className = 'subsections';
				renderSections(section.subsections, subsectionContainer);
				sectionElement.querySelector('.section_body').appendChild(subsectionContainer);
			}

			/**
			 * Get a random color excluding specified colors
			 * @param {Array} excludeColors - Colors to exclude from the selection
			 * @returns {string} - A unique color code
			 */
			const getUniqueColor = (excludeColors) => {
				const colors = [
					'#664d00', '#6e2a0c', '#691312', '#5d0933',
					'#291938', '#042d3a', '#12403c', '#475200'
				];
				const filteredColors = colors.filter(color => !excludeColors.includes(color));
				return filteredColors[Math.floor(Math.random() * filteredColors.length)];
			};

			/**
			 * Get colors of adjacent and parent sections
			 * @param {HTMLElement} sectionElement - The section element to check
			 * @returns {Array} - Array of colors to exclude
			 */
			const getExcludedColors = (sectionElement) => {
				const excludeColors = [];
	
				// Helper function to add background color if available
				const addBackgroundColor = (element) => {
					if (element && element.classList.contains('section') && element.dataset.background) {
						excludeColors.push(element.dataset.background);
					}
				};
	
				addBackgroundColor(sectionElement.previousElementSibling);
				addBackgroundColor(sectionElement.nextElementSibling);
	
				const parentSection = findClosestSectionParent(sectionElement.parentElement);
				addBackgroundColor(parentSection);
	
				const subsectionsContainer = sectionElement.querySelector('.section_body .subsections');
				if (subsectionsContainer) {
					Array.from(subsectionsContainer.children)
						.filter(child => child.classList.contains('section'))
						.forEach(child => {
							if (child.dataset.background) {
								excludeColors.push(child.dataset.background);
							}
						});
				}
	
				return excludeColors;
			};

			/**
			 * Update section background color when opening
			 * @param {HTMLElement} sectionElement - The section element to update
			 */
			const updateSectionBackgroundColorOnOpen = (sectionElement) => {
				const excludeColors = getExcludedColors(sectionElement);
				const randomColor = getUniqueColor(excludeColors);
				sectionElement.style.backgroundColor = randomColor;
				sectionElement.dataset.background = randomColor;
			};

			/**
			 * Find the closest parent section element
			 * @param {HTMLElement} element - The starting element to search from
			 * @returns {HTMLElement|null} - The closest section parent or null
			 */
			const findClosestSectionParent = (element) => {
				while (element && !element.classList.contains('section')) {
					element = element.parentElement;
				}
				return element;
			};

			// Toggle section body visibility on click
			sectionElement.querySelector('.toggle').addEventListener('click', function () {
				const body = sectionElement.querySelector('.section_body');
				const isOpened = body.style.display === 'block';
				
				if (isOpened) {
					body.style.display = 'none';
					this.classList.remove('opened');
					sectionElement.style.backgroundColor = '';
					delete sectionElement.dataset.background;
				} else {
					body.style.display = 'block';
					this.classList.add('opened');
					updateSectionBackgroundColorOnOpen(sectionElement);
				}
			});

			// Open edit modal on click
			sectionElement.querySelector('.edit').addEventListener('click', function () {
				showModal(getParentOptions(), section);
			});

			// Delete section on click
			sectionElement.querySelector('.delete').addEventListener('click', function () {
				const subsectionCount = countSubsections(sectionElement);
				fetch('/sections/delete', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
					body: JSON.stringify({ id: section.id })
				})
				.then(response => response.text())
				.then(text => {
					try {
						const data = JSON.parse(text);
						if (data.success) {
							sectionElement.remove();
							const subsectionText = subsectionCount === 1 ? 'subsection' : 'subsections';
							showNotificationMessage(`Deleted section with ${subsectionCount} ${subsectionText}`, 'info');
							if (container.querySelectorAll('.section').length === 0) {
								const noSectionsMessage = document.createElement('p');
								noSectionsMessage.className = 'no-sections';
								noSectionsMessage.innerText = 'No sections available.';
								container.appendChild(noSectionsMessage);
							}
						} else {
							showNotificationMessage(data.error, 'error');
						}
					} catch (error) {
						showNotificationMessage('Failed to parse JSON: ' + error.message, 'error');
					}
				})
				.catch(error => showNotificationMessage('Error: ' + error.message, 'error'));
			});
		});

		// Show message if no sections are available
		if (sections.length === 0 && !parent.querySelector('.no-sections')) {
			const noSectionsMessage = document.createElement('p');
			noSectionsMessage.className = 'no-sections';
			noSectionsMessage.innerText = 'No sections available.';
			parent.appendChild(noSectionsMessage);
		}
	};

	// Load sections on page load
	loadSections();
});