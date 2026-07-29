// Scripts for the logged-in application. Turbo, Alpine and the theme store come
// from base.js, which the marketing bundle imports too.

import Alpine from './base';

import morph from '@alpinejs/morph';
import ajax from '@imacrayon/alpine-ajax';
import SpinningCounter from './components/spinning-counter';
import TiltCard from './components/tilt-card';
import TypeSchemaValidator from './components/type-schema-validator';

Alpine.plugin(morph);
Alpine.plugin(ajax);
// Morph preserves DOM/Alpine state for elements that persist between requests,
// instead of discarding and recreating them — needed so a form nested inside
// its own x-target region stays connected (and keeps firing ajax:* events)
// after the region it lives in gets updated.
ajax.configure({ mergeStrategy: 'morph' });
Alpine.data('spinningCounter', SpinningCounter);
Alpine.data('tiltCard', TiltCard);
Alpine.data('typeSchemaValidator', TypeSchemaValidator);

// --- Collection items view switching ---
// Persists the chosen view (grid/list/table) for the current user in the background. Grid and
// list share the same page chrome, so they swap instantly. The table view uses a different
// chrome (the sidebar moves to a top bar), so crossing into or out of it needs a reload for the
// server to render the right shell.
window.switchCatalogView = (component, target) => {
  const url = document.getElementById('collection-view-endpoint')?.value;
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  const persisted = url
    ? fetch(url, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-TOKEN': token,
        },
        body: JSON.stringify({ view: target }),
      }).catch(() => {})
    : Promise.resolve();

  const crossesTable = (target === 'table') !== (component.serverView === 'table');

  if (crossesTable) {
    // Wait for the preference to save before reloading — reloading first would
    // cancel the in-flight request and the server would render the old view.
    persisted.finally(() => window.location.reload());
    return;
  }

  component.view = target;
};

// --- Photos view switching ---
// Both layouts are rendered on the page, so the switch itself is instant and this only
// saves the choice for the current user, in the background. Failing to save is not worth
// interrupting anyone over: the layout still changed, it just will not be remembered.
window.switchPhotoView = (target) => {
  const url = document.getElementById('photos-view-endpoint')?.value;

  if (!url) {
    return;
  }

  fetch(url, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
    body: JSON.stringify({ view: target }),
  }).catch(() => {});
};

// --- Dashboard sections ---
// The whole dashboard is on the page, so showing and hiding a block is instant and this only
// saves which blocks the current user turned off, in the background. Failing to save is not
// worth interrupting anyone over: the screen still changed, it just will not be remembered.
window.saveDashboardSections = (hidden) => {
  const url = document.getElementById('dashboard-sections-endpoint')?.value;

  if (!url) {
    return;
  }

  fetch(url, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
    },
    body: JSON.stringify({ sections: hidden }),
  }).catch(() => {});
};

// --- Upload size guard ---
// Returns the files from `fileList` that are larger than maxKilobytes, so an
// upload form can reject them in the browser and show a friendly message rather
// than letting an oversized request hit the server and bounce off nginx.
window.oversizedFiles = (fileList, maxKilobytes) => Array.from(fileList ?? []).filter((file) => file.size > maxKilobytes * 1024);

// Turbo morph refreshes diff against the server HTML, which reverts the runtime
// DOM changes Alpine makes (x-show/x-cloak). That reopens toggles like the sidebar
// user menu. Skip morphing anything marked data-morph-skip so its state survives.
// This only fires on morph refreshes, so full navigations still update normally.
addEventListener('turbo:before-morph-element', (event) => {
  if (event.target.matches?.('[data-morph-skip]')) {
    event.preventDefault();
  }
});

// Morph refreshes patch the toast DOM without re-running Alpine on it, so a
// fresh toast keeps its stale state and never starts its auto-hide timer.
// Rebuild the Alpine tree for just that island after every morph.
addEventListener('turbo:morph', () => {
  const notifications = document.getElementById('notifications');
  if (notifications && window.Alpine?.initTree) {
    Alpine.destroyTree(notifications);
    Alpine.initTree(notifications);
  }
});

// If you need page-specific teardown, you can hook before Turbo renders the new DOM:
// addEventListener('turbo:before-render', (event) => { /* cleanup here */ });
