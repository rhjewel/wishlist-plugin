(function () {
	'use strict';

	function showToast(message) {
		if (!window.egwlData || !egwlData.toastEnabled || !message) {
			return;
		}

		var toast = document.createElement('div');
		toast.className = 'egwl-toast egwl-toast-' + egwlData.toastPosition;
		toast.textContent = message;
		document.body.appendChild(toast);

		window.setTimeout(function () {
			toast.classList.add('is-visible');
		}, 20);

		window.setTimeout(function () {
			toast.classList.remove('is-visible');
			window.setTimeout(function () {
				toast.remove();
			}, 250);
		}, 2500);
	}

	function updateCounts(count) {
		document.querySelectorAll('.egwl-count, .egwl-link span').forEach(function (node) {
			node.textContent = count;
		});
	}

	function setButtonState(button, isAdded) {
		button.classList.toggle('is-added', isAdded);
		button.setAttribute('aria-pressed', isAdded ? 'true' : 'false');

		var text = button.querySelector('.egwl-text');
		if (text && window.egwlData) {
			text.textContent = isAdded ? egwlData.addedText : egwlData.buttonText;
		}
	}

	function post(action, postId) {
		var data = new FormData();
		data.append('action', action);
		data.append('nonce', egwlData.nonce);
		data.append('post_id', postId);

		return fetch(egwlData.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: data
		}).then(function (response) {
			return response.json();
		});
	}

	document.addEventListener('click', function (event) {
		var wishlistButton = event.target.closest('.egwl-button');
		var removeButton = event.target.closest('.egwl-remove-button');

		if (!wishlistButton && !removeButton) {
			return;
		}

		if (!window.egwlData || !egwlData.ajaxEnabled) {
			return;
		}

		event.preventDefault();

		var button = wishlistButton || removeButton;
		var postId = button.getAttribute('data-post-id');
		var action = wishlistButton ? 'egwl_toggle' : 'egwl_remove';

		button.disabled = true;

		post(action, postId).then(function (payload) {
			button.disabled = false;

			if (!payload || !payload.success) {
				showToast(payload && payload.data ? payload.data.message : '');
				if (payload && payload.data && payload.data.code === 'login_required') {
					window.location.href = egwlData.loginUrl;
				}
				return;
			}

			if (wishlistButton) {
				setButtonState(wishlistButton, payload.data.isAdded);
			}

			if (removeButton) {
				var item = removeButton.closest('.egwl-item');
				if (item) {
					item.remove();
				}
			}

			updateCounts(payload.data.count);
			showToast(payload.data.message);
		}).catch(function () {
			button.disabled = false;
		});
	});
}());

