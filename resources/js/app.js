import 'instant.page';

// --- Turbo Drive ---
import * as Turbo from '@hotwired/turbo';
window.Turbo = Turbo;
Turbo.session.drive = false; // explicit (enabled by default)

// --- Alpine ---
import Alpine from 'alpinejs';
import morph from '@alpinejs/morph';
import intersect from '@alpinejs/intersect';
import ajax from '@imacrayon/alpine-ajax';
import Popover from './components/popover';
import RelationshipTypeSorter from './components/relationship-type-sorter';
import TypeSchemaValidator from './components/type-schema-validator';

window.Alpine = Alpine;
Alpine.plugin(morph);
Alpine.plugin(intersect);
Alpine.plugin(ajax);
// Morph preserves DOM/Alpine state for elements that persist between requests,
// instead of discarding and recreating them — needed so a form nested inside
// its own x-target region stays connected (and keeps firing ajax:* events)
// after the region it lives in gets updated.
ajax.configure({ mergeStrategy: 'morph' });
Alpine.data('popover', Popover);
Alpine.data('relationshipTypeSorter', RelationshipTypeSorter);
Alpine.data('typeSchemaValidator', TypeSchemaValidator);

// --- Collection items view switching ---
// Persists the chosen view (grid/list/table) for the current user in the background. Grid and
// list share the same page chrome, so they swap instantly. The table view uses a different
// chrome (the sidebar moves to a top bar), so crossing into or out of it needs a reload for the
// server to render the right shell.
window.switchCollectionView = (component, target) => {
  const url = document.getElementById('collection-view-endpoint')?.value;
  const token = document.querySelector('meta[name="csrf-token"]')?.content;

  const persisted = url
    ? fetch(url, {
        method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
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

// --- Light/dark theme ---
// The initial class is set before paint by the inline script in partials/meta.
Alpine.store('theme', {
  dark: document.documentElement.classList.contains('dark'),
  toggle() {
    this.dark = !this.dark;
    document.documentElement.classList.toggle('dark', this.dark);
    try {
      localStorage.setItem('theme', this.dark ? 'dark' : 'light');
    } catch (e) {}
  },
});

// Start Alpine on the initial load (once)
document.addEventListener('DOMContentLoaded', () => {
  if (!document.documentElement.__alpined) {
    Alpine.start();
    document.documentElement.__alpined = true;
  }
});

// Re-initialize Alpine after every Turbo-driven navigation
addEventListener('turbo:load', () => {
  if (window.Alpine?.initTree) Alpine.initTree(document.body);
});

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
