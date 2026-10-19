import { initDialogs } from "./dialog.js";
import { initDropdowns } from "./dropdown.js";
import { initToasts } from "./toast.js";
import { initForms, initMotionToggle } from "./form.js";
import { initFileDrops } from "./file-drop.js";
import { initNav } from "./nav.js";

export function initUi() {
  initDialogs();
  initDropdowns();
  initToasts();
  initForms();
  initMotionToggle();
  initFileDrops();
  initNav();
}
