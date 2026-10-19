const TONE = {
  mint: "border-mint/40",
  coral: "border-coral/40",
  signal: "border-signal/40",
  sun: "border-sun/40",
};

function region() {
  return document.querySelector("[data-toast-region]");
}

function scheduleDismiss(el) {
  const timeout = window.setTimeout(() => el.remove(), 5000);
  el.addEventListener("mouseenter", () => window.clearTimeout(timeout), { once: true });
}

export function showToast(message, tone = "signal") {
  const host = region();
  if (!host) return;

  const el = document.createElement("div");
  el.setAttribute("role", "status");
  el.setAttribute("data-toast", "");
  el.className = `pointer-events-auto rounded-card border bg-ink-raised px-4 py-3 text-sm text-paper shadow-lift ${TONE[tone] ?? TONE.signal}`;
  el.textContent = message;
  host.appendChild(el);
  scheduleDismiss(el);
}

export function initToasts(root = document) {
  root.querySelectorAll("[data-toast]").forEach((el) => scheduleDismiss(el));

  root.querySelectorAll("[data-toast-trigger]").forEach((btn) => {
    btn.addEventListener("click", () => {
      showToast(btn.getAttribute("data-toast-trigger") ?? "Tersimpan.", btn.getAttribute("data-toast-tone") ?? "signal");
    });
  });
}
