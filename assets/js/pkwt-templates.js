/* global pkwtTemplatesData */
(function(){
	var ajaxUrl = pkwtTemplatesData.ajaxUrl;
	var i18n    = pkwtTemplatesData.i18n;

	document.querySelectorAll('.pkwt-dash-import-btn').forEach(function(btn){
		btn.addEventListener('click', function(){
			var setSlug    = btn.dataset.set;
			var pageType   = btn.dataset.pageType;
			var card       = btn.closest('.pkwt-tpl-card');
			var statusEl   = card ? card.querySelector('.pkwt-dash-import-status') : null;
			var allBtns    = card ? card.querySelectorAll('.pkwt-dash-import-btn') : [btn];
			var origHTML   = btn.innerHTML;

			allBtns.forEach(function(b){ b.disabled = true; });
			btn.innerHTML = '<span class="pkwt-spin-icon">&#9696;</span> ' + i18n.importing;

			if(statusEl){ statusEl.style.display='none'; statusEl.className='pkwt-dash-import-status'; statusEl.innerHTML=''; }

			// Step 1: fresh nonce at click time
			var nfd = new FormData();
			nfd.append('action', 'pkwt_get_import_nonce');

			fetch(ajaxUrl, { method:'POST', credentials:'same-origin', body:nfd })
			.then(function(r){ return r.json(); })
			.then(function(nd){
				if(!nd.success || !nd.data || !nd.data.nonce){ throw { isNonceFail:true }; }

				var fd = new FormData();
				fd.append('action',    'pkwt_ajax_import_template');
				fd.append('nonce',     nd.data.nonce);
				fd.append('set_slug',  setSlug);
				fd.append('page_type', pageType);

				return fetch(ajaxUrl, { method:'POST', credentials:'same-origin', body:fd })
				.then(function(r){
					return r.text().then(function(raw){
						var parsed;
						try { parsed = JSON.parse(raw); }
						catch(e){ throw { isRawResponse:true, raw:raw }; }
						return parsed;
					});
				});
			})
			.then(function(data){
				allBtns.forEach(function(b){ b.disabled = false; });
				btn.innerHTML = origHTML;
				if(statusEl){
					statusEl.style.display = 'block';
					if(data.success){
						statusEl.classList.add('pkwt-dash-import-status--ok');
						var msg  = (data.data && data.data.message) ? data.data.message : i18n.imported;
						var html = '&#10003; <strong>' + msg + '</strong>';
						if(data.data && data.data.edit_url)  html += ' <a href="'+data.data.edit_url+'"  target="_blank" class="pkwt-dash-import-link">'+i18n.openElementor+'</a>';
						if(data.data && data.data.view_url)  html += ' <a href="'+data.data.view_url+'"  target="_blank" class="pkwt-dash-import-link">'+i18n.viewPage+'</a>';
						statusEl.innerHTML = html;
					} else {
						statusEl.classList.add('pkwt-dash-import-status--err');
						statusEl.innerHTML = '&#10007; ' + ((data.data && data.data.message) ? data.data.message : i18n.importFailed);
					}
				}
			})
			.catch(function(err){
				allBtns.forEach(function(b){ b.disabled = false; });
				btn.innerHTML = origHTML;
				if(statusEl){
					statusEl.style.display = 'block';
					statusEl.classList.add('pkwt-dash-import-status--err');
					var msg;
					if(err && err.isNonceFail) msg = i18n.sessionExpired;
					else if(err && err.isRawResponse) msg = 'Server error: ' + String(err.raw||'').trim().slice(0,180);
					else msg = i18n.importFailedRetry;
					statusEl.innerHTML = '&#10007; ' + msg;
				}
			});
		});
	});
}());
