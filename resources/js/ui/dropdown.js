function closeDropdown(root) {
  const trigger = root.querySelector("[data-dropdown-trigger]");
  const panel = root.querySelector("[data-dropdown-panel]");
  if (!trigger || !panel) return;
  panel.hidden = true;
  trigger.setAttribute("aria-expanded", "false");
}

function openDropdown(root) {
  const trigger = root.querySelector("[data-dropdown-trigger]");
  const panel = root.querySelector("[data-dropdown-panel]");
  if (!trigger || !panel) return;
  panel.hidden = false;
  trigger.setAttribute("aria-expanded", "true");
  const first = panel.querySelector('[role="menuitem"]');
  if (first instanceof HTMLElement) first.focus();
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
