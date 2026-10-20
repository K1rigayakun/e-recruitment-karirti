import { spring } from "animejs";
import { isReduced } from "./prefs.js";

export { isReduced };

export const dur = { fast: 200, base: 360, slow: 640, hero: 1100 };

export const ease = {
  out: "outExpo",
  soft: "outQuart",
  inOut: "inOutQuad",
  spring: spring({ stiffness: 170, damping: 14 }),
};

export const gap = { tight: 40, base: 70, loose: 120 };
