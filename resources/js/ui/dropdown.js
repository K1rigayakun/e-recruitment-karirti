import { animate, stagger, utils } from "animejs";
import { isReduced } from "../motion/prefs.js";
import { dur, ease } from "../motion/tokens.js";

function closeDropdown(root) {
  const trigger = root.querySelector("[data-dropdown-trigger]");
  const panel = root.querySelector("[data-dropdown-panel]");
  if (!trigger || !panel || panel.hidden) return;

  const finish = () => {
    panel.hidden = true;
    trigger.setAttribute("aria-expanded", "false");
  };

  if (isReduced()) {
    finish();
    return;
  }

  animate(panel, {
    opacity: 0,
    scaleY: 0.96,
    duration: Math.round(dur.fast * 0.6),
    ease: ease.inOut,
    onComplete: finish,
  });
}

function openDropdown(root) {
  const trigger = root.querySelector("[data-dropdown-trigger]");
  const panel = root.querySelector("[data-dropdown-panel]");
  if (!trigger || !panel) return;
  panel.hidden = false;
  trigger.setAttribute("aria-expanded", "true");
  const items = [...panel.querySelectorAll('[role="menuitem"]')].slice(0, 12);
  const first = items[0];
  if (first instanceof HTMLElement) first.focus();

  if (isReduced()) return;

  utils.set(panel, { opacity: 0, scaleY: 0.96, transformOrigin: "top" });
  animate(panel, { opacity: 1, scaleY: 1, duration: dur.fast, ease: ease.out });
  if (items.length) {
    utils.set(items, { opacity: 0, y: 6 });
    animate(items, { opacity: 1, y: 0, duration: dur.fast, delay: stagger(30), ease: ease.out });
  }
}

export function initDropdowns(root = document) {
  root.querySelectorAll("[data-dropdown]").forEach((dropdown) => {
    const trigger = dropdown.querySelector("[data-dropdown-trigger]");
    const panel = dropdown.querySelector("[data-dropdown-panel]");
    if (!trigger || !panel) return;

    trigger.addEventListener("click", (event) => {
      event.stopPropagation();
      const open = trigger.getAttribute("aria-expanded") === "true";
      root.querySelectorAll("[data-dropdown]").forEach((other) => {
        if (other !== dropdown) closeDropdown(other);
      });
      if (open) closeDropdown(dropdown);
      else openDropdown(dropdown);
    });

    trigger.addEventListener("keydown", (event) => {
      if (event.key === "ArrowDown") {
        event.preventDefault();
        openDropdown(dropdown);
      }
    });

    panel.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        event.preventDefault();
        closeDropdown(dropdown);
        trigger.focus();
      }
    });
  });

  document.addEventListener("click", (event) => {
    root.querySelectorAll("[data-dropdown]").forEach((dropdown) => {
      if (!dropdown.contains(event.target)) closeDropdown(dropdown);
    });
  });

  document.addEventListener("keydown", (event) => {
    if (event.key !== "Escape") return;
    root.querySelectorAll("[data-dropdown]").forEach((dropdown) => closeDropdown(dropdown));
  });
}
