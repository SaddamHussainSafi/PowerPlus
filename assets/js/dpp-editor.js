(() => {
	const cfg = window.PKWTEditor || {};
	if (!cfg.postId || !cfg.url || !cfg.nonce) return;

	const renderButton = () => {
		if (document.querySelector('.dpp-editor-duplicate')) return;

		const button = document.createElement('a');
		button.className = 'dpp-editor-duplicate';
		button.textContent = cfg.label || 'Duplicate Page';
		button.href = `${cfg.url}&_wpnonce=${encodeURIComponent(cfg.nonce)}`;
		button.style.cssText = 'position:fixed;right:24px;bottom:24px;z-index:99999;background:#6d28d9;color:#fff;padding:10px 14px;border-radius:6px;text-decoration:none;font-weight:600;box-shadow:0 6px 20px rgba(0,0,0,.2)';
		document.body.appendChild(button);
	};

	document.addEventListener('DOMContentLoaded', renderButton);
})();
