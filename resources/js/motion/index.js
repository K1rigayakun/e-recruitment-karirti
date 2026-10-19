import { initPrefs } from "./prefs.js";
import { initReveal, replayReveal } from "./reveal.js";
import { initBackground } from "./background.js";
import { initButtons } from "./buttons.js";
import { initTilt } from "./tilt.js";
import { initCounters, replayCounter } from "./counters.js";
import { initPageEnter, initScrollProgress } from "./page-enter.js";
import { initPipeline, replayPipeline } from "./pipeline.js";
import { getScope } from "./scope.js";

export function initMotion() {
  initPrefs();
  getScope();
  initPageEnter();
  initScrollProgress();
  initBackground();
  initReveal();
  initButtons();
  initTilt();
  initCounters();
  initPipeline();
  initReplay();
}

function initReplay() {
  document.querySelectorAll("[data-replay]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const sel = btn.getAttribute("data-replay");
      const el = sel ? document.querySelector(sel) : null;
      if (!el) return;
      if (el.hasAttribute("data-reveal")) replayReveal(el);
      else if (el.hasAttribute("data-count-to")) replayCounter(el);
      else if (el.hasAttribute("data-pipeline")) replayPipeline(el);
      else if (el.hasAttribute("data-list-enter")) {
        [...el.children].slice(0, 12).forEach((child, i) => {
          child.style.opacity = "0";
          window.setTimeout(() => {
            child.style.opacity = "1";
            child.style.transform = "translateY(0)";
          }, i * 40);
        });
      }
    });
  });
}
