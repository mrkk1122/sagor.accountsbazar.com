<section class="ai-gen-page" id="ai-gen-page">
	<div class="container">
		<div class="ai-chat-shell">
			<aside class="ai-chat-sidebar">
				<div class="ai-side-head">
					<h3>AI Workspace</h3>
					<p>GPT-style quick panel</p>
				</div>
				<div class="ai-mode-buttons" role="tablist" aria-label="AI generation mode">
					<button type="button" class="mode-btn active" data-mode="photo" role="tab" aria-selected="true">Photo Generate</button>
					<button type="button" class="mode-btn" data-mode="video" role="tab" aria-selected="false">Video Generate</button>
				</div>
				<div class="ai-quick-list">
					<h4>Quick Ideas</h4>
					<button type="button" class="quick-item" data-prompt="cinematic portrait, warm rim light, ultra detailed">Cinematic Portrait</button>
					<button type="button" class="quick-item" data-prompt="premium product shot, glass reflection, dark studio">Product Ad Shot</button>
					<button type="button" class="quick-item" data-prompt="5s drone reveal of sea beach, smooth dolly in, golden hour">5s Beach Reveal</button>
				</div>
				<div class="ai-sidebar-note">
					<strong>Tip:</strong> prompt যত clear হবে, result তত better হবে।
				</div>
			</aside>

			<div class="ai-chat-main">
				<div class="ai-status-row">
					<div>
						<strong class="ai-top-title">AI Chat</strong>
						<span class="ai-badge" id="ai-current-mode">Photo Mode Active</span>
					</div>
					<span class="ai-subtle">Live API Ready</span>
				</div>

				<div class="ai-messages" id="ai-messages" aria-live="polite">
					<div class="msg msg-ai">
						<p>আমি আপনার AI assistant. আপনি photo নাকি video generate করতে চান?</p>
					</div>
				</div>

				<form id="ai-generate-form" class="ai-compose" action="#" method="post" onsubmit="return false;">
					<label for="ai-prompt" class="sr-only">Prompt</label>
					<textarea id="ai-prompt" rows="2" placeholder="Message AI generator..."></textarea>

					<div class="ai-options-grid">
						<div class="field-wrap" id="photo-options">
							<label for="photo-style">Photo Style</label>
							<select id="photo-style">
								<option value="Realistic">Realistic</option>
								<option value="Portrait">Portrait</option>
								<option value="Product">Product</option>
								<option value="Fantasy">Fantasy</option>
							</select>
						</div>
						<div class="field-wrap" id="video-options" hidden>
							<label for="video-length">Video Length</label>
							<select id="video-length">
								<option value="5s">5 seconds</option>
								<option value="10s">10 seconds</option>
								<option value="15s">15 seconds</option>
								<option value="30s">30 seconds</option>
							</select>
						</div>
					</div>

					<div class="ai-compose-footer">
						<span class="ai-foot-note">Enter চাপলে generate হবে</span>
						<button type="submit" class="btn btn-gold ai-send-btn" id="ai-generate-btn">Generate Photo</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>

<script>
(function () {
	var currentMode = 'photo';
	var modeButtons = document.querySelectorAll('.mode-btn');
	var currentModeBadge = document.getElementById('ai-current-mode');
	var promptEl = document.getElementById('ai-prompt');
	var submitBtn = document.getElementById('ai-generate-btn');
	var messagesEl = document.getElementById('ai-messages');
	var form = document.getElementById('ai-generate-form');
	var photoOptions = document.getElementById('photo-options');
	var videoOptions = document.getElementById('video-options');
	var quickItems = document.querySelectorAll('.quick-item');

	if (!form || !messagesEl || !submitBtn) {
		return;
	}

	function addMessage(text, type) {
		var wrapper = document.createElement('div');
		wrapper.className = 'msg ' + (type === 'user' ? 'msg-user' : 'msg-ai');
		var p = document.createElement('p');
		p.textContent = text;
		wrapper.appendChild(p);
		messagesEl.appendChild(wrapper);
		messagesEl.scrollTop = messagesEl.scrollHeight;
	}

	function addImageMessage(url, caption) {
		var wrapper = document.createElement('div');
		wrapper.className = 'msg msg-ai msg-media';

		var img = document.createElement('img');
		img.src = url;
		img.alt = caption || 'Generated photo';
		img.loading = 'lazy';

		var p = document.createElement('p');
		p.textContent = caption || 'Generated photo';

		wrapper.appendChild(img);
		wrapper.appendChild(p);
		messagesEl.appendChild(wrapper);
		messagesEl.scrollTop = messagesEl.scrollHeight;
	}

	function addTypingMessage() {
		var wrapper = document.createElement('div');
		wrapper.className = 'msg msg-ai msg-typing';
		wrapper.id = 'ai-typing';
		wrapper.innerHTML = '<span></span><span></span><span></span>';
		messagesEl.appendChild(wrapper);
		messagesEl.scrollTop = messagesEl.scrollHeight;
	}

	function removeTypingMessage() {
		var typing = document.getElementById('ai-typing');
		if (typing) {
			typing.remove();
		}
	}

	function setMode(mode) {
		currentMode = mode;
		var isPhoto = mode === 'photo';

		modeButtons.forEach(function (btn) {
			var active = btn.getAttribute('data-mode') === mode;
			btn.classList.toggle('active', active);
			btn.setAttribute('aria-selected', active ? 'true' : 'false');
		});

		photoOptions.hidden = !isPhoto;
		videoOptions.hidden = isPhoto;
		submitBtn.textContent = isPhoto ? 'Generate Photo' : 'Generate Video';
		currentModeBadge.textContent = isPhoto ? 'Photo Mode Active' : 'Video Mode Active';
		promptEl.placeholder = isPhoto
			? 'উদাহরণ: studio lighting এ professional passport photo'
			: 'উদাহরণ: 10 second cinematic drone shot at sea beach';
	}

	modeButtons.forEach(function (btn) {
		btn.addEventListener('click', function () {
			setMode(btn.getAttribute('data-mode') || 'photo');
		});
	});

	quickItems.forEach(function (item) {
		item.addEventListener('click', function () {
			promptEl.value = item.getAttribute('data-prompt') || '';
			promptEl.focus();
		});
	});

	promptEl.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			form.dispatchEvent(new Event('submit'));
		}
	});

	form.addEventListener('submit', function () {
		var prompt = (promptEl.value || '').trim();
		if (prompt === '') {
			addMessage('দয়া করে আগে prompt লিখুন।', 'ai');
			return;
		}

		var isPhoto = currentMode === 'photo';
		var meta = isPhoto
			? ('Style: ' + (document.getElementById('photo-style').value || 'Realistic'))
			: ('Length: ' + (document.getElementById('video-length').value || '5s'));

		addMessage((isPhoto ? 'Photo' : 'Video') + ' request: ' + prompt + ' (' + meta + ')', 'user');

		submitBtn.disabled = true;
		submitBtn.textContent = 'Generating...';
		addTypingMessage();

		fetch('/includes/ai_generate_api.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				mode: currentMode,
				prompt: prompt,
				style: document.getElementById('photo-style').value || 'Realistic',
				length: document.getElementById('video-length').value || '5s'
			})
		})
		.then(function (res) {
			return res.json().catch(function () {
				return { ok: false, error: 'Server response parse failed' };
			});
		})
		.then(function (data) {
			removeTypingMessage();
			if (!data || !data.ok) {
				addMessage((data && data.error) ? data.error : 'Generation failed. আবার চেষ্টা করুন।', 'ai');
				return;
			}

			if (data.image_url) {
				addImageMessage(data.image_url, 'Generated Image (Seed: ' + (data.seed || '-') + ')');
			}

			if (data.message) {
				addMessage(data.message, 'ai');
			}

			promptEl.value = '';
			promptEl.focus();
		})
		.catch(function () {
			removeTypingMessage();
			addMessage('Network error হয়েছে। আবার চেষ্টা করুন।', 'ai');
		})
		.finally(function () {
			submitBtn.disabled = false;
			submitBtn.textContent = isPhoto ? 'Generate Photo' : 'Generate Video';
		});
	});

	setMode('photo');
})();
</script>
