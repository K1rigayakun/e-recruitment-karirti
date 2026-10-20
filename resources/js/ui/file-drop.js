import { animate } from "animejs";
import { isReduced } from "../motion/prefs.js";
import { ease } from "../motion/tokens.js";

function bytesToLabel(bytes) {
  if (bytes < 1024) return `${bytes} B`;
  if (bytes < 1024 * 1024) return `${Math.round(bytes / 1024)} KB`;
  return `${(bytes / (1024 * 1024)).toFixed(1)} MB`;
}

function isPdf(file) {
  return file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
}

export function initFileDrops(root = document) {
  root.querySelectorAll("[data-file-drop]").forEach((wrap) => {
    const input = wrap.querySelector('input[type="file"]');
    const zone = wrap.querySelector("[data-file-drop-zone]");
    const idle = wrap.querySelector("[data-file-drop-idle]");
    const fileLabel = wrap.querySelector("[data-file-drop-file]");
    const remove = wrap.querySelector("[data-file-drop-remove]");
    const error = wrap.querySelector("[data-file-drop-error]");
    const maxKb = Number(wrap.getAttribute("data-max-kb") || 2048);

    if (!(input instanceof HTMLInputElement) || !zone) return;

    const showError = (message) => {
      if (error) {
        error.textContent = message;
        error.classList.remove("hidden");
      }
      input.setAttribute("aria-invalid", "true");
      if (!isReduced()) animate(zone, { x: [0, 6, -6, 0], duration: 260, ease: "inOutQuad" });
    };

    const clearError = () => {
      if (error) {
        error.textContent = "";
        error.classList.add("hidden");
      }
      input.removeAttribute("aria-invalid");
    };

    const applyFile = (file) => {
      clearError();
      if (!file) return;
      if (!isPdf(file) || file.size > maxKb * 1024) {
        showError("CV harus berformat PDF dan maksimal 2 MB. Pilih file lain lalu coba lagi.");
        input.value = "";
        return;
      }
      if (idle) idle.classList.add("hidden");
      if (fileLabel) {
        fileLabel.textContent = `${file.name} · ${bytesToLabel(file.size)}`;
        fileLabel.classList.remove("hidden");
        if (!isReduced()) {
          animate(fileLabel, { scale: [0.9, 1], opacity: [0, 1], duration: 360, ease: ease.spring });
        }
      }
      if (remove) remove.classList.remove("hidden");
    };

    zone.addEventListener("dragover", (event) => {
      event.preventDefault();
      zone.classList.add("border-solid", "border-signal");
      if (!isReduced()) animate(zone, { scale: 1.02, duration: 180, ease: "outQuart" });
    });

    zone.addEventListener("dragleave", () => {
      zone.classList.remove("border-solid", "border-signal");
      if (!isReduced()) animate(zone, { scale: 1, duration: 180, ease: "outQuart" });
    });

    zone.addEventListener("drop", (event) => {
      event.preventDefault();
      zone.classList.remove("border-solid", "border-signal");
      if (!isReduced()) animate(zone, { scale: 1, duration: 180, ease: "outQuart" });
      const file = event.dataTransfer?.files?.[0];
      if (!file) return;
      const transfer = new DataTransfer();
      transfer.items.add(file);
      input.files = transfer.files;
      applyFile(file);
    });

    input.addEventListener("change", () => applyFile(input.files?.[0]));

    remove?.addEventListener("click", () => {
      input.value = "";
      if (idle) idle.classList.remove("hidden");
      if (fileLabel) fileLabel.classList.add("hidden");
      remove.classList.add("hidden");
      clearError();
    });
  });
}
