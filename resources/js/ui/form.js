import { animate, svg } from "animejs";
import { isReduced } from "../motion/prefs.js";

function shake(el) {
  if (isReduced()) return;
  animate(el, { x: [0, 6, -6, 6, -6, 0], duration: 260, ease: "inOutQuad" });
}

export function initForms(root = document) {
  root.querySelectorAll("form[data-loading-submit]").forEach((form) => {
    form.addEventListener("submit", () => {
      const btn = form.querySelector("button[type='submit']");
      if (!(btn instanceof HTMLButtonElement) || btn.disabled) return;
      const text = btn.getAttribute("data-loading-text") || "Menyimpan...";
      btn.disabled = true;
      btn.setAttribute("aria-busy", "true");
      btn.textContent = text;
    });
  });

  root.querySelectorAll("[data-field-error]").forEach((err) => {
    const field = err.closest(".field") ?? err;
    shake(field);
    if (!isReduced()) {
      animate(err, { opacity: [0, 1], duration: 200, ease: "outQuart" });
    }
  });

  root.querySelectorAll("[data-form-success]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const mark = document.querySelector("[data-success-check]");
      if (!mark) return;
      mark.classList.remove("hidden");
      if (isReduced()) return;
      const lines = svg.createDrawable(mark);
      animate(lines, { draw: ["0 0", "0 1"], duration: 500, ease: "inOutQuad" });
    });
  });

  root.querySelectorAll("[data-password-meter]").forEach((input) => {
    const bar = document.querySelector("[data-password-meter-bar]");
    if (!(input instanceof HTMLInputElement) || !bar) return;
    input.addEventListener("input", () => {
      const v = input.value.length;
      const score = Math.min(1, v / 12);
      if (isReduced()) {
        bar.style.transform = `scaleX(${score})`;
        return;
      }
      animate(bar, { scaleX: score, duration: 200, ease: "outQuart" });
    });
  });

  root.querySelectorAll("[data-replay-shake]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const field = document.querySelector("[data-shake-demo]");
      if (field) shake(field);
    });
  });
}
