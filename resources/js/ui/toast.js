import { animate } from "animejs";
import { isReduced } from "../motion/prefs.js";
import { ease } from "../motion/tokens.js";

const TONE = {
  mint: "border-mint/40",
  coral: "border-coral/40",
  signal: "border-signal/40",
  sun: "border-sun/40",
};

function region() {
  return document.querySelector("[data-toast-region]");
}

function dismiss(el, anim) {
  if (isReduced()) {
    el.remove();
    return;
  }
  anim?.pause();
  animate(el, {
    opacity: 0,
    scale: 0.96,
    duration: 160,
    ease: "inOutQuad",
    onComplete: () => el.remove(),
  });
}

function scheduleDismiss(el) {
  const bar = el.querySelector("[data-toast-progress]");
  let progress;
  if (bar && !isReduced()) {
    progress = animate(bar, { scaleX: [1, 0], duration: 5000, ease: "linear" });
  }

  let timeout = window.setTimeout(() => dismiss(el, progress), 5000);
  el.addEventListener("mouseenter", () => {
    window.clearTimeout(timeout);
    progress?.pause();
  });
  el.addEventListener("mouseleave", () => {
    const remain = progress ? Math.max(400, 5000 * (1 - (progress.currentTime || 0) / 5000)) : 5000;
    progress?.play();
    timeout = window.setTimeout(() => dismiss(el, progress), remain);
  });
}

export function showToast(message, tone = "signal") {
  const host = region();
  if (!host) return;

  const el = document.createElement("div");
  el.setAttribute("role", "status");
  el.setAttribute("data-toast", "");
  el.className = `pointer-events-auto overflow-hidden rounded-card border bg-ink-raised px-4 py-3 text-sm text-paper shadow-lift ${TONE[tone] ?? TONE.signal}`;
  el.innerHTML = `<p></p><span data-toast-progress class="mt-2 block h-0.5 origin-left bg-signal"></span>`;
  el.querySelector("p").textContent = message;
  host.appendChild(el);

  if (!isReduced()) {
    animate(el, { x: [24, 0], opacity: [0, 1], duration: 420, ease: ease.spring });
  }

  scheduleDismiss(el);
}

export function initToasts(root = document) {
  root.querySelectorAll("[data-toast]").forEach((el) => scheduleDismiss(el));

  root.querySelectorAll("[data-toast-trigger]").forEach((btn) => {
    btn.addEventListener("click", () => {
      showToast(btn.getAttribute("data-toast-trigger") ?? "Tersimpan.", btn.getAttribute("data-toast-tone") ?? "signal");
    });
  });
}
