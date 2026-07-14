import 'instant.page';

// --- Turbo Drive ---
import * as Turbo from '@hotwired/turbo';
window.Turbo = Turbo;
Turbo.session.drive = false; // explicit (enabled by default)

// --- Alpine ---
import Alpine from 'alpinejs';
import ajax from '@imacrayon/alpine-ajax';
import Popover from './components/popover';
import RelationshipTypeSorter from './components/relationship-type-sorter';

window.Alpine = Alpine;
Alpine.plugin(ajax);
Alpine.data('popover', Popover);
Alpine.data('relationshipTypeSorter', RelationshipTypeSorter);

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

// If you need page-specific teardown, you can hook before Turbo renders the new DOM:
// addEventListener('turbo:before-render', (event) => { /* cleanup here */ });
