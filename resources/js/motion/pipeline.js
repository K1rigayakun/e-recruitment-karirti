import { animate, stagger, svg } from "animejs";
import { isReduced } from "./prefs.js";
import { ease } from "./tokens.js";

export function replayPipeline(root) {
  play(root);
}

function play(root) {
  const status = root.getAttribute("data-status") || "applied";
  const map = { applied: 0.25, viewed: 0.5, interview: 0.75, accepted: 1, rejected: 1 };
  const progress = map[status] ?? 0.25;
  const line = root.querySelector("[data-pipeline-line]");
  const nodes = [...root.querySelectorAll("[data-pipeline-node]")];
  const current = Math.min(nodes.length - 1, Math.round(progress * (nodes.length - 1)));

  if (isReduced() || !line) {
    nodes.forEach((n, i) => n.classList.toggle("is-on", i <= current));
    return;
  }

  const drawable = svg.createDrawable(line);
  animate(drawable, {
    draw: ["0 0", `0 ${progress}`],
    duration: 900,
    ease: "inOutQuad",
  });

  animate(nodes.slice(0, current + 1), {
    scale: [0, 1],
    duration: 320,
    delay: stagger(120),
    ease: ease.spring,
    onComplete: () => {
      const active = nodes[current];
      if (!active || isReduced()) return;
      animate(active, {
        scale: [1, 1.12, 1],
        duration: 2400,
        ease: "inOutSine",
        loop: true,
      });
    },
  });
}

export function initPipeline() {
  document.querySelectorAll("[data-pipeline]").forEach(play);
}
