// gsap-custom-animations.js
(function ($) {
	"use strict";

	gsap.registerPlugin(
		ScrollTrigger,
		ScrollSmoother,
		ScrollToPlugin,
		SplitText,
		CustomEase,
	);

	// match media
	let mediaMatch = gsap.matchMedia();

	// rtl
	function rtlValue(value) {
		const isRTL = document.documentElement.dir === "rtl";
		return isRTL ? -value : value;
	}

	// smooth scroll

	function initSmoothScrolling() {
		const device_width = window.screen.width;
		const hasSmoothElement = document.querySelector("#has_smooth");

		if (
			// device_width > 767 &&
			hasSmoothElement &&
			hasSmoothElement.classList.contains("has-smooth")
		) {
			// Create new smoother
			let smoother = ScrollSmoother.create({
				wrapper: "#smooth-wrapper",
				content: "#smooth-content",
				smooth: 1.2,
				effects: true,
				smoothTouch: 0.1,
				normalizeScroll: {
					allowNestedScroll: true,
				},
				ignoreMobileResize: true,
				// REMOVE onUpdate - causes performance issues
			});

			// CRITICAL: Make globally accessible
		}
	}

	window.GSAPAnimations = {
		scrollTriggers: [],
		timelines: [],

		init: function () {
			// Initialize smooth scrolling FIRST
			initSmoothScrolling();

			// Start animations immediately (no delay)
			this.fadeElements();
			this.parallaxImages();
			this.counterAnimation();
			this.titleAnimation();
			this.splitFlipText();
			this.h1HeroAnimation();
			this.h1HeroVideoAnimation();
			this.h1ProcessSlider();
			this.h1ProjectScroll();
			this.imageScalingAnimation();
			this.marqueeOpenAnimation();
			this.scrollSliderAnimation();
			this.awardAnimation();
			this.projectAnimation();
			this.sidebarStickyAnimation();
			this.windowShapeAnimation();
			this.h3AboutAnimation();
			this.sidebarStickyAnimationGlobal();
			this.h3TeamAnimation();
			this.h3TestimonalsAnim();
			this.tjPullUpAnimation();
			this.imageGalleryAnimation();
			this.perspectiveShowcase();
			this.stickyPanelAnimation();
			this.onpageNavAnimation();
			this.scrollProgressAnimation();
			this.h6HeroStarAnimation();
		},

		// Fade Elements
		fadeElements: function () {
			const fadeInEls = gsap.utils.toArray(".fade-in");
			if (fadeInEls.length) {
				fadeInEls.forEach(el => {
					gsap.set(el, { opacity: 0, visibility: "hidden" });

					const anim = gsap.to(el, {
						opacity: 1,
						duration: 2,
						ease: "power2.out",
						visibility: "visible",
						scrollTrigger: {
							trigger: el,
							start: "top 85%",
							once: true,
							// invalidateOnRefresh: true,
						},
					});

					// Track ScrollTrigger
					if (anim.scrollTrigger) {
						this.scrollTriggers.push(anim.scrollTrigger);
					}
				});
			}

			// fade animations
			const fadeItems = gsap.utils.toArray(".tj-fade");
			if (fadeItems.length) {
				fadeItems.forEach(item => {
					let onscrollValue = item.getAttribute("data-on-scroll") || 1,
						fadeOffset = item.getAttribute("data-offset") || 50,
						delayValue = item.getAttribute("data-delay") || 0.15,
						durationValue = item.getAttribute("data-duration") || 1.15,
						fadeDirection = item.getAttribute("data-direction") || "bottom",
						easeValue = item.getAttribute("data-ease") || "power2.out",
						animationSetting = {
							autoAlpha: 0,
							ease: easeValue,
							duration: durationValue,
							delay: delayValue,
							x:
								fadeDirection == "left"
									? -fadeOffset
									: fadeDirection == "right"
										? fadeOffset
										: 0,
							y:
								fadeDirection == "top"
									? -fadeOffset
									: fadeDirection == "bottom"
										? fadeOffset
										: 0,
						};
					if (onscrollValue == 1) {
						animationSetting.scrollTrigger = {
							trigger: item,
							start: `top bottom`,
							// invalidateOnRefresh: true,
						};
					}
					gsap.set(item, { autoAlpha: 1 });
					gsap.from(item, animationSetting);
				});
			}
		},

		// image parallax
		parallaxImages: function () {
			const images = gsap.utils.toArray(".parallax-image");
			if (!images.length) return;

			images.forEach(image => {
				const speed = image.dataset.move || 0.5;
				const anim = gsap.to(image, {
					y: () => -100 * speed,
					ease: "none",
					scrollTrigger: {
						trigger: image,
						start: "top bottom",
						end: "bottom top",
						scrub: 1,
					},
				});

				if (anim.scrollTrigger) {
					this.scrollTriggers.push(anim.scrollTrigger);
				}
			});
		},

		// counter
		counterAnimation: function () {
			const counters = document.querySelectorAll(".counter");
			if (!counters.length) return;

			counters.forEach(counter => {
				const start = parseFloat(counter.dataset.start ?? 0);
				const rawTarget = String(counter.dataset.target);
				const targetStr = rawTarget.replace(/,/g, "");
				const target = parseFloat(targetStr);
				const duration = parseFloat(counter.dataset.duration || 2);
				const delay = parseFloat(counter.dataset.delay || 0.4);
				const postfix = counter.dataset.postfix || "";

				// set initial value
				counter.innerText = start;

				const trigger = ScrollTrigger.create({
					trigger: counter,
					start: "top 85%",
					once: true,
					onEnter: () => {
						gsap.fromTo(
							counter,
							{ innerText: start },
							{
								innerText: target,
								duration,
								delay,
								ease: "power4.out",
								snap: { innerText: 0.1 },
								onUpdate: () => {
									let value = parseFloat(counter.innerText);

									// dynamic formatting
									let formattedValue;

									if (rawTarget.includes(",") || target >= 1000) {
										// comma formatting
										formattedValue = Math.round(value)
											.toString()
											.replace(/\B(?=(\d{3})+(?!\d))/g, ",");
									} else if (/^0\d/.test(rawTarget)) {
										// leading zero
										formattedValue = Math.round(value)
											.toString()
											.padStart(2, "0");
									} else if (target % 1 !== 0) {
										// decimal point
										formattedValue = value.toFixed(1);
									} else if (target < 10) {
										// single digit
										formattedValue = Math.round(value).toString();
									} else {
										// default rounding
										formattedValue = Math.round(value).toString();
									}

									// append postfix dynamically
									if (formattedValue !== "NaN") {
										counter.innerText = formattedValue + postfix;
									}
								},
							},
						);
					},
				});

				if (!this.scrollTriggers) this.scrollTriggers = [];
				this.scrollTriggers.push(trigger);
			});
		},

		// title
		titleAnimation: function () {
			// title animation
			$(".tj-chars-up").each(function () {
				const element = this;

				// Split into chars
				const split = new SplitText(element, {
					type: "chars,lines",
					charsClass: "char",
					linesClass: "line",
				});

				const tl = gsap.timeline({
					scrollTrigger: {
						trigger: element,
						start: "top 85%",
						toggleActions: "play none none none",
					},
				});

				tl.from(split.chars, {
					yPercent: 100,
					duration: 0.3,
					ease: "power1.out",
					stagger: { amount: 0.6 },
				});
			});

			// title animation 2
			document.querySelectorAll(".tj-line-up").forEach(el => {
				const childSplit = new SplitText(el, {
					type: "lines",
					linesClass: "line-inner",
				});

				new SplitText(el, {
					type: "lines",
					linesClass: "line",
				});

				gsap.from(childSplit.lines, {
					yPercent: 100,
					duration: 1,
					ease: "power4.out",
					stagger: 0.2,
					scrollTrigger: {
						trigger: el,
						start: "top 85%",
						toggleActions: "play none none none",
					},
				});
			});

			// title animation 3
			if ($(".tj-split-text").length) {
				let staggerAmount = 0.02,
					translateXValue = 30,
					delayValue = 0.3,
					easeType = "power2.out",
					animatedTextElements = document.querySelectorAll(".tj-split-text");

				animatedTextElements.forEach(element => {
					let animationSplitText = new SplitText(element, {
						type: "chars, words",
						charsClass: "char",
					});
					gsap.from(animationSplitText.chars, {
						duration: 1,
						delay: delayValue,
						x: translateXValue,
						autoAlpha: 0,
						stagger: staggerAmount,
						ease: easeType,
						scrollTrigger: { trigger: element, start: "top 85%" },
					});
				});
			}

			// text animation
			$(".words-fade-in-random").each(function () {
				const split = new SplitText(this, { type: "words" });

				gsap.from(split.words, {
					scrollTrigger: {
						trigger: this,
						start: "top 80%",
						once: true,
					},
					opacity: 0,
					duration: 0.5,
					ease: "power1.out",
					stagger: { amount: 0.9, from: "random" },
				});
			});

			// char reveal from left
			$(".tj-char-reveal-left").each(function () {
				const split = new SplitText(this, { type: "chars" });

				gsap.from(split.chars, {
					scrollTrigger: {
						trigger: this,
						start: "top 90%",
						once: true,
					},
					x: -100,
					opacity: 0,
					delay: 0.7,
					duration: 0.8,
					ease: "power2.out",
					stagger: { amount: 0.5, from: "left" },
				});
			});

			// highlight text
			const highlightText = gsap.utils.toArray(".title-highlight");
			if (highlightText.length) {
				highlightText.forEach(el => {
					const split = new SplitText(el, {
						type: "lines",
						linesClass: "line",
					});

					const lines = split.lines;

					const tl = gsap.timeline({
						scrollTrigger: {
							trigger: el,
							start: "top 80%",
							end: "top 20%",
							scrub: true,
							// markers: true,
						},
					});

					// ONE line at a time (no stagger overlap)
					lines.forEach(line => {
						tl.to(line, {
							"--highlight-offset": "100%",
							ease: "power2.out",
							duration: 1, // per-line duration
							stagger: 0.4,
						});
					});
				});
			}

			// reveal line
			const revealLink = gsap.utils.toArray(".tj-reveal-line");
			if (revealLink.length) {
				revealLink.forEach(el => {
					const split = new SplitText(el, {
						type: "lines",
						linesClass: "reveal-line",
					});

					// wrap each line
					split.lines.forEach(line => {
						const wrapper = document.createElement("div");
						wrapper.classList.add("tj-reveal-line__wrap");
						wrapper.style.overflow = "hidden";

						line.parentNode.insertBefore(wrapper, line);
						wrapper.appendChild(line);
					});

					// initial state
					gsap.set(split.lines, {
						yPercent: 100,
						autoAlpha: 0,
					});

					// animate (ALL lines together)
					gsap
						.timeline({
							scrollTrigger: {
								trigger: el,
								start: "top 85%",
								toggleActions: "play none none none",
								// markers: true,
							},
						})
						.to(split.lines, {
							yPercent: 0,
							autoAlpha: 1,
							duration: 1.4,
							ease: "power3.out",
							stagger: 0, // all lines together
						});
				});
			}

			// random char blink
			const randomCharBlink = gsap.utils.toArray(".tj-random-char-blink");
			if (randomCharBlink.length) {
				randomCharBlink.forEach(text => {
					// Split text into characters
					const split = new SplitText(text, {
						type: "chars",
						charsClass: "char",
					});

					const chars = split.chars;
					let tl;

					text.addEventListener("mouseenter", () => {
						tl?.kill();

						tl = gsap.timeline();

						tl.fromTo(
							chars,
							{ opacity: 0.2, willChange: "opacity" },
							{
								opacity: 1,
								duration: 0.25,
								stagger: {
									each: 0.025,
									from: "random",
								},
							},
						);
					});

					text.addEventListener("mouseleave", () => {
						tl?.kill();
						gsap.set(chars, { opacity: 1, willChange: "opacity" });
					});
				});
			}
		},

		// Split Flip Text
		splitFlipText: function () {
			const splitFlipTextWrappers = document.querySelectorAll(
				".split-flip-text-wrap",
			);
			if (splitFlipTextWrappers?.length) {
				splitFlipTextWrappers.forEach(textWrap => {
					const chars = textWrap.querySelectorAll(".char-wrap");

					// Store active tweens per button
					let hoverTweens = [];

					// Set initial state
					chars.forEach(char => {
						const front = char.children[0];
						const back = char.children[1];

						gsap.set(front, { rotationX: 0 });
						gsap.set(back, { rotationX: 90 });
					});

					textWrap.addEventListener("mouseenter", () => {
						// Kill previous animations
						hoverTweens.forEach(t => t.kill());
						hoverTweens = [];

						chars.forEach((char, i) => {
							const front = char.children[0];
							const back = char.children[1];

							hoverTweens.push(
								gsap.to(front, {
									rotationX: 90,
									duration: 0.1,
									delay: i * 0.05,
									ease: "power2.out",
								}),
							);
							hoverTweens.push(
								gsap.to(back, {
									rotationX: 0,
									duration: 0.1,
									delay: i * 0.05 + 0.15,
									ease: "power2.out",
								}),
							);
						});
					});

					textWrap.addEventListener("mouseleave", () => {
						// Kill previous animations
						hoverTweens.forEach(t => t.kill());
						hoverTweens = [];

						chars.forEach(char => {
							const front = char.children[0];
							const back = char.children[1];

							hoverTweens.push(
								gsap.to(front, {
									rotationX: 0,
									duration: 0.3,
									ease: "none",
								}),
							);

							hoverTweens.push(
								gsap.to(back, {
									rotationX: 90,
									duration: 0.1,
									ease: "none",
								}),
							);
						});
					});
				});
			}
		},

		// H1 hero animation
		h1HeroAnimation: function () {
			const h1Hero = document.querySelector(".h1-hero-section.h1HeroAnimation");
			const h1HeroVideo = document.querySelector(
				".h1-video-section.h1HeroAnimation",
			);
			if (h1Hero) {
				h1Hero.classList.add("activeAnimation");
				h1HeroVideo.classList.add("activeAnimation");
			} else {
				return;
			}

			let h1HeroAnimation = document.querySelector(
				".h1HeroAnimation.activeAnimation",
			);
			let h1HeroVideoAnimation = document.querySelector(
				".h1-video-section.activeAnimation",
			);

			const heroTitle = h1HeroAnimation.querySelector(".hero_title");
			const heroTitleArrow = h1HeroAnimation.querySelector(".hero_title i");
			const heroShape = h1HeroAnimation.querySelector(
				".h1_hero_shape_animation",
			);
			const heroLeftContent = h1HeroAnimation.querySelector(
				".bottom_left_content",
			);
			const heroVideo = h1HeroVideoAnimation.querySelector(".h1_hero_video");
			const heroScrollDown = h1HeroAnimation.querySelector(".tj_scroll_down");

			const h1HeroTl = gsap.timeline({
				defaults: { ease: "power2.out" },
			});

			// Hero title
			if (heroTitle) {
				const itemSplitted = new SplitText(heroTitle, {
					type: "lines",
					linesClass: "hero-line",
				});

				// Wrap each line in overflow container
				itemSplitted.lines.forEach(line => {
					const wrapper = document.createElement("div");
					wrapper.style.overflow = "hidden";
					line.parentNode.insertBefore(wrapper, line);
					wrapper.appendChild(line);
				});

				// Set initial state
				gsap.set(itemSplitted.lines, { yPercent: 100, autoAlpha: 0 });

				// Animate all lines together
				h1HeroTl.to(
					itemSplitted.lines,
					{
						yPercent: 0,
						autoAlpha: 1,
						duration: 1.4,
						ease: "power3.out",
						stagger: 0, // All lines appear together
					},
					0,
				);
			}

			// Title arrow
			if (heroTitleArrow) {
				h1HeroTl.from(
					heroTitleArrow,
					{
						x: -20,
						autoAlpha: 0,
						duration: 0.5,
					},
					"-=0.4",
				);
			}

			// Shape
			if (heroShape) {
				h1HeroTl.from(
					heroShape,
					{
						scale: 0,
						autoAlpha: 0,
						duration: 0.6,
					},
					"<",
				);
			}

			// Left content
			if (heroLeftContent) {
				h1HeroTl.from(
					heroLeftContent,
					{
						x: -100,
						autoAlpha: 0,
						duration: 0.6,
					},
					"<",
				);
			}

			// Video
			if (heroVideo) {
				h1HeroTl.from(
					heroVideo,
					{
						x: 100,
						autoAlpha: 0,
						duration: 0.6,
					},
					"<",
				);
			}

			// Scroll down
			if (heroScrollDown) {
				h1HeroTl.from(heroScrollDown, {
					y: -50,
					autoAlpha: 0,
					duration: 0.6,
				});
			}

			this.timelines.push(h1HeroTl);
		},

		// H1 hero video animation
		h1HeroVideoAnimation: function () {
			const h1VideoSection = document.querySelector(".h1-video-section");
			if (!h1VideoSection) return;

			mediaMatch.add("(min-width: 992px)", () => {
				const videoWrap = h1VideoSection.querySelector(".h1_hero_video_wrap");
				const video = h1VideoSection.querySelector(".h1_hero_video");

				let videoWidthTrigger, videoOpacityTrigger;

				function initVideoAnimations() {
					// Kill previous triggers for this section only
					if (videoWidthTrigger) videoWidthTrigger.kill();
					if (videoOpacityTrigger) videoOpacityTrigger.kill();

					gsap.set(video, {
						// width: "450px",
						willChange: "transform, opacity, width, height",
					});

					// Video width animation
					videoWidthTrigger = gsap.to(video, {
						width: "100%",
						ease: "none",
						scrollTrigger: {
							trigger: videoWrap,
							start: "bottom bottom",
							end: "+=100%",
							pin: true,
							pinSpacing: false,
							pinReparent: true,
							scrub: 1.5,
							// markers: true,
						},
					}).scrollTrigger;

					// Section opacity animation
					videoOpacityTrigger = gsap.to(h1VideoSection, {
						// opacity: 0,
						ease: "none",
						scrollTrigger: {
							trigger: h1VideoSection,
							start: "bottom bottom",
							end: () => `+=${h1VideoSection.offsetHeight}`,
							scrub: 2,
							pin: true,
							pinSpacing: false,
							// markers: true,
						},
					}).scrollTrigger;
				}

				// Initialize
				initVideoAnimations();

				// Re-init on window resize
				window.addEventListener("resize", () => {
					initVideoAnimations();
				});
			});
		},

		// H1 Process Slider
		h1ProcessSlider: function () {
			const h1ProcessWrap = document.querySelector(".h1_process_wrap");
			if (!h1ProcessWrap) return;

			mediaMatch.add(
				{
					isDesktop: "(min-width: 992px)",
					lg: "(min-width: 992px) and (max-width: 1199px)",
				},
				screen => {
					const { isDesktop, lg } = screen?.conditions;

					const topSpace = lg ? "75" : "100";

					if (isDesktop) {
						const processItems = gsap.utils.toArray(
							".h1_process_items .tj_process_item_2",
						);
						const processImages = gsap.utils.toArray(
							".h1_process_images .tj_process_img",
						);
						const processNo = gsap.utils.toArray(".h1_process_no > span");

						let totalProcessItems = processItems.length;

						processItems.forEach((item, index) => {
							gsap.set(item, {
								position: "absolute",
								width: "100%",
								left: 0,
								top: 0,
								y: index === 0 ? 0 : "200%", // first item visible
								opacity: index === 0 ? 1 : 0,
							});
						});

						processImages.forEach((image, index) => {
							gsap.set(image, {
								position: "absolute",
								width: "100%",
								left: 0,
								top: 0,
								x: index === 0 ? 0 : "100%", // first item visible
							});
						});

						processNo.forEach((no, index) => {
							gsap.set(no, {
								position: "absolute",
								width: "auto",
								left: 0,
								top: 0,
								y: index === 0 ? 0 : "100%", // first item visible
							});
						});

						ScrollTrigger.create({
							trigger: h1ProcessWrap,
							start: "top top",
							end: "bottom top",
							pin: true,
							anticipatePin: 1,
							// markers: true,

							onUpdate: self => {
								let progress = self.progress;
								let progressModified = progress * (totalProcessItems - 1);
								let activeIndex = Math.round(progressModified);

								processItems.forEach((item, index) => {
									item.style.top = `${topSpace * index}px`;
									const innerElems = item.querySelectorAll(
										".process_desc, .process_bottom",
									);

									if (index < activeIndex) {
										gsap.to(item, {
											y: 0,
											opacity: `${0.25 * (index + 1)}`,
											duration: 0.4,
											ease: "power1.out",
										});
										gsap.to(innerElems, { opacity: 0, duration: 0.6 });
									} else if (index === activeIndex) {
										gsap.to(item, {
											y: 0,
											opacity: 1,
											duration: 0.4,
											ease: "power1.out",
										});
										gsap.to(innerElems, { opacity: 1, duration: 0.5 });
									} else {
										gsap.to(item, {
											y: "200%",
											opacity: 0,
											duration: 0.4,
											ease: "power1.out",
										});
										gsap.to(innerElems, { opacity: 1, duration: 0.5 });
									}
								});

								processImages.forEach((image, index) => {
									if (index < activeIndex) {
										gsap.to(image, { x: 0, duration: 0.4, ease: "power1.out" });
									} else if (index === activeIndex) {
										gsap.to(image, { x: 0, duration: 0.4, ease: "power1.out" });
									} else {
										gsap.to(image, {
											x: "100%",
											duration: 0.4,
											ease: "power1.out",
										});
									}
								});

								processNo.forEach((no, index) => {
									if (index < activeIndex) {
										gsap.to(no, {
											y: "-100%",
											duration: 0.4,
											ease: "power1.out",
										});
									} else if (index === activeIndex) {
										gsap.to(no, { y: 0, duration: 0.4, ease: "power1.out" });
									} else {
										gsap.to(no, {
											y: "100%",
											duration: 0.4,
											ease: "power1.out",
										});
									}
								});
							},
						});
					}
				},
			);
		},

		// H1 Project Horizontal Scroll
		h1ProjectScroll: function () {
			mediaMatch.add("(min-width: 768px)", () => {
				const h1ProjectsWrap = document.querySelector(".h1_projects_wrap");
				if (!h1ProjectsWrap) return;

				mediaMatch.add("(min-width: 992px)", () => {
					const track = h1ProjectsWrap.querySelector(".h1_projects_items");
					const bar = h1ProjectsWrap.querySelector(".projects-progress span");

					if (!h1ProjectsWrap || !track || !bar) return;

					const getMaxScroll = () =>
						track.scrollWidth - h1ProjectsWrap.clientWidth;

					// horizontal scroll
					gsap.to(track, {
						x: () => -getMaxScroll(),
						ease: "none",
						scrollTrigger: {
							trigger: h1ProjectsWrap,
							start: "top top",
							end: () => "+=" + getMaxScroll(),
							pin: true,
							scrub: 1,
							invalidateOnRefresh: true,
						},
					});

					// progress bar
					gsap.to(bar, {
						scaleX: 1,
						ease: "none",
						scrollTrigger: {
							trigger: h1ProjectsWrap,
							start: "top top",
							end: () => "+=" + getMaxScroll(),
							scrub: 0.3,
							invalidateOnRefresh: true,
						},
					});
				});
			});
		},

		// H2 Animate the image scaling to fullscreen
		imageScalingAnimation: function () {
			if ($(".h2-about-content-wrap").length > 0) {
				mediaMatch.add("(min-width: 1200px)", () => {
					gsap.set(".video-btn", {
						opacity: 0,
						scale: 0.7,
						pointerEvents: "none",
					});
					gsap.to(".image-wrapper", {
						width: "780px",
						ease: "none",
						scrollTrigger: {
							trigger: ".h2-about-content-wrap",
							start: "top top",
							end: "+=30%",
							scrub: true,
							pin: true,
							onUpdate: self => {
								gsap.set(".video-btn", {
									opacity: self.progress >= 0.5 ? 1 : 0,
									scale: self.progress >= 0.5 ? 1 : 0.7,
									pointerEvents: self.progress >= 0.5 ? "auto" : "none",
								});
							},
						},
					});
				});
			}

			if (document.querySelectorAll(".img-reveal").length > 0) {
				let imgReveal = document.querySelectorAll(".img-reveal");
				imgReveal.forEach(element => {
					const image = element.querySelector("img");

					gsap.set(element, { autoAlpha: 1 });

					gsap.fromTo(
						image,
						{ scale: 1.3 },
						{
							scale: 1,
							duration: 3,
							ease: "power4.out",
							scrollTrigger: {
								trigger: element,
								start: "top 40%",
							},
						},
					);
				});
			}
		},

		// H2 marquee slider
		marqueeOpenAnimation: function () {
			mediaMatch.add("(min-width: 992px)", () => {
				gsap.utils
					.toArray(".h2-marquee-slider:not(:first-child)")
					.forEach(slider => {
						gsap.fromTo(
							slider,
							{
								marginTop: -128,
							},
							{
								marginTop: 0,
								ease: "none",
								scrollTrigger: {
									trigger: slider,
									start: "top 75%",
									end: "top 45%",
									scrub: 1,
								},
							},
						);
					});
			});

			// testimonial move horizontal on scroll
			if ($(".h2_testimonial_item").length > 0) {
				document.querySelectorAll(".h2_testimonial_item").forEach(item => {
					const moveX = item.offsetWidth * 0.8;

					gsap.to(item, {
						x: -moveX,
						ease: "none",
						scrollTrigger: {
							trigger: item,
							start: "top bottom",
							end: "bottom top",
							scrub: 0.2,
							invalidateOnRefresh: true,
						},
					});
				});
			}
		},

		// H2 scroll slider
		scrollSliderAnimation: function () {
			if ($(".tj-scroll-slider").length > 0) {
				mediaMatch.add("(min-width: 992px)", () => {
					const slider = document.querySelector(".tj-scroll-slider");
					if (!slider) return;

					const panels = gsap.utils.toArray(".tj-scroll-slider-item");
					const startOffset = window.innerWidth * 0.61; // visual start
					const endGap = 0;
					const speedFactor = 4; // slower

					// initial visual offset
					gsap.set(slider, { x: startOffset });

					// shared end distance
					const getEnd = () => {
						const base = slider.scrollWidth - slider.clientWidth + endGap;
						return "+=" + base * speedFactor;
					};

					// HORIZONTAL SCROLL
					gsap.to(panels, {
						xPercent: rtlValue(-100) * (panels.length - 1),
						ease: "none",
						force3D: true,
						scrollTrigger: {
							trigger: slider,
							start: "top top",
							end: getEnd,
							scrub: 1,
							pin: true,
							pinSpacing: true,
						},
					});
				});
			}
		},

		// H2 Award animation
		awardAnimation: function () {
			if (document.querySelectorAll(".tj-reveal-right").length > 0) {
				const items = document.querySelectorAll(".tj-reveal-right");

				gsap.from(items, {
					x: "110%",
					duration: 1,
					stagger: 0.2,
					ease: "power2.out",
					scrollTrigger: {
						scrub: 2,
						trigger: ".tj-reveal-right-wrap",
						start: "top 100%",
						end: "bottom 70%",
						toggleActions: "play none none reverse",
					},
				});
			}
		},

		// H2 project animation
		projectAnimation: function () {
			// =======================
			if ($(".h2_projects_item").length > 0) {
				const container = document.querySelector(".h2_projects_wrap");
				const items = container.querySelectorAll(".h2_projects_item");
				if (items.length) {
					window.addEventListener("resize", () => {
						const animationEndPosition = items[0].offsetHeight * items.length;
						container.style.minHeight = `${animationEndPosition}px`;
					});
					items.forEach((item, index) => {
						if (index > 0) {
							ScrollTrigger.create({
								trigger: item,
								start: "top 50%",
								onEnter: () => item.classList.add("active"),
								onLeaveBack: () => item.classList.remove("active"),
								toggleActions: "play none none none",
							});
						}
					});
				}
				setTimeout(() => {
					items.forEach((item, index) => {
						if (index > 0) {
							item.classList.remove("active");
						}
					});
				}, 50);
			}

			if ($(".h2-project-section").length > 0) {
				ScrollTrigger.create({
					trigger: ".h2-project-section",
					start: "top 70%",
					once: true,
					onEnter: () => {
						document
							.querySelectorAll(".blocks-shape svg path")
							.forEach(path => {
								path.style.animationPlayState = "running";
							});
					},
				});
			}
		},

		// H2 sidebar sticky
		sidebarStickyAnimation: function () {
			if ($(".tj-sticky-wrapper").length > 0) {
				mediaMatch.add("(min-width: 1200px)", medias => {
					const wrap = document.querySelector(".tj-sticky-wrapper");
					const left = document.querySelector(".tj-sticky-left");
					const right = document.querySelector(".tj-sticky-right");

					if (wrap && left && right) {
						const getScrollAmount = () =>
							right.scrollHeight - left.offsetHeight;

						gsap.to(right, {
							y: () => -getScrollAmount(),
							ease: "none",
							scrollTrigger: {
								trigger: wrap,
								start: `top-=70 top`,
								end: () => `+=${getScrollAmount()}`,
								scrub: true,
								pin: true,
								anticipatePin: 1,
								invalidateOnRefresh: true,
							},
						});
					}
				});
			}
		},

		// H3 Hero
		windowShapeAnimation: function () {
			if ($(".window-shape-wrapper").length > 0) {
				if (typeof WindowShapeAnim !== "undefined") {
					new WindowShapeAnim();
				} else {
					new window.WindowShapeAnim();
				}
			}
		},

		// H3 About
		h3AboutAnimation: function () {
			if ($(".title-highlight-2").length > 0) {
				const highlightText = gsap.utils.toArray(".title-highlight-2");
				highlightText.forEach(el => {
					const split = new SplitText(el, {
						type: "lines",
						linesClass: "line-2",
					});
					const lines = split.lines;
					const tl = gsap.timeline({
						scrollTrigger: {
							trigger: el,
							start: "top 80%",
							end: "top 20%",
							scrub: true,
						},
					});

					// ONE line at a time (no stagger overlap)
					lines.forEach(line => {
						tl.to(line, {
							"--highlight-offset": "100%",
							ease: "power2.out",
							duration: 1, // per-line duration
							stagger: 0.4,
						});
					});

					mediaMatch.add(
						{
							isDesktop: "(min-width: 992px)",
							isMobile: "(max-width: 991px)",
							xxl: "(min-width: 1400px) and (max-width: 1599px)",
							xl: "(min-width: 1200px) and (max-width: 1399px)",
							lg: "(min-width: 992px) and (max-width: 1299px)",
						},
						medias => {
							const { isDesktop, isMobile, xxl, xl, lg } = medias?.conditions;
							const image = el.querySelector(".title-img-anim");
							const imageWidth = `${xxl || xl ? 154 : lg ? 134 : isMobile ? 80 : 194}px`;
							gsap.set(image, { width: 0 });
							tl.to(
								image,
								{
									width: imageWidth,
								},
								".8",
							);
						},
					);
				});
			}
		},

		// Sidebar Sticky
		sidebarStickyAnimationGlobal: function () {
			function sidebarStickyController() {
				const containers = document.querySelectorAll(
					".sidebar-sticky-container",
				);
				if (!containers.length) return;

				containers.forEach(container => {
					// ✅ SAFETY CHECK (THIS FIXES YOUR ERROR)
					if (!container || container.nodeType !== 1) return;

					const panels = container.querySelectorAll(".sidebar-sticky");
					if (!panels.length) return;

					mediaMatch.add("(min-width: 992px)", () => {
						const startOffset = 60;
						const lastPanel = panels[panels.length - 1];

						// ✅ SAFE getComputedStyle
						const styles = getComputedStyle(container);
						const paddingBottom = parseInt(styles.paddingBottom) || 0;

						panels.forEach(panel => {
							gsap.to(panel, {
								ease: "circ",
								scrollTrigger: {
									trigger: panel,
									start: `top-=${startOffset} top`,
									endTrigger: container,
									end: () =>
										`bottom top+=${
											lastPanel.offsetHeight + startOffset + paddingBottom
										}`,
									pin: true,
									pinSpacing: false,
									scrub: true,
									invalidateOnRefresh: true,
								},
							});
						});
					});
				});
			}
			sidebarStickyController();
		},

		// h3 Team Animation
		h3TeamAnimation: function () {
			let animItem = document.querySelector(".team_pin_area_container");
			if (animItem) {
				mediaMatch.add("(min-width: 992px)", () => {
					gsap
						.timeline({
							defaults: {},
							scrollTrigger: {
								trigger: ".team_pin_area_container",
								pin: ".team_pin_area",
								scrub: 1,
								start: "top top",
								end: "bottom bottom",
								endTrigger: ".team_pin_area_container",
								pinSpacing: false,
								markers: false,
							},
						})
						.to(
							".team_pin_area .section_heading",
							{
								yPercent: 35,
								ease: "power2.out",
								scale: 1.1,
								duration: 4,
							},
							"0",
						)
						.to(
							".team_pin_area_container .team_items_wrapper",
							{
								y: "-100vh",
								duration: 4,
							},
							"0",
						)
						.from(
							".team_pin_area .team_badge img",
							{
								opacity: 0,
								scale: 0.7,
								rotate: 45,
								duration: 4,
							},
							"0",
						);
				});
			}
		},

		// h3 Testimonilas & Marquee Animation
		h3TestimonalsAnim: function () {
			if ($(".section-parallax").length > 0) {
				let animaItem = document.querySelector(".section-parallax");
				let animTerget = document.querySelector(".section-parallax-target");
				if (animaItem && animTerget) {
					mediaMatch.add("(min-width: 992px)", () => {
						gsap.set(animaItem, { yPercent: -30 });
						gsap.to(
							animaItem,
							{
								yPercent: 0,
								scrollTrigger: {
									trigger: ".section-parallax-target",
									scrub: 1,
									start: "top-=20% top",
									end: "bottom top",
									markers: false,
									invalidateOnRefresh: true,
								},
							},
							0,
						);
					});
				}
			}
		},

		// Pull Up Animation
		tjPullUpAnimation: function () {
			let animItemContainers = document.querySelectorAll(
				".tj-pull-up-container",
			);
			if (animItemContainers?.length) {
				mediaMatch.add("(min-width: 992px)", () => {
					animItemContainers?.forEach((animItemContainer, idx) => {
						const animItem = animItemContainer?.querySelector(".tj-pull-up");
						gsap.to(
							animItem,
							{
								yPercent: -100,
								scrollTrigger: {
									trigger: animItemContainer,
									scrub: 1,
									start: "top top",
									end: "top+=50% top",
									markers: false,
								},
							},
							0,
						);
					});
				});
			}
		},

		// h3 Project Animation
		imageGalleryAnimation: function () {
			let galleryItems = document.querySelectorAll(".tj-project-gallery-item");

			if (galleryItems?.length) {
				mediaMatch.add("(min-width: 992px)", () => {});
				galleryItems.forEach((galleryItem, idx) => {
					const galleryItemInner = galleryItem.querySelector(
						".tj-project-gallery-item-inner",
					);
					gsap.set(galleryItemInner, { width: 0 });
					const currentWidth = "100%";
					gsap.to(galleryItemInner, {
						width: currentWidth,
						duration: 1,
						ease: "power2.out",
						scrollTrigger: {
							trigger: galleryItem,
							start: "top 80%",
							end: "bottom bottom",
						},
					});
				});
			}
		},

		// Perspective showcase Animation
		perspectiveShowcase: function () {
			mediaMatch.add("(min-width: 1200px)", () => {
				const section = document.querySelector(".perspective-showcase-section");
				const imagesWrap = document.querySelector(
					".perspective-showcase-images",
				);
				const items = gsap.utils.toArray(".perspective-showcase-item");
				const images = gsap.utils.toArray(".perspective-showcase-img");
				const titlesWrapContainer = gsap.utils.toArray(
					".perspective-showcase-title-wrap",
				);
				const titlesWrap = gsap.utils.toArray(".perspective-title-wrap");
				const titles = gsap.utils.toArray(".perspective-title");
				const subtitles = gsap.utils.toArray(
					".perspective-showcase-subtitle .sec_subtitle",
				);
				const years = gsap.utils.toArray(
					".perspective-showcase-year .sec_subtitle",
				);
				const currentNo = document.querySelector(".current-item-no");

				if (section && images.length) {
					const total = images.length;
					const gap = 30;
					const itemH = images[0].offsetHeight + gap;
					const segment = window.innerHeight;
					const scrollDist = segment * (total - 1);
					// The vertical center of the title container (where active title lives)
					const containerH = window.innerHeight;
					const centerY = containerH / 2;

					ScrollTrigger.create({
						trigger: section,
						start: "top top",
						end: "+=" + scrollDist,
						pin: true,
						scrub: true,
						onUpdate: self => {
							let progress = self.progress;
							let progressModified = progress * (total - 1);
							let activeIndex = Math.round(progressModified);
							// Move images
							gsap.set(imagesWrap, { y: -progressModified * itemH });
							// Update active index
							setActive(activeIndex);
							const distination =
								titlesWrapContainer[0].getBoundingClientRect().height -
								185 -
								titlesWrap[0].getBoundingClientRect().height;

							titles.forEach((el, idx) => {
								if (idx === activeIndex) {
									el.style.transform = `translateY(-${distination}px)`;
								}
								if (idx > activeIndex) {
									el.style.transform = `translateY(0)`;
								}
							});
						},
					});

					function setActive(activeIndex) {
						items.forEach((el, index) =>
							el.classList.toggle("active", index === activeIndex),
						);
						subtitles.forEach((el, index) =>
							el.classList.toggle("active", index === activeIndex),
						);
						years.forEach((el, index) =>
							el.classList.toggle("active", index === activeIndex),
						);
						titles.forEach((el, index) =>
							el.classList.toggle("active", index === activeIndex),
						);

						currentNo.textContent = String(activeIndex + 1).padStart(2, "0");
					}
					setActive(0);
				}
			});
		},
		// sticky Pannels
		stickyPanelAnimation: function () {
			const container = document.querySelector(".tj-sticky-panel-container");
			const panels = document.querySelectorAll(".tj-sticky-panel");
			if (!container || panels.length === 0) return;
			mediaMatch.add("(min-width: 992px)", () => {
				const startOffset = 60;
				const stackDifference = 0;
				const lastIdx = panels.length - 1;
				const lastPanel = panels[lastIdx];
				const paddingBottom =
					parseInt(getComputedStyle(container).paddingBottom, 10) || 0;
				panels.forEach((panel, i) => {
					const extraStartingOffset =
						i === 0 || i === 1 ? 0 : (i - 1) * stackDifference;
					if (i === 0) {
						gsap.to(panel, {
							y: 140,
							scrollTrigger: {
								trigger: panel,
								start: `top-=${startOffset + extraStartingOffset} top`,
								endTrigger: container,
								pin: true,
								pinSpacing: false,
								scrub: true,
								markers: false,
								invalidateOnRefresh: true,
								end: () =>
									`bottom top+=${
										lastPanel.offsetHeight +
										startOffset +
										paddingBottom +
										(lastIdx - 1) * stackDifference
									}`,
							},
							ease: "circ",
						});
					}
				});
			});
		},
		// onepage nav
		onpageNavAnimation: function () {
			// Onepage navigation
			document.addEventListener("click", function (e) {
				const btn = e.target.closest(
					".tj-scroll-btn, .has-onepage-nav .site_navigation nav ul:not(.sub-menu) > li > a, .has-onepage-nav .offcanvas_menu .mean-nav ul:not(.sub-menu) > li > a:not(.mean-expand)",
				);
				if (!btn) return;
				e.preventDefault();
				let sectionTarget =
					btn.getAttribute("href") || btn.getAttribute("data-target");
				gsap.to(window, {
					duration: 0.3,
					scrollTo: { y: sectionTarget, offsetY: 0 },
				});
			});
		},
		// Scroll Progress Animation
		scrollProgressAnimation: function () {
			if ($(".tj-progress-item-2").length > 0) {
				let mediaMatch = gsap.matchMedia();
				mediaMatch.add("(min-width: 992px)", () => {
					const slider = document.querySelector(".tj-progress-wrapper-2");
					if (slider?.children?.length) {
						let panels = gsap.utils.toArray(".tj-progress-item-2");
						let mockupItems = gsap.utils.toArray(
							".tj-progress-mockup-item-wrapper .tj-progress-mockup-item",
						);
						let totalPanels = panels.length;

						// Extended scroll for slower mockup animations
						const extendedScroll = window.innerHeight * 2;

						gsap.to(panels, {
							ease: "none",
							scrollTrigger: {
								trigger: slider,
								start: "top+=92 top",
								pin: true,
								scrub: 1,
								end: `+=${extendedScroll}`,
								onUpdate: self => {
									let progress = self.progress;
									let progressModified = progress * (totalPanels - 1);
									let activeIndex = Math.round(progressModified);

									panels.forEach((panel, index) => {
										panel.classList.toggle("active", index === activeIndex);
									});
									mockupItems.forEach((panel, index) => {
										panel.classList.toggle("active", index === activeIndex);
									});
								},
							},
						});
					}
				});
			}
		},
		// Home 6 Star Animation
		h6HeroStarAnimation: function () {
			if ($(".tj-scroll-spin").length > 0) {
				let mediaMatch = gsap.matchMedia();
				let lastProgress = 0;
				let prevState = false;
				mediaMatch.add("(min-width: 992px)", () => {
					const starWrapper = document.querySelector(
						".tj-scroll-spin-container",
					);
					const star = starWrapper.querySelector(".tj-scroll-spin");
					const starInner = starWrapper.querySelector(".tj-scroll-spin-inner");
					if (starWrapper) {
						const extendedScroll = "bottom top";
						const tl = gsap
							.timeline({
								defaults: {},
								scrollTrigger: {
									trigger: starWrapper,
									start: "top top",
									end: extendedScroll,
									scrub: 1.1,
									onUpdate: self => {
										let progress = self.progress;
										const isReverse = progress < lastProgress ? true : false;
										if (isReverse) {
											if (prevState !== isReverse) {
												starInner.style.animationDirection = "reverse";
											}
										} else {
											if (prevState !== isReverse) {
												starInner.style.animationDirection = "normal";
											}
										}
										prevState = isReverse;
										lastProgress = progress;
									},
								},
							})
							.to(star, {
								ease: "none",
								rotate: "1080",
								scale: 1.3,
							});
					}
				});
			}
		},
	};
})(jQuery);
