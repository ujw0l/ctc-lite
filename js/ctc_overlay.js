/*
 * CTC Gallery Viewer
 * A dependency-free, responsive image gallery viewer.
 * MIT License
 */

"use strict";

class ctcOverlayViewer {
	constructor(selector, options) {
		this.options = Object.assign({
			slideshowInterval: 3000,
			closeOnBackdrop: true,
			loop: true
		}, options && typeof options === "object" ? options : {});
		this.ssIntervalId = 0;
		this.activeIndex = 0;
		this.gallery = [];
		this.overlay = null;
		this.lastFocusedElement = null;
		this.previousBodyOverflow = "";
		this.touchStartX = 0;
		this.boundResize = event => this.adjustApp(event);
		this.boundKeydown = event => this.onKeyStroke(event);

		Array.from(document.querySelectorAll(selector)).forEach((gallery, index) => {
			this.prepareGal(gallery, index, this.options);
		});
		window.addEventListener("resize", this.boundResize);
		window.addEventListener("keydown", this.boundKeydown);
	}

	prepareGal(gallery, galleryIndex, options) {
		const images = Array.from(gallery.querySelectorAll("img"));
		images.forEach((image, imageIndex) => {
			image.setAttribute("data-ctc-gallery-index", galleryIndex);
			if (!image.hasAttribute("tabindex") && !image.closest("a, button")) image.tabIndex = 0;
			if (!image.hasAttribute("role") && !image.closest("a, button")) image.setAttribute("role", "button");
			if (!image.hasAttribute("aria-label")) {
				image.setAttribute("aria-label", `Open image ${imageIndex + 1} of ${images.length}`);
			}
			const open = event => {
				if (event.type === "keydown" && event.key !== "Enter" && event.key !== " ") return;
				if (event.type === "keydown") event.preventDefault();
				this.createOverlay(image, imageIndex, images, options);
			};
			image.addEventListener("click", open);
			image.addEventListener("keydown", open);
		});
	}

	createOverlay(image, imageIndex, gallery, options) {
		if (!image || !gallery || !gallery.length) return;
		if (this.overlay) this.closeOverlay(this.overlay, false);

		this.options = Object.assign({}, this.options, options && typeof options === "object" ? options : {});
		this.gallery = Array.from(gallery);
		this.activeIndex = imageIndex;
		this.lastFocusedElement = document.activeElement;
		this.previousBodyOverflow = document.body.style.overflow;
		this.injectStyles();
		document.body.style.overflow = "hidden";

		const overlay = document.createElement("div");
		overlay.id = "gallery-overlay";
		overlay.className = "ctc-gallery-overlay";
		overlay.setAttribute("role", "dialog");
		overlay.setAttribute("aria-modal", "true");
		overlay.setAttribute("aria-label", "Image gallery viewer");
		overlay.innerHTML = `
			<div class="ctc-gallery__backdrop" data-ctc-close></div>
			<div class="ctc-gallery__shell">
				<div class="ctc-gallery__topbar">
					<div class="ctc-gallery__brand" aria-hidden="true"><span></span> Gallery</div>
					<div id="ctc-image-counter" class="ctc-gallery__counter" aria-live="polite"></div>
					<button id="overlay-close-btn" class="ctc-gallery__button ctc-gallery__close" type="button" title="Close" aria-label="Close gallery">${this.icon("close")}</button>
				</div>
				<div class="ctc-gallery__stage">
					<div id="image-loading-main" class="ctc-gallery__loading" role="status"><span class="ctc-gallery__spinner"></span><span>Loading image</span></div>
					<div id="ctc-image-error" class="ctc-gallery__error" role="alert" hidden>We couldn’t load this image.</div>
					<img id="loaded-img" class="ctc-gallery__image" alt="" draggable="false">
					<div class="ctc-gallery__tap-hint" aria-hidden="true">Click either side to browse</div>
				</div>
				<div id="img-title-info" class="ctc-gallery__caption"></div>
			</div>`;

		document.body.appendChild(overlay);
		this.overlay = overlay;
		const imageElement = overlay.querySelector("#loaded-img");

		overlay.querySelector("#overlay-close-btn").addEventListener("click", () => this.closeOverlay(overlay));
		if (this.options.closeOnBackdrop) {
			overlay.querySelector("[data-ctc-close]").addEventListener("click", () => this.closeOverlay(overlay));
		}
		imageElement.addEventListener("click", event => {
			if (this.gallery.length < 2) return;
			const bounds = event.currentTarget.getBoundingClientRect();
			this.loadImg(event.clientX > bounds.left + bounds.width / 2 ? this.nextIndex() : this.previousIndex(), this.gallery, overlay, imageElement);
		});
		imageElement.addEventListener("touchstart", event => {
			this.touchStartX = event.changedTouches[0].clientX;
		}, { passive: true });
		imageElement.addEventListener("touchend", event => {
			const distance = event.changedTouches[0].clientX - this.touchStartX;
			if (Math.abs(distance) > 45 && this.gallery.length > 1) {
				this.loadImg(distance < 0 ? this.nextIndex() : this.previousIndex(), this.gallery, overlay, imageElement);
			}
		}, { passive: true });

		if (this.gallery.length > 1) {
			this.createToolbar(overlay, this.gallery, imageElement, imageIndex, this.options);
			this.createSidebar(overlay, this.gallery, imageElement, imageIndex, this.options);
		} else {
			overlay.classList.add("ctc-gallery-overlay--single");
		}

		this.loadImg(imageIndex, this.gallery, overlay, imageElement);
		requestAnimationFrame(() => overlay.classList.add("is-visible"));
		overlay.querySelector("#overlay-close-btn").focus({ preventScroll: true });
	}

	createToolbar(overlay, gallery, imageElement, imageIndex) {
		let toolbar = overlay.querySelector("#toolbar-div");
		if (!toolbar) {
			toolbar = document.createElement("div");
			toolbar.id = "toolbar-div";
			toolbar.className = "ctc-gallery__toolbar";
			toolbar.setAttribute("role", "toolbar");
			toolbar.setAttribute("aria-label", "Gallery controls");

			const controls = [
				["gal-first-img", "first", "First image"],
				["gal-prev-img", "previous", "Previous image"],
				["img-zoom-out", "zoomOut", "Zoom out"],
				["gal-slide-show", "play", "Start slideshow"],
				["img-zoom-in", "zoomIn", "Zoom in"],
				["gal-next-img", "next", "Next image"],
				["gal-last-img", "last", "Last image"]
			];
			controls.forEach(([id, icon, label]) => {
				const button = document.createElement("button");
				button.id = id;
				button.type = "button";
				button.className = "ctc-gallery__button";
				button.title = label;
				button.setAttribute("aria-label", label);
				button.innerHTML = this.icon(icon);
				toolbar.appendChild(button);
			});

			toolbar.querySelector("#gal-first-img").addEventListener("click", () => this.loadImg(0, gallery, overlay, imageElement));
			toolbar.querySelector("#gal-prev-img").addEventListener("click", () => this.loadImg(this.previousIndex(), gallery, overlay, imageElement));
			toolbar.querySelector("#gal-next-img").addEventListener("click", () => this.loadImg(this.nextIndex(), gallery, overlay, imageElement));
			toolbar.querySelector("#gal-last-img").addEventListener("click", () => this.loadImg(gallery.length - 1, gallery, overlay, imageElement));
			toolbar.querySelector("#img-zoom-in").addEventListener("click", () => this.setZoom(this.getZoom(imageElement) + 0.2));
			toolbar.querySelector("#img-zoom-out").addEventListener("click", () => this.setZoom(this.getZoom(imageElement) - 0.2));
			toolbar.querySelector("#gal-slide-show").setAttribute("data-interval-id", "0");
			toolbar.querySelector("#gal-slide-show").addEventListener("click", () => this.toggleSlideshow());
			overlay.querySelector(".ctc-gallery__shell").appendChild(toolbar);
		}
		this.updateToolbar(imageIndex);
	}

	createSidebar(overlay, gallery, imageElement, imageClicked) {
		const sidebar = document.createElement("div");
		sidebar.id = "gal-sidebar";
		sidebar.className = "ctc-gallery__thumbnails";
		sidebar.setAttribute("role", "list");
		sidebar.setAttribute("aria-label", "Gallery thumbnails");

		gallery.forEach((image, index) => {
			const thumbnail = document.createElement("button");
			thumbnail.type = "button";
			thumbnail.className = "img-preview";
			thumbnail.setAttribute("role", "listitem");
			thumbnail.setAttribute("aria-label", `View image ${index + 1}: ${this.getTitle(image) || image.alt || "Untitled"}`);
			thumbnail.innerHTML = `<span class="ctc-gallery__thumb-loader"></span><img alt="" loading="lazy">`;
			const thumbnailImage = thumbnail.querySelector("img");
			thumbnailImage.addEventListener("load", () => thumbnail.classList.add("is-loaded"));
			thumbnailImage.addEventListener("error", () => thumbnail.classList.add("is-error"));
			thumbnailImage.src = image.currentSrc || image.src;
			thumbnail.addEventListener("click", () => this.loadImg(index, gallery, overlay, imageElement));
			sidebar.appendChild(thumbnail);
		});

		overlay.querySelector(".ctc-gallery__shell").appendChild(sidebar);
		this.scrollToPrev(imageClicked);
	}

	loadImg(imageIndex, gallery, overlay, imageElement) {
		if (!overlay || !imageElement || !gallery || !gallery.length) return;
		const normalizedIndex = Math.max(0, Math.min(Number(imageIndex) || 0, gallery.length - 1));
		const sourceImage = gallery[normalizedIndex];
		const source = sourceImage.currentSrc || sourceImage.src;
		const loader = overlay.querySelector("#image-loading-main");
		const error = overlay.querySelector("#ctc-image-error");

		this.activeIndex = normalizedIndex;
		this.setZoom(1);
		loader.hidden = false;
		error.hidden = true;
		imageElement.classList.remove("is-loaded");
		imageElement.alt = sourceImage.alt || this.getTitle(sourceImage) || `Gallery image ${normalizedIndex + 1}`;

		const preload = new Image();
		preload.onload = () => {
			if (normalizedIndex !== this.activeIndex || !this.overlay) return;
			imageElement.src = source;
			loader.hidden = true;
			requestAnimationFrame(() => imageElement.classList.add("is-loaded"));
		};
		preload.onerror = () => {
			if (normalizedIndex !== this.activeIndex || !this.overlay) return;
			loader.hidden = true;
			error.hidden = false;
		};
		preload.src = source;

		const title = this.getTitle(sourceImage);
		const caption = overlay.querySelector("#img-title-info");
		caption.textContent = title;
		caption.hidden = !title;
		overlay.querySelector("#ctc-image-counter").textContent = `${normalizedIndex + 1} / ${gallery.length}`;
		this.updateToolbar(normalizedIndex);
		this.scrollToPrev(normalizedIndex);
		this.preloadNeighbors(normalizedIndex);
	}

	scrollToPrev(imageIndex) {
		if (!this.overlay) return;
		Array.from(this.overlay.querySelectorAll(".img-preview")).forEach((thumbnail, index) => {
			const active = index === imageIndex;
			thumbnail.classList.toggle("is-active", active);
			thumbnail.setAttribute("aria-current", active ? "true" : "false");
			if (active) thumbnail.scrollIntoView({ block: "nearest", inline: "nearest", behavior: this.prefersReducedMotion() ? "auto" : "smooth" });
		});
	}

	adjustApp() {
		if (!this.overlay) return;
		this.overlay.style.setProperty("--ctc-viewport-height", `${window.innerHeight}px`);
	}

	closeOverlay(overlayElement, restoreFocus = true) {
		const overlay = overlayElement || this.overlay;
		if (!overlay) return;
		this.stopSlideshow();
		overlay.classList.remove("is-visible");
		const remove = () => {
			if (overlay.parentNode) overlay.parentNode.removeChild(overlay);
			if (this.overlay === overlay) this.overlay = null;
			document.body.style.overflow = this.previousBodyOverflow;
			if (restoreFocus && this.lastFocusedElement && typeof this.lastFocusedElement.focus === "function") this.lastFocusedElement.focus();
		};
		this.prefersReducedMotion() ? remove() : window.setTimeout(remove, 180);
	}

	getOptimizedImageSize(screenWidth, screenHeight, imageWidth, imageHeight, imageCount) {
		const rail = imageCount > 1 && screenWidth > 720 ? 116 : 0;
		const maxWidth = Math.max(0, screenWidth - rail - 64);
		const maxHeight = Math.max(0, screenHeight - (imageCount > 1 ? 184 : 112));
		if (!imageWidth || !imageHeight) return { width: maxWidth, height: maxHeight };
		const ratio = Math.min(1, maxWidth / imageWidth, maxHeight / imageHeight);
		return { width: imageWidth * ratio, height: imageHeight * ratio };
	}

	onKeyStroke(event) {
		if (!this.overlay) return;
		if (event.key === "Tab") {
			this.trapFocus(event);
			return;
		}
		const actions = {
			ArrowUp: () => this.overlay.querySelector("#img-zoom-in")?.click(),
			ArrowDown: () => this.overlay.querySelector("#img-zoom-out")?.click(),
			ArrowLeft: () => this.overlay.querySelector("#gal-prev-img")?.click(),
			ArrowRight: () => this.overlay.querySelector("#gal-next-img")?.click(),
			Escape: () => this.closeOverlay(this.overlay),
			Home: () => this.overlay.querySelector("#gal-first-img")?.click(),
			End: () => this.overlay.querySelector("#gal-last-img")?.click(),
			" ": () => this.overlay.querySelector("#gal-slide-show")?.click()
		};
		if (actions[event.key]) {
			event.preventDefault();
			actions[event.key]();
		}
	}

	destroy() {
		if (this.overlay) this.closeOverlay(this.overlay, false);
		window.removeEventListener("resize", this.boundResize);
		window.removeEventListener("keydown", this.boundKeydown);
	}

	getTitle(image) {
		return image.getAttribute("title") || image.getAttribute("data-caption") || "";
	}

	nextIndex() {
		return this.activeIndex >= this.gallery.length - 1 ? (this.options.loop ? 0 : this.activeIndex) : this.activeIndex + 1;
	}

	previousIndex() {
		return this.activeIndex <= 0 ? (this.options.loop ? this.gallery.length - 1 : 0) : this.activeIndex - 1;
	}

	getZoom(imageElement) {
		return Number(imageElement?.dataset.zoom || 1);
	}

	setZoom(value) {
		if (!this.overlay) return;
		const imageElement = this.overlay.querySelector("#loaded-img");
		if (!imageElement) return;
		const zoom = Math.min(4, Math.max(0.4, Math.round(value * 10) / 10));
		imageElement.dataset.zoom = String(zoom);
		imageElement.style.transform = `scale(${zoom})`;
		imageElement.classList.toggle("is-zoomed", zoom > 1);
	}

	toggleSlideshow() {
		if (this.ssIntervalId) {
			this.stopSlideshow();
			return;
		}
		const button = this.overlay?.querySelector("#gal-slide-show");
		if (!button) return;
		button.innerHTML = this.icon("pause");
		button.title = "Pause slideshow";
		button.setAttribute("aria-label", "Pause slideshow");
		button.setAttribute("aria-pressed", "true");
		this.ssIntervalId = window.setInterval(() => {
			if (this.overlay) this.loadImg(this.nextIndex(), this.gallery, this.overlay, this.overlay.querySelector("#loaded-img"));
		}, Math.max(1000, Number(this.options.slideshowInterval) || 3000));
		button.setAttribute("data-interval-id", String(this.ssIntervalId));
	}

	stopSlideshow() {
		if (this.ssIntervalId) window.clearInterval(this.ssIntervalId);
		this.ssIntervalId = 0;
		const button = this.overlay?.querySelector("#gal-slide-show");
		if (button) {
			button.innerHTML = this.icon("play");
			button.title = "Start slideshow";
			button.setAttribute("aria-label", "Start slideshow");
			button.setAttribute("aria-pressed", "false");
			button.setAttribute("data-interval-id", "0");
		}
	}

	updateToolbar(imageIndex) {
		if (!this.overlay) return;
		const previous = this.overlay.querySelector("#gal-prev-img");
		const next = this.overlay.querySelector("#gal-next-img");
		if (previous) previous.setAttribute("data-img-num", String(this.previousIndex()));
		if (next) next.setAttribute("data-img-num", String(this.nextIndex()));
		if (!this.options.loop) {
			[previous, this.overlay.querySelector("#gal-first-img")].forEach(button => { if (button) button.disabled = imageIndex === 0; });
			[next, this.overlay.querySelector("#gal-last-img")].forEach(button => { if (button) button.disabled = imageIndex === this.gallery.length - 1; });
		}
	}

	preloadNeighbors(imageIndex) {
		if (this.gallery.length < 2) return;
		[this.previousIndex(), this.nextIndex()].forEach(index => {
			const preload = new Image();
			preload.src = this.gallery[index].currentSrc || this.gallery[index].src;
		});
	}

	trapFocus(event) {
		const focusable = Array.from(this.overlay.querySelectorAll("button:not([disabled]), [tabindex]:not([tabindex='-1'])"));
		if (!focusable.length) return;
		const first = focusable[0];
		const last = focusable[focusable.length - 1];
		if (event.shiftKey && document.activeElement === first) {
			event.preventDefault();
			last.focus();
		} else if (!event.shiftKey && document.activeElement === last) {
			event.preventDefault();
			first.focus();
		}
	}

	prefersReducedMotion() {
		return window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
	}

	icon(name) {
		const paths = {
			close: '<path d="M6 6l12 12M18 6L6 18"/>',
			previous: '<path d="M15 18l-6-6 6-6"/>',
			next: '<path d="M9 18l6-6-6-6"/>',
			first: '<path d="M18 18l-6-6 6-6M6 6v12"/>',
			last: '<path d="M6 18l6-6-6-6M18 6v12"/>',
			zoomIn: '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4M11 8v6M8 11h6"/>',
			zoomOut: '<circle cx="11" cy="11" r="7"/><path d="M20 20l-4-4M8 11h6"/>',
			play: '<path d="M8 5l11 7-11 7V5z"/>',
			pause: '<path d="M9 5v14M15 5v14"/>'
		};
		return `<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">${paths[name] || ""}</svg>`;
	}

	injectStyles() {
		if (document.querySelector("#ctc-scroll-css")) return;
		const style = document.createElement("style");
		style.id = "ctc-scroll-css";
		style.textContent = `
			.ctc-gallery-overlay{--ctc-accent:#ffb648;--ctc-panel:rgba(17,18,22,.82);--ctc-border:rgba(255,255,255,.14);position:fixed;inset:0;z-index:1000000;height:var(--ctc-viewport-height,100dvh);color:#fff;font-family:Inter,ui-sans-serif,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;opacity:0;transition:opacity .18s ease;isolation:isolate}
			.ctc-gallery-overlay.is-visible{opacity:1}.ctc-gallery-overlay *{box-sizing:border-box}.ctc-gallery__backdrop{position:absolute;inset:0;background:radial-gradient(circle at 50% 40%,rgba(50,53,62,.72),rgba(5,6,8,.96) 75%);backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px)}
			.ctc-gallery__shell{position:relative;display:grid;grid-template-columns:104px minmax(0,1fr);grid-template-rows:64px minmax(0,1fr) auto 76px;width:100%;height:100%;padding:0 24px 16px 12px}
			.ctc-gallery__topbar{position:relative;z-index:2;grid-column:1/-1;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:8px 0 8px 12px}.ctc-gallery__brand{display:flex;align-items:center;gap:9px;color:rgba(255,255,255,.74);font-size:13px;font-weight:650;letter-spacing:.08em;text-transform:uppercase}.ctc-gallery__brand span{width:9px;height:9px;border-radius:50%;background:var(--ctc-accent);box-shadow:0 0 0 5px rgba(255,182,72,.12)}
			.ctc-gallery__counter{font-size:13px;font-variant-numeric:tabular-nums;color:rgba(255,255,255,.68);background:rgba(255,255,255,.07);border:1px solid var(--ctc-border);border-radius:999px;padding:6px 11px}.ctc-gallery__close{justify-self:end}
			.ctc-gallery__stage{position:absolute;z-index:1;inset:0 112px;display:flex;align-items:center;justify-content:center;min-height:0;overflow:hidden;border-radius:18px}.ctc-gallery__image{display:block;max-width:100%;max-height:calc(100% - 240px);width:auto;height:auto;object-fit:contain;opacity:0;cursor:ew-resize;filter:drop-shadow(0 24px 45px rgba(0,0,0,.38));transition:opacity .24s ease,transform .22s ease;user-select:none}.ctc-gallery__image.is-loaded{opacity:1}.ctc-gallery__image.is-zoomed{cursor:zoom-out}
			.ctc-gallery__loading,.ctc-gallery__error{position:absolute;display:flex;align-items:center;gap:10px;color:rgba(255,255,255,.72);font-size:13px}.ctc-gallery__loading[hidden],.ctc-gallery__error[hidden]{display:none}.ctc-gallery__spinner,.ctc-gallery__thumb-loader{width:18px;height:18px;border:2px solid rgba(255,255,255,.18);border-top-color:var(--ctc-accent);border-radius:50%;animation:ctc-spin .7s linear infinite}@keyframes ctc-spin{to{transform:rotate(360deg)}}
			.ctc-gallery__tap-hint{position:absolute;bottom:12px;padding:7px 11px;border-radius:999px;background:rgba(0,0,0,.44);color:rgba(255,255,255,.62);font-size:11px;opacity:0;transition:opacity .2s}.ctc-gallery__stage:hover .ctc-gallery__tap-hint{opacity:1}
			.ctc-gallery__caption{position:absolute;z-index:2;left:50%;bottom:90px;transform:translateX(-50%);width:max-content;max-width:min(720px,calc(100% - 32px));padding:12px 20px 4px;color:rgba(255,255,255,.78);font-size:14px;line-height:1.5;text-align:center}.ctc-gallery__caption[hidden]{display:block;visibility:hidden;padding-top:4px}
			.ctc-gallery__toolbar{position:absolute;z-index:2;left:50%;bottom:17px;transform:translateX(-50%);display:flex;align-items:center;gap:5px;padding:7px;background:var(--ctc-panel);border:1px solid var(--ctc-border);border-radius:16px;box-shadow:0 16px 38px rgba(0,0,0,.28);backdrop-filter:blur(18px)}
			.ctc-gallery__button{display:inline-grid;place-items:center;width:42px;height:42px;padding:0;border:1px solid transparent;border-radius:11px;background:transparent;color:rgba(255,255,255,.86);cursor:pointer;transition:background .15s,color .15s,transform .15s,border-color .15s}.ctc-gallery__button svg{width:20px;height:20px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.ctc-gallery__button:hover{background:rgba(255,255,255,.1);color:#fff}.ctc-gallery__button:active{transform:scale(.94)}.ctc-gallery__button:focus-visible{outline:2px solid var(--ctc-accent);outline-offset:2px}.ctc-gallery__button:disabled{opacity:.3;cursor:not-allowed}.ctc-gallery__toolbar #gal-slide-show{background:var(--ctc-accent);color:#1c1408}.ctc-gallery__toolbar #gal-slide-show:hover{background:#ffc46a}
			.ctc-gallery__thumbnails{position:absolute;z-index:2;left:16px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;justify-content:safe center;gap:9px;max-height:calc(100% - 96px);min-height:0;overflow:auto;padding:4px 12px 4px 4px;scrollbar-width:none}.ctc-gallery__thumbnails::-webkit-scrollbar{display:none}.img-preview{position:relative;flex:0 0 auto;width:72px;height:58px;padding:0;overflow:hidden;border:2px solid transparent;border-radius:11px;background:rgba(255,255,255,.07);cursor:pointer;opacity:.62;transition:opacity .15s,border-color .15s,transform .15s}.img-preview:hover{opacity:1;transform:translateX(2px)}.img-preview:focus-visible{outline:2px solid var(--ctc-accent);outline-offset:2px}.img-preview.is-active{opacity:1;border-color:var(--ctc-accent);box-shadow:0 0 0 3px rgba(255,182,72,.13)}.img-preview img{width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity .2s}.img-preview.is-loaded img{opacity:1}.img-preview .ctc-gallery__thumb-loader{position:absolute;inset:0;margin:auto;width:14px;height:14px}.img-preview.is-loaded .ctc-gallery__thumb-loader{display:none}
			.ctc-gallery-overlay--single .ctc-gallery__shell{grid-template-columns:1fr}.ctc-gallery-overlay--single .ctc-gallery__caption{bottom:24px}.ctc-gallery-overlay--single .ctc-gallery__tap-hint{display:none}
			@media(max-width:720px){.ctc-gallery__shell{grid-template-columns:1fr;grid-template-rows:58px minmax(0,1fr) auto 70px 84px;padding:0 12px 8px}.ctc-gallery__topbar{padding-left:4px}.ctc-gallery__brand{font-size:11px}.ctc-gallery__stage{inset:0 12px;border-radius:12px}.ctc-gallery__image{max-width:100%;max-height:calc(100% - 260px)}.ctc-gallery__caption{bottom:158px;font-size:13px;padding:8px 12px 2px}.ctc-gallery__toolbar{bottom:91px;gap:2px;padding:5px;max-width:calc(100% - 24px)}.ctc-gallery__button{width:38px;height:38px;border-radius:10px}.ctc-gallery__toolbar #gal-first-img,.ctc-gallery__toolbar #gal-last-img{display:none}.ctc-gallery__thumbnails{left:50%;top:auto;bottom:8px;transform:translateX(-50%);width:fit-content;max-width:calc(100% - 24px);max-height:none;flex-direction:row;justify-content:safe center;align-items:center;padding:8px 4px;gap:8px}.img-preview{width:64px;height:52px}.img-preview:hover{transform:translateY(-2px)}.ctc-gallery__tap-hint{display:none}}
			@media(max-width:380px){.ctc-gallery__button{width:36px;height:36px}.ctc-gallery__toolbar{gap:0}.ctc-gallery__brand{visibility:hidden}}
			@media(prefers-reduced-motion:reduce){.ctc-gallery-overlay,.ctc-gallery__image,.ctc-gallery__button,.img-preview{transition:none}.ctc-gallery__spinner,.ctc-gallery__thumb-loader{animation-duration:1.5s}}
		`;
		document.head.appendChild(style);
	}
}

if (typeof globalThis !== "undefined") globalThis.ctcOverlayViewer = ctcOverlayViewer;
if (typeof module !== "undefined" && module.exports) module.exports = ctcOverlayViewer;
