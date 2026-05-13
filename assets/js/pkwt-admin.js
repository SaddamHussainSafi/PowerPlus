document.addEventListener('DOMContentLoaded', () => {
	const trackedForms = document.querySelectorAll('.pkwt-ui form[action="options.php"], .dpp-feature-panel form[action="options.php"]');
	let hasDirtyForms = false;

	const formStates = new Map();

	const normalizeValue = (field) => {
		if (field.type === 'checkbox' || field.type === 'radio') {
			return field.checked ? '1' : '0';
		}
		return field.value ?? '';
	};

	const snapshotForm = (form) => {
		const snap = {};
		form.querySelectorAll('input, select, textarea').forEach((field) => {
			if (!field.name) return;
			snap[field.name + '::' + (field.value || '') + '::' + field.type] = normalizeValue(field);
		});
		return snap;
	};

	const hasSnapshotChanged = (initial, current) => {
		const initialKeys = Object.keys(initial);
		const currentKeys = Object.keys(current);
		if (initialKeys.length !== currentKeys.length) {
			return true;
		}
		for (const key of initialKeys) {
			if (initial[key] !== current[key]) {
				return true;
			}
		}
		return false;
	};

	const injectStickySaveBar = (form) => {
		if (form.querySelector('[data-pkwt-sticky-savebar]')) {
			return form.querySelector('[data-pkwt-sticky-savebar]');
		}

		const bar = document.createElement('div');
		bar.className = 'pkwt-sticky-savebar';
		bar.setAttribute('data-pkwt-sticky-savebar', '1');
		bar.setAttribute('data-state', 'pristine');
		bar.innerHTML = `
			<div class="pkwt-sticky-savebar__status" data-pkwt-savebar-status>You have unsaved changes</div>
			<div class="pkwt-sticky-savebar__actions">
				<button type="button" class="button button-secondary" data-pkwt-reset-form>Reset</button>
				<button type="button" class="button button-primary" data-pkwt-save-form>Save Changes</button>
			</div>
		`;
		document.body.appendChild(bar);
		return bar;
	};

	const updateSaveBarState = (bar, state, message) => {
		bar.setAttribute('data-state', state);
		const status = bar.querySelector('[data-pkwt-savebar-status]');
		if (status && message) {
			status.textContent = message;
		}
		if (state === 'pristine') {
			bar.classList.remove('is-visible');
		} else {
			bar.classList.add('is-visible');
		}
	};

	const setDirtyState = (form, dirty) => {
		const state = formStates.get(form);
		if (!state) return;
		state.isDirty = dirty;
		hasDirtyForms = Array.from(formStates.values()).some((f) => f.isDirty);
		if (dirty) {
			updateSaveBarState(state.bar, 'dirty', 'You have unsaved changes');
		} else {
			updateSaveBarState(state.bar, 'pristine', 'All changes saved');
		}
	};

	trackedForms.forEach((form) => {
		const state = {
			initial: snapshotForm(form),
			isDirty: false,
			bar: injectStickySaveBar(form),
		};
		formStates.set(form, state);

		const saveButton = state.bar.querySelector('[data-pkwt-save-form]');
		const resetButton = state.bar.querySelector('[data-pkwt-reset-form]');

		const checkFormDirty = () => {
			const current = snapshotForm(form);
			setDirtyState(form, hasSnapshotChanged(state.initial, current));
		};

		form.addEventListener('change', checkFormDirty);
		form.addEventListener('input', checkFormDirty);

		if (saveButton) {
			saveButton.addEventListener('click', () => {
				updateSaveBarState(state.bar, 'saving', 'Saving...');
				sessionStorage.setItem('pkwt_settings_saved_notice', '1');
				form.requestSubmit();
			});
		}

		if (resetButton) {
			resetButton.addEventListener('click', () => {
				form.reset();
				setTimeout(() => {
					checkFormDirty();
				}, 0);
			});
		}

		form.addEventListener('submit', () => {
			updateSaveBarState(state.bar, 'saving', 'Saving...');
			hasDirtyForms = false;
		});
	});

	window.addEventListener('beforeunload', (event) => {
		if (!hasDirtyForms) return;
		event.preventDefault();
		event.returnValue = '';
	});

	if (sessionStorage.getItem('pkwt_settings_saved_notice') === '1') {
		sessionStorage.removeItem('pkwt_settings_saved_notice');
		const noticeHost = document.querySelector('.wrap.pkwt-ui, .wrap.dpp-feature-panel');
		if (noticeHost) {
			const notice = document.createElement('div');
			notice.className = 'notice notice-success is-dismissible';
			notice.innerHTML = '<p>PowerKit - Powerful Tools For Your Website settings saved! Your WordPress power is activated.</p>';
			noticeHost.insertBefore(notice, noticeHost.children[1] || null);
		}
	}

	const addTopSaveBars = () => {
		document.querySelectorAll('.dpp-feature-panel form[action="options.php"], .pkwt-ui form[action="options.php"]').forEach((form) => {
			if (form.querySelector('[data-pkwt-top-savebar], .pkwt-savebar-top')) return;
			const submitBtn = form.querySelector('button[type="submit"], input[type="submit"]');
			if (!submitBtn) return;

			const bar = document.createElement('div');
			bar.className = 'pkwt-savebar pkwt-savebar-top';
			bar.setAttribute('data-pkwt-top-savebar', '1');

			const text = document.createElement('div');
			text.className = 'pkwt-savebar-text';
			text.textContent = 'Changes are saved only after clicking Save Changes.';

			const btn = document.createElement('button');
			btn.type = 'submit';
			btn.className = 'button button-primary';
			btn.textContent = 'Save Changes';

			bar.appendChild(text);
			bar.appendChild(btn);
			form.insertBefore(bar, form.firstElementChild);
		});
	};
	addTopSaveBars();

	const priority = document.querySelector('input[name="pkwt_settings[filter_priority]"]');
	if (priority) {
		priority.addEventListener('change', () => {
			const value = Number(priority.value);
			if (value < 1) priority.value = '1';
			if (value > 99) priority.value = '99';
		});
	}

	const search = document.querySelector('[data-pkwt-settings-search]');
	if (search) {
		search.addEventListener('input', () => {
			const term = search.value.trim().toLowerCase();
			document.querySelectorAll('.form-table tr').forEach((row) => {
				const text = row.textContent.toLowerCase();
				row.style.display = term && !text.includes(term) ? 'none' : '';
			});
		});
	}

	// Permalink-style slug editor for Login URL.
	const editSlugBtn    = document.getElementById('pkwt-edit-slug-btn');
	const slugOkBtn      = document.getElementById('pkwt-slug-ok');
	const slugCancelBtn  = document.getElementById('pkwt-slug-cancel');
	const slugInput      = document.getElementById('pkwt-slug-input');
	const hiddenUrlField = document.getElementById('pkwt-custom-login-url');
	const displayRow     = document.getElementById('pkwt-login-url-display');
	const editorRow      = document.getElementById('pkwt-login-url-editor');
	const prefixEl       = editorRow ? editorRow.querySelector('.pkwt-permalink-prefix') : null;

	if (editSlugBtn && slugInput && hiddenUrlField && displayRow && editorRow) {
		let originalSlug = slugInput.value;

		editSlugBtn.addEventListener('click', () => {
			originalSlug = slugInput.value;
			displayRow.style.display = 'none';
			editorRow.style.display  = 'flex';
			slugInput.focus();
			slugInput.select();
		});

		slugCancelBtn && slugCancelBtn.addEventListener('click', () => {
			slugInput.value          = originalSlug;
			editorRow.style.display  = 'none';
			displayRow.style.display = '';
		});

		const applySlug = () => {
			const raw  = slugInput.value.trim().toLowerCase().replace(/[^a-z0-9-]/g, '-').replace(/-+/g, '-').replace(/^-|-$/g, '');
			slugInput.value = raw;

			const prefix = prefixEl ? prefixEl.textContent : (window.location.origin + '/');
			const fullUrl = prefix + raw + '/';

			hiddenUrlField.value = fullUrl;

			// Update display label.
			const labelEl = displayRow.querySelector('.pkwt-permalink-label');
			if (labelEl) labelEl.textContent = fullUrl;

			// Update the View button href if present.
			const viewBtn = displayRow.querySelector('a.button-small');
			if (viewBtn) viewBtn.href = fullUrl;

			// Show edit button if it was hidden (first time set).
			if (editSlugBtn && editSlugBtn.style.display === 'none') {
				editSlugBtn.style.display = '';
			}

			editorRow.style.display  = 'none';
			displayRow.style.display = '';
		};

		slugOkBtn && slugOkBtn.addEventListener('click', applySlug);

		slugInput.addEventListener('keydown', (e) => {
			if (e.key === 'Enter') { e.preventDefault(); applySlug(); }
			if (e.key === 'Escape') { slugCancelBtn && slugCancelBtn.click(); }
		});
	}

	document.querySelectorAll('[data-pkwt-async-form]').forEach((form) => {
		form.addEventListener('submit', () => {
			const buttons = form.querySelectorAll('button[type="submit"],input[type="submit"]');
			buttons.forEach((btn) => {
				btn.dataset.originalText = btn.textContent || btn.value || '';
				if ('value' in btn) {
					btn.value = 'Working...';
				}
				btn.textContent = 'Working...';
				btn.disabled = true;
			});
		});
	});

	document.querySelectorAll('[data-pkwt-confirm]').forEach((trigger) => {
		trigger.addEventListener('click', (event) => {
			const message = trigger.getAttribute('data-pkwt-confirm') || 'Are you sure?';
			if (!window.confirm(message)) {
				event.preventDefault();
			}
		});
	});

	document.querySelectorAll('[data-pkwt-toggle-advanced]').forEach((toggle) => {
		const target = toggle.getAttribute('data-pkwt-toggle-advanced');
		const advancedRows = document.querySelectorAll(`[data-pkwt-advanced-group="${target}"]`);
		const render = () => {
			const show = toggle.checked;
			advancedRows.forEach((row) => {
				row.style.display = show ? '' : 'none';
			});
		};
		toggle.addEventListener('change', render);
		render();
	});

	const moduleSearch = document.querySelector('[data-pkwt-module-search]');
	const moduleFilterButtons = document.querySelectorAll('[data-pkwt-module-filter]');
	const moduleCards = document.querySelectorAll('[data-pkwt-module-card]');
	let activeModuleFilter = 'all';

	const filterModules = () => {
		const term = moduleSearch ? moduleSearch.value.trim().toLowerCase() : '';
		moduleCards.forEach((card) => {
			const state = card.getAttribute('data-module-state') || '';
			const category = card.getAttribute('data-module-category') || '';
			const text = card.textContent.toLowerCase();
			const termMatch = !term || text.includes(term);
			let filterMatch = true;
			if (activeModuleFilter === 'active' || activeModuleFilter === 'inactive') {
				filterMatch = state === activeModuleFilter;
			} else if (activeModuleFilter !== 'all') {
				filterMatch = category === activeModuleFilter;
			}
			card.style.display = termMatch && filterMatch ? '' : 'none';
		});
	};

	if (moduleSearch) {
		moduleSearch.addEventListener('input', filterModules);
	}
	moduleFilterButtons.forEach((button) => {
		button.addEventListener('click', () => {
			activeModuleFilter = button.getAttribute('data-pkwt-module-filter') || 'all';
			moduleFilterButtons.forEach((btn) => btn.classList.remove('is-active'));
			button.classList.add('is-active');
			filterModules();
		});
	});
	filterModules();

	document.querySelectorAll('[data-pkwt-module-toggle-form]').forEach((form) => {
		const checkbox = form.querySelector('[data-pkwt-module-switch]');
		const stateInput = form.querySelector('[data-pkwt-module-state]');
		if (!checkbox || !stateInput) return;
		checkbox.addEventListener('change', () => {
			stateInput.value = checkbox.checked ? 'on' : 'off';
			form.submit();
		});
	});

	// Force all legacy DPP toggles to render with the same switch system as Overview.
	document.querySelectorAll('input.dpp-toggle').forEach((input) => {
		input.classList.add('pkwt-toggle');
		if (input.closest('.pkwt-switch')) {
			return;
		}
		const parent = input.parentNode;
		if (!parent) return;
		const switchEl = document.createElement('label');
		switchEl.className = 'pkwt-switch';
		parent.insertBefore(switchEl, input);
		switchEl.appendChild(input);
		const track = document.createElement('span');
		track.className = 'pkwt-switch-track';
		switchEl.appendChild(track);
	});

	const guidePanel = document.querySelector('[data-pkwt-guide-panel]');
	if (guidePanel) {
		const hidden = localStorage.getItem('pkwt_guide_hidden') === '1';
		if (hidden) {
			guidePanel.style.display = 'none';
		}
		const dismiss = guidePanel.querySelector('[data-pkwt-guide-dismiss]');
		if (dismiss) {
			dismiss.addEventListener('click', () => {
				localStorage.setItem('pkwt_guide_hidden', '1');
				guidePanel.style.display = 'none';
			});
		}
	}
});
