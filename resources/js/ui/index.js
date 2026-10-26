import { initDialogs } from "./dialog.js";
import { initDropdowns } from "./dropdown.js";
import { initToasts } from "./toast.js";
import { initForms } from "./form.js";
import { initFileDrops } from "./file-drop.js";
import { initNav } from "./nav.js";
import { initDelegations } from "./delegations.js";

export function initUi() {
  initDialogs();
  initDropdowns();
  initToasts();
  initForms();
  initFileDrops();
  initNav();
  initDelegations();
}
