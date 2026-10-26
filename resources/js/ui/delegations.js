/**
 * Global event delegations to replace inline handlers for CSP compliance.
 */
export function initDelegations() {
  // 1. Konfirmasi submit form (data-confirm="Pesan?")
  document.addEventListener('submit', (e) => {
    const form = e.target.closest('form[data-confirm]');
    if (form) {
      const msg = form.dataset.confirm || 'Apakah kamu yakin?';
      if (!window.confirm(msg)) {
        e.preventDefault();
        e.stopPropagation();
      }
    }
  });

  // 2. Auto-submit filter & Toggle fields
  document.addEventListener('change', (e) => {
    if (e.target.matches('[data-autosubmit]')) {
      if (e.target.form) {
        if (typeof e.target.form.requestSubmit === 'function') {
          e.target.form.requestSubmit();
        } else {
          e.target.form.submit();
        }
      }
    }

    if (e.target.matches('[data-interview-mode-toggle]')) {
      const mode = e.target.value;
      const meetingField = document.getElementById('field-meeting-url');
      const locationField = document.getElementById('field-location');
      if (meetingField) meetingField.style.display = mode === 'online' ? '' : 'none';
      if (locationField) locationField.style.display = mode === 'onsite' ? '' : 'none';
    }
  });

  // 3. Salin tautan & reload halaman
  document.addEventListener('click', (e) => {
    const copyBtn = e.target.closest('[data-copy]');
    if (copyBtn) {
      const text = copyBtn.dataset.copy;
      if (text && navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
          window.karirti?.showToast?.('Tautan disalin!', 'mint');
        });
      }
    }

    const copyUrlBtn = e.target.closest('[data-copy-current-url]');
    if (copyUrlBtn) {
      if (navigator.clipboard) {
        navigator.clipboard.writeText(window.location.href).then(() => {
          window.karirti?.showToast?.('Tautan disalin!', 'mint');
        });
      }
    }

    const reloadBtn = e.target.closest('[data-reload]');
    if (reloadBtn) {
      window.location.reload();
    }

    if (e.target.closest('[data-try-error]')) {
      const input = document.querySelector('[name=e09_email]');
      if (input) {
        input.value = '';
        input.dispatchEvent(new Event('invalid'));
      }
    }

    const pipelineBtn = e.target.closest('[data-set-pipeline-status]');
    if (pipelineBtn) {
      const status = pipelineBtn.dataset.setPipelineStatus;
      const pipelineEl = document.querySelector('#e17-demo > div');
      if (pipelineEl) {
        pipelineEl.dataset.status = status;
        window.karirti?.replayPipeline?.(pipelineEl);
      }
    }
  });

  document.addEventListener('submit', (e) => {
    if (e.target.matches('[data-validate-demo]')) {
      e.preventDefault();
      const el = document.getElementById('e09-success');
      if (el) {
        el.hidden = false;
        setTimeout(() => (el.hidden = true), 3000);
      }
    }
  });
}
