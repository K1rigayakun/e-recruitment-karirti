import { animate } from "animejs";
import { isReduced } from "./prefs.js";

export function replayCounter(el) {
  run(el);
}

function run(el) {
  const target = Number(el.getAttribute("data-count-to") || el.textContent.replace(/\D/g, "") || 0);
  if (isReduced()) {
    el.textContent = Math.round(target).toLocaleString("id-ID");
    return;
  }
  const state = { v: 0 };
  animate(state, {
    v: target,
    duration: 1200,
    ease: "outExpo",
    onUpdate: () => {
      el.textContent = Math.round(state.v).toLocaleString("id-ID");
    },
  });
}

export function initCounters() {
  const els = [...document.querySelectorAll("[data-count-to]")];
  if (!els.length) return;

  if (isReduced() || !("IntersectionObserver" in window)) {
    els.forEach(run);
    return;
  }

  const io = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      io.unobserve(entry.target);
      run(entry.target);
    }
  }, { threshold: 0.4 });

  els.forEach((el) => io.observe(el));
}
