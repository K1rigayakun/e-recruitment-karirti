import { createScope } from "animejs";

let scope;

export function getScope() {
  if (!scope) {
    scope = createScope({
      root: document.documentElement,
      mediaQueries: { reduce: "(prefers-reduced-motion: reduce)" },
    });
    window.addEventListener("pagehide", () => {
      scope?.revert();
      scope = null;
    });
  }
  return scope;
}
