// Window Shape
let animationStarted = false;
let panels = gsap.utils.toArray(
	".window-shape-screen-2 .purpose-item-wrapper .tj-progress-item",
);
const totalPanels = panels?.length;
class WindowShapeAnim {
	constructor() {
		((this.mm = gsap.matchMedia()), (this.orientation = null), this.init());
	}
	init() {
		this.hero();
	}

	hero() {
		this.mm.add(
			{
				isDesktop: "(min-width: 992px)",
				isMobile: "(max-width: 991px)",
				isLandscape: "(orientation: landscape)",
				isPortrait: "(orientation: portrait)",
			},
			t => {
				const {
					isDesktop: d,
					isMobile: m,
					isLandscape: a,
					isPortrait: r,
				} = t.conditions;
				(gsap.set(
					[
						".window-shape-screen-2 .purpose-mockup-wrapper",
						".window-shape-screen-2 .purpose-mockup",
					],
					{
						clearProps: "all",
					},
				),
					d &&
						(gsap.set(".window-shape-screen-2 .purpose-mockup-wrapper", {
							xPercent: -50,
							yPercent: -50,
						}),
						gsap
							.timeline({
								defaults: {},
								scrollTrigger: {
									trigger: ".window-shape-wrapper",
									start: "top top",
									end: "bottom+=100% top",
									scrub: 1.1,
									pin: !0,
									onUpdate: i => {},
								},
							})
							.to(
								{},
								{
									duration: 12,
								},
							)
							.to(
								".window-shape-screen-1 .hero_title",
								{
									xPercent: 100,
									autoAlpha: 0,
									duration: 4,
								},
								"0",
							)
							.to(
								".window-shape-screen-1 .trusted_features",
								{
									xPercent: -100,
									autoAlpha: 0,
									duration: 4,
								},
								"0",
							)
							.to(
								[".window-shape-screen-1 .desc_wrapper"],
								{
									autoAlpha: 0,
									y: -50,
									stagger: 0.1,
									duration: 4,
								},
								"0",
							)
							.to(
								[".window-shape-screen-1 .category-wrap"],
								{
									autoAlpha: 0,
									y: 50,
									stagger: 0.1,
									duration: 4,
								},
								"0",
							)
							.fromTo(
								".window-shape-screen-2 .purpose-mockup-wrapper",
								{ autoAlpha: 0 },
								{
									autoAlpha: 1,
									duration: 10,
									scale: 1,
									clipPath: "polygon(0% 0%, 100% 0%, 100% 100%, 0% 100%)",
								},
								"0",
							)
							.fromTo(
								".window-shape-img img",
								{ scale: 0 },
								{
									duration: 10,
									scale: 200,
									autoAlpha: 1,
								},
								"0",
							)
							.to(
								".window-shape-screen-2 .purpose-mockup",
								{
									duration: 5,
									scale: 1,
								},
								"0",
							)
							.fromTo(
								".window-shape-screen-2 .sec_subtitle",
								{
									autoAlpha: 0,
									xPercent: "-50",
								},
								{
									autoAlpha: 1,
									xPercent: 1,
									stagger: 0.1,
									duration: 3,
								},
								"2.5",
							)
							.fromTo(
								".window-shape-screen-2 .sec_title",
								{
									autoAlpha: 0,
									xPercent: "-50",
								},
								{
									autoAlpha: 1,
									xPercent: 1,
									stagger: 0.1,
									duration: 3,
								},
								"2.5",
							)

							.fromTo(
								[
									".window-shape-screen-2 video",
									".window-shape-screen-2 .purpose-video-title",
								],
								{
									autoAlpha: 0,
									y: 50,
								},
								{
									autoAlpha: 1,
									y: 0,
									stagger: 0.3,
									duration: 4,
								},
								"3",
							)
							.set(
								".window-shape-screen-2",
								{
									pointerEvents: "all",
								},
								"3.5",
							)
							.fromTo(
								".window-shape-screen-2 .purpose-cta",
								{
									autoAlpha: 0,
									xPercent: -100,
								},
								{
									autoAlpha: 1,
									xPercent: 0,
									duration: 3,
								},
								"3.5",
							)
							.fromTo(
								".window-shape-screen-2 .tj-progress-item",
								{
									autoAlpha: 0,
									yPercent: 50,
								},
								{
									autoAlpha: 1,
									yPercent: 0,
									stagger: 0.1,
									duration: 3,
								},
								"3.5",
							)
							.fromTo(
								".window-shape-screen-2 .purpose-mockup-item:not(:first-child)",
								{
									yPercent: 100,
									scale: 1.5,
								},
								{
									scale: 1.2,
									yPercent: 0,
									stagger: 4,
									duration: 4,
								},
								"7",
							)
							.fromTo(
								{},
								{},
								{
									duration: 3 * totalPanels,
									onStart: function () {
										animationStarted = true;
									},
									onUpdate: function () {
										// Only run if animation has actually started
										if (!animationStarted) return;
										const progress = this.progress();
										let progressModified = progress * (totalPanels - 1);
										let activeIndex = Math.round(progressModified);
										panels.forEach((panel, index) => {
											panel.classList.toggle("active", index === activeIndex);
										});
									},
									onReverseComplete: function () {
										animationStarted = false;
									},
								},
								"7",
							)),
					m &&
						(gsap.set(
							[
								".window-shape-screen-1 .hero_title",
								".window-shape-screen-1 .desc_wrapper",
								".window-shape-screen-1 .trusted_features",
								".window-shape-screen-1 .category-wrap",
								".window-shape-screen-2 .purpose-mockup-wrapper",
							],
							{
								clearProps: "all",
							},
						),
						gsap
							.timeline({
								defaults: {},
								scrollTrigger: {
									trigger: ".window-shape-wrapper",
									start: "top top",
									end: "bottom top",
									scrub: 1,
								},
							})
							.to(
								{},
								{
									duration: 10,
								},
							)
							.to(
								".window-shape-wrapper",
								{
									autoAlpha: 1,
									duration: 0,
								},
								0,
							)
							.set(
								".window-shape-screen-2",
								{
									pointerEvents: "all",
								},
								"0",
							)
							.fromTo(
								".window-shape-screen-2 .sec_subtitle",
								{
									autoAlpha: 0,
									y: 100,
								},
								{
									autoAlpha: 1,
									y: 0,
									duration: 1,
								},
								"0",
							)
							.fromTo(
								".window-shape-screen-2 .sec_title",
								{
									autoAlpha: 0,
									y: 100,
								},
								{
									autoAlpha: 1,
									y: 0,
									duration: 2,
								},
								"0",
							)
							.fromTo(
								".window-shape-screen-2 .purpose-cta",
								{
									autoAlpha: 0,
									y: 100,
								},
								{
									autoAlpha: 1,
									y: 0,
									duration: 3,
								},
								"0",
							)
							.fromTo(
								".window-shape-screen-2 .purpose-video",
								{
									autoAlpha: 0,
									y: 100,
								},
								{
									autoAlpha: 1,
									y: 0,
									stagger: 0.3,
									duration: 0.5,
								},
								"1.5",
							)
							.fromTo(
								".window-shape-screen-2 .tj-progress-item",
								{
									autoAlpha: 0,
									y: 100,
								},
								{
									autoAlpha: 1,
									y: 0,
									stagger: 2,
									duration: 1,
								},
								"2",
							)));
			},
		);
	}
}
