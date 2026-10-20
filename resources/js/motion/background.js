import { animate } from "animejs";
import { isReduced } from "./prefs.js";

let raf = 0;
let mx = 50;
let my = 30;
let tx = 50;
let ty = 30;
let idleTimer = 0;
let tracking = true;

function isCoarse() {
  return window.matchMedia("(pointer: coarse)").matches;
}

function stopLoop() {
  if (raf) cancelAnimationFrame(raf);
  raf = 0;
}

function tick() {
  mx += (tx - mx) * 0.12;
  my += (ty - my) * 0.12;
  document.documentElement.style.setProperty("--mx", `${mx.toFixed(1)}%`);
  document.documentElement.style.setProperty("--my", `${my.toFixed(1)}%`);
  raf = requestAnimationFrame(tick);
}

function startSpotlight() {
  if (isReduced() || isCoarse() || document.hidden) return;
  if (!raf) raf = requestAnimationFrame(tick);
}

export function initBackground() {
  if (!isReduced() && !isCoarse()) {
    document.addEventListener("pointermove", (event) => {
      tx = (event.clientX / window.innerWidth) * 100;
      ty = (event.clientY / window.innerHeight) * 100;
      tracking = true;
      startSpotlight();
      window.clearTimeout(idleTimer);
      idleTimer = window.setTimeout(() => {
        tracking = false;
        stopLoop();
      }, 2000);
    });
  }

  document.addEventListener("visibilitychange", () => {
    if (document.hidden) {
      stopLoop();
      pauseGlows();
    } else if (tracking) {
      startSpotlight();
      resumeGlows();
    }
  });

  initGlows();
}

let glowAnims = [];

function pauseGlows() {
  glowAnims.forEach((a) => a.pause());
}

function resumeGlows() {
  if (isReduced() || document.hidden) return;
  glowAnims.forEach((a) => a.play());
}

function initGlows() {
  if (!document.body.hasAttribute("data-ambient")) return;

  const host = document.querySelector("[data-ambient-glows]");
  if (!host) return;

  const glows = [...host.querySelectorAll("[data-glow]")];
  const count = isCoarse() ? 2 : 3;
  glows.forEach((el, i) => {
    el.hidden = i >= count;
  });

  if (isReduced()) return;

  glowAnims = glows.slice(0, count).map((el, i) =>
    animate(el, {
      x: ["-6vw", "6vw"],
      y: i % 2 === 0 ? ["-4vw", "5vw"] : ["5vw", "-4vw"],
      duration: 18000 + i * 5000,
      ease: "inOutSine",
      alternate: true,
      loop: true,
    }),
  );

  const io = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (entry.isIntersecting && !document.hidden && !isReduced()) resumeGlows();
      else pauseGlows();
    }
  });
  io.observe(host);
}
