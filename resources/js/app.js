import { animate, createTimeline, createAnimatable, createScope, spring,
         onScroll, splitText, stagger, svg, utils } from "animejs";

// Export ke window agar bisa diakses dari modul lain
window.anime = { animate, createTimeline, createAnimatable, createScope, spring,
                 onScroll, splitText, stagger, svg, utils };

// Spotlight: perbarui CSS variable --mx --my saat kursor bergerak
document.addEventListener("pointermove", (e) => {
  const x = ((e.clientX / window.innerWidth) * 100).toFixed(1) + "%";
  const y = ((e.clientY / window.innerHeight) * 100).toFixed(1) + "%";
  document.documentElement.style.setProperty("--mx", x);
  document.documentElement.style.setProperty("--my", y);
});
