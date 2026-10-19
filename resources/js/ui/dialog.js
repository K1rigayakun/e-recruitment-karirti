export function initDialogs(root = document) {
  root.querySelectorAll("[data-dialog-open]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const dialog = document.getElementById(btn.getAttribute("data-dialog-open"));
      if (!(dialog instanceof HTMLDialogElement)) return;
      dialog._opener = btn;
      dialog.showModal();
      const closeBtn = dialog.querySelector("[data-dialog-close]");
      (closeBtn instanceof HTMLElement ? closeBtn : dialog).focus();
    });
  });

  root.querySelectorAll("dialog.ui-dialog").forEach((dialog) => {
    if (!(dialog instanceof HTMLDialogElement)) return;

    dialog.querySelectorAll("[data-dialog-close]").forEach((btn) => {
      btn.addEventListener("click", () => dialog.close());
    });

    dialog.addEventListener("click", (event) => {
      if (event.target === dialog) dialog.close();
    });

    dialog.addEventListener("close", () => {
      if (dialog._opener instanceof HTMLElement) {
        dialog._opener.focus();
      }
    });
  });
}
