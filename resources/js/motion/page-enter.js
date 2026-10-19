import { animate, utils } from "animejs";
import { dur, ease, isReduced } from "./tokens.js";

export function initPageEnter() {
  const main = document.querySelector("[data-page-enter]");
  if (!main) return;

  if (isReduced()) {
    main.setAttribute("data-entered", "");
    return;
  }

  utils.set(main, { opacity: 0, y: 16 });
  main.setAttribute("data-entered", "");
  animate(main, {
    opacity: 1,
    y: 0,
    duration: dur.base,
    ease: ease.soft,
  });
}

export function initScrollProgress() {
  const bar = document.querySelector("[data-scroll-progress]");
  const nav = document.querySelector("[data-nav]");
  if (!bar && !nav) return;

  const onScroll = () => {
    const max = document.documentElement.scrollHeight - window.innerHeight;
    const p = max > 0 ? window.scrollY / max : 0;
    if (bar) bar.style.transform = `scaleX(${p})`;
    if (nav) {
      const scrolled = window.scrollY > 40;
      nav.classList.toggle("is-scrolled", scrolled);
      nav.classList.toggle("glass", scrolled);
      nav.classList.toggle("border-ink-line", scrolled);
    }
  };

  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });
}
