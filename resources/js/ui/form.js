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
}

export function initMotionToggle(root = document) {
  const reduced = document.documentElement.getAttribute("data-motion") === "reduced";
  root.querySelectorAll("[data-motion-toggle-label]").forEach((label) => {
    label.textContent = reduced ? "Aktifkan animasi" : "Kurangi animasi";
  });

  root.querySelectorAll("[data-motion-toggle]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const next = document.documentElement.getAttribute("data-motion") === "reduced" ? "auto" : "reduced";
      try {
        localStorage.setItem("motion-pref", next);
      } catch (e) {
        /* abaikan mode privat */
      }
      if (next === "reduced") {
        document.documentElement.setAttribute("data-motion", "reduced");
      } else {
        document.documentElement.removeAttribute("data-motion");
      }
      root.querySelectorAll("[data-motion-toggle-label]").forEach((label) => {
        label.textContent = next === "reduced" ? "Aktifkan animasi" : "Kurangi animasi";
      });
    });
  });
}
