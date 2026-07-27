// The public FAQ page: filtering the hundred questions as you type, and opening or
// closing them. Every question and answer is already in the page, so the filter reads
// the text straight off the DOM rather than carrying a second copy of it in JavaScript.
export default () => ({
  query: '',
  allOpen: false,
  open: {},
  // The component root. $el resolves to whichever element an expression is bound to,
  // so the one place it means "the whole page" is init(), and we keep it from there.
  root: null,

  init() {
    this.root = this.$el;
  },

  get term() {
    return this.query.trim().toLowerCase();
  },

  get isSearching() {
    return this.term !== '';
  },

  // A search of three characters or more opens what it finds, so the answers are
  // readable without a second click on each one.
  isOpen(key) {
    return this.open[key] ?? (this.allOpen || this.term.length > 2);
  },

  toggle(key) {
    this.open[key] = !this.isOpen(key);
  },

  toggleAll() {
    this.allOpen = !this.allOpen;
    this.open = {};
  },

  clear() {
    this.query = '';
  },

  matches(element) {
    if (!this.isSearching) {
      return true;
    }

    return element.textContent.toLowerCase().includes(this.term);
  },

  // A section is shown when at least one of its questions matches, so a section
  // never sits on the page with nothing under it.
  sectionMatches(element) {
    if (!this.isSearching) {
      return true;
    }

    return [...element.querySelectorAll('[data-faq-question]')].some((question) => this.matches(question));
  },

  matched() {
    if (!this.isSearching) {
      return 0;
    }

    return [...this.root.querySelectorAll('[data-faq-question]')].filter((question) => this.matches(question)).length;
  },

  get hasResults() {
    return !this.isSearching || this.matched() > 0;
  },
});
