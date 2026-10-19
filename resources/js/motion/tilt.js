import { createAnimatable } from "animejs";
import { isReduced } from "./prefs.js";

export function initTilt() {
  if (isReduced() || window.matchMedia("(pointer: coarse)").matches) return;

  document.querySelectorAll("[data-tilt]").forEach((card) => {
    const m = createAnimatable(card, {
      rotateX: 200,
      rotateY: 200,
      ease: "out(3)",
    });
    card.style.perspective = "900px";

    card.addEventListener("pointermove", (event) => {
      const r = card.getBoundingClientRect();
      const px = (event.clientX - r.left) / r.width;
      const py = (event.clientY - r.top) / r.height;
      card.style.setProperty("--mx", `${(px * 100).toFixed(1)}%`);
      card.style.setProperty("--my", `${(py * 100).toFixed(1)}%`);
      m.rotateY((px - 0.5) * 10);
      m.rotateX((0.5 - py) * 10);
    });

    card.addEventListener("pointerleave", () => {
      m.rotateX(0);
      m.rotateY(0);
    });
  });
}
