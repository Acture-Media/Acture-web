class Cursor {
	constructor(options) {
		this.options = $.extend(
			true,
			{
				container: "body",
				speed: 0.7,
				ease: "expo.out",
				visibleTimeout: 300,
			},
			options,
		);
		this.body = $(this.options.container);
		this.el = $('<div class="cb-cursor"></div>');
		this.text = $('<div class="cb-cursor-text"></div>');
		this.init();
	}

	init() {
		this.el.append(this.text);
		this.body.append(this.el);
		this.bind();
		this.move(-window.innerWidth, -window.innerHeight, 0);
	}

	bind() {
		const self = this;

		this.body
			.on("mouseleave", () => {
				self.hide();
			})
			.on("mouseenter", () => {
				self.show();
			})
			.on("mousemove", e => {
				this.pos = {
					x: this.stick
						? this.stick.x - (this.stick.x - e.clientX) * 0.15
						: e.clientX,
					y: this.stick
						? this.stick.y - (this.stick.y - e.clientY) * 0.15
						: e.clientY,
				};
				this.update();
			})
			.on("mousedown", () => {
				self.setState("-active");
			})
			.on("mouseup", () => {
				self.removeState("-active");
			})
			// Extended target elements
			.on("mouseenter", "a,input,textarea,button", () => {
				self.setState("-pointer");
				self.hide();
			})
			.on("mouseleave", "a,input,textarea,button", () => {
				self.removeState("-pointer");
				self.show();
			})
			.on("mouseenter", "iframe", () => {
				self.hide();
			})
			.on("mouseleave", "iframe", () => {
				self.show();
			})

			// State & Text management
			.on("mouseenter", "[data-cursor]", function () {
				self.setState(this.dataset.cursor);
			})
			.on("mouseleave", "[data-cursor]", function () {
				self.removeState(this.dataset.cursor);
			})
			.on("mouseenter", "[data-cursor-text]", function () {
				self.setText($(this).data("cursor-text"));
			})
			.on("mouseleave", "[data-cursor-text]", function () {
				self.removeText();
			})

			// Generic class support
			.on("mouseenter", "[data-cursor-class]", function () {
				const classes = $(this).data("cursor-class").split(" ");
				self.addClasses(classes);
			})
			.on("mouseleave", "[data-cursor-class]", function () {
				const classes = $(this).data("cursor-class").split(" ");
				self.removeClasses(classes);
			})

			// Legacy support for color-based classes
			.on("mouseenter", "[data-cursor-text-green]", function () {
				self.setText(this.dataset.cursorText);
				self.el.addClass("-green");
			})
			.on("mouseleave", "[data-cursor-text-green]", function () {
				self.removeText();
				self.el.removeClass("-green");
			})
			.on("mouseenter", "[data-cursor-text-red]", function () {
				self.setText(this.dataset.cursorText);
				self.el.addClass("-red");
			})
			.on("mouseleave", "[data-cursor-text-red]", function () {
				self.removeText();
				self.el.removeClass("-red");
			})
			.on("mouseenter", "[data-cursor-text-light]", function () {
				self.setText($(this).data("cursor-text-light"));
				self.el.addClass("light");
			})
			.on("mouseleave", "[data-cursor-text-light]", function () {
				self.removeText();
				self.el.removeClass("light");
			})
			.on("mouseenter", "[data-cursor-text-service]", function () {
				self.setText($(this).data("cursor-text-service"));
				self.el.addClass("service");
			})
			.on("mouseleave", "[data-cursor-text-service]", function () {
				self.removeText();
				self.el.removeClass("service");
			})
			.on("mouseenter", "[data-cursor-text-project]", function () {
				self.setText($(this).data("cursor-text-project"));
				self.el.addClass("project");
			})
			.on("mouseleave", "[data-cursor-text-project]", function () {
				self.removeText();
				self.el.removeClass("project");
			})
			.on("mouseenter", "[data-cursor-text-service-2]", function () {
				self.setText($(this).data("cursor-text-service-2"));
				self.el.addClass("service-2");
			})
			.on("mouseleave", "[data-cursor-text-service-2]", function () {
				self.removeText();
				self.el.removeClass("service-2");
			})
			.on("mouseenter", "[data-cursor-text-service-3]", function () {
				self.setText($(this).data("cursor-text-service-3"));
				self.el.addClass("service-3");
			})
			.on("mouseleave", "[data-cursor-text-service-3]", function () {
				self.removeText();
				self.el.removeClass("service-3");
			})
			.on("mouseenter", "[data-cursor-text-cta]", function () {
				self.setText($(this).data("cursor-text-cta"));
				self.el.addClass("cta");
			})
			.on("mouseleave", "[data-cursor-text-cta]", function () {
				self.removeText();
				self.el.removeClass("cta");
			})
			// Stick-to-element behavior
			.on("mouseenter", "[data-cursor-stick]", function () {
				self.setStick(this.dataset.cursorStick);
			})
			.on("mouseleave", "[data-cursor-stick]", function () {
				self.removeStick();
			});
	}

	setState(state) {
		this.el.addClass(state);
	}

	removeState(state) {
		this.el.removeClass(state);
	}

	addClasses(classes) {
		this.el.addClass(classes.join(" "));
	}

	removeClasses(classes) {
		this.el.removeClass(classes.join(" "));
	}

	setText(text) {
		this.text.html(text);
		this.el.addClass("-text");
	}

	removeText() {
		this.el.removeClass("-text");
	}

	setStick(el) {
		const target = $(el);
		const bound = target.get(0).getBoundingClientRect();
		this.stick = {
			y: bound.top + target.height() / 2,
			x: bound.left + target.width() / 2,
		};
		this.move(this.stick.x, this.stick.y, 5);
	}

	removeStick() {
		this.stick = false;
	}

	update() {
		this.move();
		this.show();
	}

	move(x, y, duration) {
		gsap.to(this.el, {
			x: x || this.pos.x,
			y: y || this.pos.y,
			force3D: true,
			overwrite: true,
			ease: this.options.ease,
			duration: this.visible ? duration || this.options.speed : 0,
		});
	}

	show() {
		if (this.visible) return;
		clearInterval(this.visibleInt);
		this.el.addClass("-visible");
		this.visibleInt = setTimeout(() => (this.visible = true));
	}

	hide() {
		clearInterval(this.visibleInt);
		this.el.removeClass("-visible");
		this.visibleInt = setTimeout(
			() => (this.visible = false),
			this.options.visibleTimeout,
		);
	}
}

// Init cursor
const cursor = new Cursor();
