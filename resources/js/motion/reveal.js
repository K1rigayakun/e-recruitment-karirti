import { animate, splitText, stagger, utils } from "animejs";
import { dur, ease, gap, isReduced } from "./tokens.js";

function play(el) {
  const kind = el.dataset.reveal || "fade-up";

  if (kind === "split") {
    const { words } = splitText(el, { words: { wrap: "clip" } });
    utils.set(words, { y: "110%" });
    el.setAttribute("data-revealed", "");
    animate(words, {
      y: "0%",
      duration: dur.slow + 200,
      delay: stagger(55),
      ease: ease.out,
    });
    return;
  }

  if (kind === "scale-clip") {
    utils.set(el, { opacity: 0, scale: 1.04, clipPath: "inset(8% round 18px)" });
    el.setAttribute("data-revealed", "");
    animate(el, {
      opacity: 1,
      scale: 1,
      clipPath: "inset(0% round 18px)",
      duration: dur.slow,
      ease: ease.soft,
    });
    return;
  }

  const targets = kind === "stagger" ? [...el.children].slice(0, 12) : el;
  utils.set(targets, { opacity: 0, y: 24 });
  el.setAttribute("data-revealed", "");
  animate(targets, {
    opacity: 1,
    y: 0,
    duration: dur.slow,
    delay: kind === "stagger" ? stagger(gap.base) : 0,
    ease: ease.out,
  });
}

export function replayReveal(el) {
  el.removeAttribute("data-revealed");
  if (isReduced()) {
    el.setAttribute("data-revealed", "");
    return;
  }
  window.requestAnimationFrame(() => play(el));
}

export function initReveal() {
  const els = [...document.querySelectorAll("[data-reveal]")];
  if (!els.length) return;

  const showNow = () => els.forEach((el) => el.setAttribute("data-revealed", ""));
  if (isReduced() || !("IntersectionObserver" in window)) return showNow();

  const io = new IntersectionObserver(
    (entries) => {
      for (const { target, isIntersecting } of entries) {
        if (!isIntersecting) continue;
        io.unobserve(target);
        play(target);
      }
    },
    { threshold: 0.2, rootMargin: "0px 0px -8% 0px" },
  );

  els.forEach((el) => io.observe(el));
}
