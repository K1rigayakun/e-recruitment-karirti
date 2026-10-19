import { initUi } from "./ui/index.js";
import { initMotion } from "./motion/index.js";
import { replayPipeline } from "./motion/pipeline.js";
import { showToast } from "./ui/toast.js";
import { applyMotionPref } from "./motion/prefs.js";

// Terapkan preferensi motion sebelum paint
applyMotionPref();

initUi();
initMotion();

// Update visual switch saat preferensi motion berubah
window.addEventListener("karirti:motion-pref", () => {
  const reduced = document.documentElement.getAttribute("data-motion") === "reduced";
  const thumb = document.getElementById("motion-toggle-thumb");
  const sw = document.getElementById("motion-toggle-switch");
  if (thumb) thumb.style.transform = reduced ? "translateX(20px)" : "translateX(0)";
  if (sw) sw.setAttribute("aria-checked", String(reduced));
});

// Expose utilitas ke window agar blade/demo bisa memanggil
window.karirti = { replayPipeline, showToast };
