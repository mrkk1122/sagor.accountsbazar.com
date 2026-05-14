<section class="ai-gen-page" id="ai-gen-page">
	<div class="container">
		<div class="ai-single-shell">
			<div class="ai-editor-panel">
				<h2>AI Photo Edit</h2>
				<p class="ai-editor-help">একটি ছবি আপলোড করুন, তারপর prompt লিখুন। AI আপনার ছবিটা edit করে দিবে।</p>

				<form id="ai-generate-form" class="ai-compose" action="#" method="post" enctype="multipart/form-data" onsubmit="return false;">
					<div class="field-wrap">
						<label for="ai-source-image">Source Photo</label>
						<input id="ai-source-image" type="file" accept="image/jpeg,image/png,image/webp" required>
					</div>

					<div class="field-wrap">
						<label for="ai-prompt">Edit Prompt</label>
						<textarea id="ai-prompt" rows="3" placeholder="উদাহরণ: background blur করে cinematic look দিন"></textarea>
					</div>

					<div class="ai-compose-footer">
						<span class="ai-foot-note">ছবি + prompt দিলে edit শুরু হবে</span>
						<button type="submit" class="btn btn-gold ai-send-btn" id="ai-generate-btn">Edit Photo</button>
					</div>
				</form>
			</div>

			<div class="ai-chat-main">
				<div class="ai-status-row">
					<strong class="ai-top-title">Edit History</strong>
					<span class="ai-subtle">AI Photo Edit Ready</span>
				</div>

				<div class="ai-messages" id="ai-messages" aria-live="polite">
					<div class="msg msg-ai">
						<p>প্রথমে একটি photo upload করুন, তারপর কী edit চান সেটা লিখুন।</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
(function () {
	var promptEl = document.getElementById('ai-prompt');
	var sourceImageEl = document.getElementById('ai-source-image');
	var submitBtn = document.getElementById('ai-generate-btn');
	var messagesEl = document.getElementById('ai-messages');
	var form = document.getElementById('ai-generate-form');

	if (!form || !messagesEl || !submitBtn || !sourceImageEl) {
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

	function addSourcePreviewMessage(file) {
		var objectUrl = URL.createObjectURL(file);
		addImageMessage(objectUrl, 'Source Image: ' + file.name);
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

	promptEl.addEventListener('keydown', function (e) {
		if (e.key === 'Enter' && !e.shiftKey) {
			e.preventDefault();
			form.dispatchEvent(new Event('submit'));
		}
	});

	form.addEventListener('submit', function () {
		var prompt = (promptEl.value || '').trim();
		var sourceFile = sourceImageEl.files && sourceImageEl.files[0] ? sourceImageEl.files[0] : null;

		if (!sourceFile) {
			addMessage('দয়া করে আগে একটি photo upload করুন।', 'ai');
			return;
		}

		if (prompt === '') {
			addMessage('দয়া করে আগে prompt লিখুন।', 'ai');
			return;
		}

		addSourcePreviewMessage(sourceFile);
		addMessage('Edit request: ' + prompt, 'user');

		submitBtn.disabled = true;
		submitBtn.textContent = 'Editing...';
		addTypingMessage();

		var formData = new FormData();
		formData.append('mode', 'photo_edit');
		formData.append('prompt', prompt);
		formData.append('source_image', sourceFile);

		fetch('/includes/ai_generate_api.php', {
			method: 'POST',
			body: formData
		})
		.then(function (res) {
			return res.text().then(function (txt) {
				try {
					var json = JSON.parse(txt);
					if (!res.ok && (!json || !json.error)) {
						return { ok: false, error: 'HTTP ' + res.status + ' error' };
					}
					return json;
				} catch (e) {
					var plain = (txt || '').trim();
					if (plain.length > 180) {
						plain = plain.slice(0, 180) + '...';
					}
					return {
						ok: false,
						error: 'Server JSON না দিয়ে অন্য response দিয়েছে (HTTP ' + res.status + '). ' + (plain || 'Empty response')
					};
				}
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

			if (data.final_prompt) {
				addMessage('Used prompt: ' + data.final_prompt, 'ai');
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
			submitBtn.textContent = 'Edit Photo';
		});
	});
})();
</script>
