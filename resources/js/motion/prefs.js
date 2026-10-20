const KEY = "motion-pref";

export function isReduced() {
  return (
    document.documentElement.dataset.motion === "reduced" ||
    window.matchMedia("(prefers-reduced-motion: reduce)").matches
  );
}

export function applyMotionPref() {
  let stored = "auto";
  try {
    stored = localStorage.getItem(KEY) || "auto";
  } catch (e) {
    stored = "auto";
  }

  if (stored === "reduced") {
    document.documentElement.setAttribute("data-motion", "reduced");
  } else {
    document.documentElement.removeAttribute("data-motion");
  }

  syncToggleLabels();
}

export function setMotionPref(next) {
  const value = next === "reduced" ? "reduced" : "auto";
  try {
    localStorage.setItem(KEY, value);
  } catch (e) {
    /* mode privat */
  }
  applyMotionPref();
  window.dispatchEvent(new CustomEvent("karirti:motion-pref", { detail: value }));
}

function syncToggleLabels() {
  const reduced = document.documentElement.getAttribute("data-motion") === "reduced";
  document.querySelectorAll("[data-motion-toggle-label]").forEach((label) => {
    label.textContent = reduced ? "Aktifkan animasi" : "Kurangi animasi";
  });
}

export function initPrefs() {
  applyMotionPref();

  document.querySelectorAll("[data-motion-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const next = document.documentElement.getAttribute("data-motion") === "reduced" ? "auto" : "reduced";
      setMotionPref(next);
    });
  });
}
