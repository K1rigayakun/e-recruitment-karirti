import { animate } from "animejs";
import { isReduced } from "../motion/prefs.js";
import { dur, ease } from "../motion/tokens.js";

function animateIn(dialog) {
  if (isReduced()) return;
  animate(dialog, {
    opacity: [0, 1],
    scale: [0.96, 1],
    duration: 240,
    ease: ease.soft,
  });
}

function animateOut(dialog) {
  if (dialog._closing) return;
  dialog._closing = true;
  const finish = () => {
    dialog.close();
    dialog._closing = false;
  };
  if (isReduced()) {
    finish();
    return;
  }
  animate(dialog, {
    opacity: 0,
    scale: 0.96,
    duration: 160,
    ease: ease.inOut,
    onComplete: finish,
  });
}

export function initDialogs(root = document) {
  root.querySelectorAll("[data-dialog-open]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const dialog = document.getElementById(btn.getAttribute("data-dialog-open"));
      if (!(dialog instanceof HTMLDialogElement)) return;
      dialog._opener = btn;
      dialog.showModal();
      animateIn(dialog);
      const closeBtn = dialog.querySelector("[data-dialog-close]");
      (closeBtn instanceof HTMLElement ? closeBtn : dialog).focus();
    });
  });

  root.querySelectorAll("dialog.ui-dialog").forEach((dialog) => {
    if (!(dialog instanceof HTMLDialogElement)) return;

    dialog.querySelectorAll("[data-dialog-close]").forEach((btn) => {
      btn.addEventListener("click", (event) => {
        event.preventDefault();
        animateOut(dialog);
      });
    });

    dialog.addEventListener("click", (event) => {
      if (event.target === dialog) animateOut(dialog);
    });

    dialog.addEventListener("cancel", (event) => {
      event.preventDefault();
      animateOut(dialog);
    });

    dialog.addEventListener("close", () => {
      if (dialog._opener instanceof HTMLElement) {
        dialog._opener.focus();
      }
    });
  });
}
