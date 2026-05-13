(() => {
	const cfg = window.PKWTFrontend || {};
	const ajaxUrl = cfg.ajaxUrl || '';
	const pending = new WeakMap();

	const setMessage = (formWrap, text, isError = false) => {
		const msg = formWrap.querySelector('.pkwt-message');
		if (!msg) return;
		msg.textContent = text || '';
		msg.classList.toggle('is-error', !!isError);
		msg.classList.toggle('is-success', !isError && !!text);
	};

	const clearFieldErrors = (form) => {
		form.querySelectorAll('.pkwt-field-error').forEach((node) => node.remove());
		form.querySelectorAll('.is-invalid').forEach((node) => node.classList.remove('is-invalid'));
	};

	const setFieldError = (form, errors = {}) => {
		clearFieldErrors(form);
		let firstInvalid = null;
		Object.entries(errors).forEach(([name, message]) => {
			const field = form.querySelector(`[name="${name}"]`);
			if (!field) return;
			field.classList.add('is-invalid');
			const id = `pkwt-error-${name}-${Math.random().toString(36).slice(2, 8)}`;
			field.setAttribute('aria-describedby', id);
			const note = document.createElement('small');
			note.id = id;
			note.className = 'pkwt-field-error';
			note.setAttribute('aria-live', 'polite');
			note.textContent = message;
			field.insertAdjacentElement('afterend', note);
			if (!firstInvalid) firstInvalid = field;
		});
		if (firstInvalid) firstInvalid.focus();
	};

	const validateField = (input) => {
		if (!input) return true;
		if (input.name === 'email') {
			const ok = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/.test(input.value.trim());
			input.classList.toggle('is-invalid', !ok);
			input.classList.toggle('is-valid', ok);
			return ok;
		}
		if (input.name === 'confirm_password') {
			const pwd = input.form?.querySelector('[name="password"]');
			const ok = !!pwd && input.value === pwd.value && input.value.length > 0;
			input.classList.toggle('is-invalid', !ok);
			input.classList.toggle('is-valid', ok);
			return ok;
		}
		return true;
	};

	const fetchWithTimeout = (resource, options = {}, timeoutMs = 15000) => {
		const timeout = new Promise((_, reject) => {
			setTimeout(() => reject(new Error('timeout')), timeoutMs);
		});
		return Promise.race([fetch(resource, options), timeout]);
	};

	const handleSubmit = async (event) => {
		event.preventDefault();
		const form = event.currentTarget;
		const wrap = form.closest('[data-pkwt-form]');
		if (!wrap || !ajaxUrl) return;

		if (!navigator.onLine) {
			setMessage(wrap, cfg.offlineError || 'You appear to be offline. Please check your connection.', true);
			return;
		}

		const submit = form.querySelector('.pkwt-submit');
		if (!submit) return;

		const oldController = pending.get(form);
		if (oldController) oldController.abort();
		const controller = new AbortController();
		pending.set(form, controller);

		const originalText = submit.textContent;
		submit.disabled = true;
		submit.classList.add('is-loading');
		submit.textContent = wrap.dataset.loadingText || cfg.pleaseWait || 'Please wait...';
		setMessage(wrap, '');
		clearFieldErrors(form);

		const formData = new FormData(form);
		formData.append('action', wrap.dataset.action || '');
		formData.append('nonce', wrap.dataset.nonce || '');

		try {
			const response = await fetchWithTimeout(ajaxUrl, { method: 'POST', credentials: 'same-origin', body: formData, signal: controller.signal }, 15000);
			const result = await response.json();
			if (result?.data?.nonce) {
				wrap.dataset.nonce = result.data.nonce;
			}

			if (result.success) {
				const redirect = result?.data?.redirect || wrap.dataset.successRedirect || '';
				setMessage(wrap, result?.data?.message || 'Success', false);
				if (redirect) {
					window.location.href = redirect;
					return;
				}
			} else {
				if (result?.data?.field_errors) {
					setFieldError(form, result.data.field_errors);
				}
				const retry = Number(result?.data?.retry_after || 0);
				const msg = retry > 0 ? `${result?.data?.message || 'Rate limited'} (${retry}s)` : (result?.data?.message || wrap.dataset.defaultError || 'Request failed');
				setMessage(wrap, msg, true);
			}
		} catch (error) {
			if (error?.name === 'AbortError') {
				return;
			}
			if (error?.message === 'timeout') {
				setMessage(wrap, cfg.slowConnection || 'Connection is slow. Please try again.', true);
			} else {
				setMessage(wrap, cfg.connectionError || 'Connection error. Please try again.', true);
			}
		} finally {
			submit.disabled = false;
			submit.classList.remove('is-loading');
			submit.textContent = originalText;
		}
	};

	const initForms = () => {
		document.querySelectorAll('[data-pkwt-form] form.pkwt-form').forEach((form) => {
			form.addEventListener('submit', handleSubmit);
			form.querySelectorAll('input').forEach((input) => {
				input.addEventListener('keyup', () => {
					input.classList.remove('is-invalid');
					setMessage(form.closest('[data-pkwt-form]'), '');
				});
			});
			form.querySelectorAll('input[name="email"], input[name="confirm_password"]').forEach((input) => {
				input.addEventListener('blur', () => validateField(input));
				input.addEventListener('focus', () => input.classList.remove('is-invalid', 'is-valid'));
			});
		});
	};

	const initPasswordToggle = () => {
		document.querySelectorAll('.pkwt-password-toggle').forEach((btn) => {
			btn.innerHTML = '<svg aria-hidden="true" viewBox="0 0 24 24" width="18" height="18"><path d="M12 5c-6 0-10 7-10 7s4 7 10 7 10-7 10-7-4-7-10-7Zm0 11a4 4 0 1 1 0-8 4 4 0 0 1 0 8Z" fill="currentColor"/></svg>';
			btn.addEventListener('click', () => {
				const input = btn.parentElement?.querySelector('input[type="password"], input[type="text"]');
				if (!input) return;
				const start = input.selectionStart;
				const end = input.selectionEnd;
				const nextType = input.type === 'password' ? 'text' : 'password';
				input.type = nextType;
				btn.setAttribute('aria-label', nextType === 'text' ? 'Hide password' : 'Show password');
				requestAnimationFrame(() => {
					if (typeof start === 'number' && typeof end === 'number') {
						input.setSelectionRange(start, end);
					}
				});
			});
		});
	};

	const initTabs = () => {
		document.querySelectorAll('[data-pkwt-tabs]').forEach((root) => {
			const buttons = root.querySelectorAll('.pkwt-auth-tab');
			const activate = (tab) => {
				buttons.forEach((btn) => btn.classList.toggle('is-active', btn.dataset.tab === tab));
				document.querySelectorAll('[data-pkwt-form]').forEach((wrap) => {
					const formType = wrap.dataset.cleForm;
					if (formType === 'login' || formType === 'register') {
						wrap.style.display = formType === tab ? '' : 'none';
					}
				});
				if (tab) window.location.hash = `#${tab}`;
			};
			buttons.forEach((btn) => btn.addEventListener('click', () => activate(btn.dataset.tab)));
			activate((window.location.hash || '').replace('#', '') || root.dataset.defaultTab || 'login');
		});
	};

	const initTimer = () => {
		document.querySelectorAll('[data-pkwt-redirect-timer]').forEach((root) => {
			let remaining = Number(root.dataset.seconds || 0);
			const template = root.dataset.message || 'Redirecting in {seconds} seconds...';
			const msg = root.querySelector('.pkwt-redirect-timer-message');
			const fill = root.querySelector('.pkwt-redirect-timer-bar-fill');
			if (!msg || remaining <= 0) return;
			const total = remaining;
			const tick = () => {
				msg.textContent = template.replace('{seconds}', String(remaining));
				if (fill) fill.style.width = `${Math.max(0, ((total - remaining) / total) * 100)}%`;
				remaining -= 1;
				if (remaining >= 0) setTimeout(tick, 1000);
			};
			tick();
		});
	};

	document.addEventListener('DOMContentLoaded', () => {
		initForms();
		initPasswordToggle();
		initTabs();
		initTimer();
	});
})();
