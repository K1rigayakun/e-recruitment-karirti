import { animate, stagger, utils } from "animejs";
import { isReduced } from "../motion/prefs.js";
import { dur, ease } from "../motion/tokens.js";

export function initNav(root = document) {
  const overlay = root.querySelector("[data-nav-overlay]");
  const panel = root.querySelector("[data-nav-panel]");
  const toggles = root.querySelectorAll("[data-nav-toggle]");

  const setOpen = (open) => {
    if (!overlay || !panel) return;
    overlay.classList.toggle("hidden", !open);
    document.body.classList.toggle("overflow-hidden", open);
    toggles.forEach((btn) => {
      btn.setAttribute("aria-expanded", open ? "true" : "false");
      btn.setAttribute("aria-label", open ? "Tutup menu" : "Buka menu");
    });
    if (open) {
      const links = [...panel.querySelectorAll("a, button")].slice(0, 12);
      const first = links[0];
      if (first instanceof HTMLElement) first.focus();
      if (!isReduced()) {
        utils.set(panel, { x: "12%" });
        animate(panel, { x: "0%", duration: dur.base, ease: ease.out });
        animate(links, { opacity: [0, 1], x: [12, 0], duration: dur.fast, delay: stagger(50), ease: ease.out });
      }
    }
  };

  toggles.forEach((btn) => {
    btn.addEventListener("click", () => {
      const open = btn.getAttribute("aria-expanded") === "true";
      setOpen(!open);
    });
  });

  root.querySelectorAll("[data-nav-close]").forEach((el) => {
    el.addEventListener("click", () => setOpen(false));
  });

  document.addEventListener("keydown", (event) => {
    if (event.key === "Escape") setOpen(false);
  });

  if (panel) {
    panel.addEventListener("keydown", (event) => {
      if (event.key !== "Tab") return;
      const focusables = [...panel.querySelectorAll('a, button, [tabindex]:not([tabindex="-1"])')].filter(
        (el) => el instanceof HTMLElement && !el.hasAttribute("disabled"),
      );
      if (!focusables.length) return;
      const first = focusables[0];
      const last = focusables[focusables.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    });
  }
}
