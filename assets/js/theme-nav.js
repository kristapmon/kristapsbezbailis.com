(function () {
	'use strict';

	var FOCUSABLE_SELECTORS = [
		'a[href]',
		'button:not([disabled])',
		'input:not([disabled])',
		'select:not([disabled])',
		'textarea:not([disabled])',
		'[tabindex]:not([tabindex="-1"])',
	].join(', ');

	function getFocusableElements(container) {
		return Array.prototype.slice.call(container.querySelectorAll(FOCUSABLE_SELECTORS));
	}

	function trapFocus(event, overlay) {
		var focusable = getFocusableElements(overlay);
		if (!focusable.length) {
			event.preventDefault();
			return;
		}
		var first = focusable[0];
		var last  = focusable[focusable.length - 1];

		if (event.shiftKey) {
			if (document.activeElement === first) {
				event.preventDefault();
				last.focus();
			}
		} else {
			if (document.activeElement === last) {
				event.preventDefault();
				first.focus();
			}
		}
	}

	function onKeyDown(event) {
		if (event.key === 'Escape' || event.keyCode === 27) {
			if (document.body.classList.contains('mobile-menu-open')) {
				closeMobileMenu();
			}
			return;
		}
		if (event.key === 'Tab' || event.keyCode === 9) {
			var overlay = document.getElementById('mobile-menu-overlay');
			if (overlay) {
				trapFocus(event, overlay);
			}
		}
	}

	function openMobileMenu(btn, icon, overlay) {
		btn.setAttribute('aria-expanded', 'true');
		icon.className = 'fa-solid fa-xmark';
		overlay.removeAttribute('aria-hidden');
		document.addEventListener('keydown', onKeyDown);

		// Move focus to first focusable element in overlay
		var focusable = getFocusableElements(overlay);
		if (focusable.length) {
			focusable[0].focus();
		}
	}

	function closeMobileMenu() {
		var body    = document.body;
		var btn     = document.querySelector('.mobile-menu .icon');
		var icon    = btn ? btn.querySelector('i') : null;
		var overlay = document.getElementById('mobile-menu-overlay');

		body.classList.remove('mobile-menu-open');
		if (btn) { btn.setAttribute('aria-expanded', 'false'); }
		if (icon) { icon.className = 'fa-solid fa-bars'; }
		if (overlay) { overlay.setAttribute('aria-hidden', 'true'); }
		document.removeEventListener('keydown', onKeyDown);

		// Return focus to trigger button
		if (btn) { btn.focus(); }
	}

	window.toggleMobileMenu = function () {
		var body    = document.body;
		var btn     = document.querySelector('.mobile-menu .icon');
		var icon    = btn ? btn.querySelector('i') : null;
		var overlay = document.getElementById('mobile-menu-overlay');
		var isOpen  = body.classList.toggle('mobile-menu-open');

		if (isOpen) {
			openMobileMenu(btn, icon, overlay);
		} else {
			closeMobileMenu();
		}
	};

}());
