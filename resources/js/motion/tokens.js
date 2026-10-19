/**
 * Token motion KarirTI
 * Semua durasi dalam ms. Semua easing sesuai anime.js v4.
 */
export const duration = {
  fast: 200,
  base: 360,
  slow: 640,
  hero: 1100,
};

export const ease = {
  enter: "outExpo",
  leave: "inOutQuad",
  spring: "spring(1, 80, 10, 0)",
};

export const staggerStep = 60; // ms antar elemen
export const staggerMax  = 12; // maksimal elemen yang di-stagger
