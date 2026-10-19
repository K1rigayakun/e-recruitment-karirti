import { createAnimatable } from "animejs";
import { isReduced } from "./prefs.js";

export function initButtons() {
  if (isReduced() || window.matchMedia("(pointer: coarse)").matches) return;

  document.querySelectorAll("[data-magnetic]").forEach((btn) => {
    const m = createAnimatable(btn, { x: 300, y: 300, ease: "out(3)" });

    btn.addEventListener("pointermove", (event) => {
      const r = btn.getBoundingClientRect();
      const dx = (event.clientX - (r.left + r.width / 2)) * 0.2;
      const dy = (event.clientY - (r.top + r.height / 2)) * 0.2;
      m.x(Math.max(-8, Math.min(8, dx)));
      m.y(Math.max(-8, Math.min(8, dy)));
    });

    btn.addEventListener("pointerleave", () => {
      m.x(0);
      m.y(0);
    });
  });
}
