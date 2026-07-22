// The Suspiciously Accurate Pricing Calculator. Every slider, toggle and option feeds an
// itemized estimate that, no matter how you drag it, always resolves to exactly $49.
// That is the joke, and also the entire business model.
export default () => ({
  items: 4200,
  collections: 12,
  members: 3,
  storage: 40,
  chaos: 60,
  giveups: 4,
  raccoons: 1,
  cloudBackups: true,
  countsShelf: true,
  labelMaker: false,
  namedItems: false,
  enthusiasm: 'Rabid',

  enthusiasmOptions: ['Casual', 'Keen', 'Rabid', 'Unhinged'],

  reset() {
    Object.assign(this, {
      items: 4200,
      collections: 12,
      members: 3,
      storage: 40,
      chaos: 60,
      giveups: 4,
      raccoons: 1,
      cloudBackups: true,
      countsShelf: true,
      labelMaker: false,
      namedItems: false,
      enthusiasm: 'Rabid',
    });
  },

  fmt(n) {
    return Number(n).toLocaleString('en-US');
  },

  get itemsDisplay() {
    return this.fmt(this.items) + ' items';
  },

  get storageDisplay() {
    return this.fmt(this.storage) + ' GB';
  },

  get giveupsDisplay() {
    return this.fmt(this.giveups) + '×';
  },

  get raccoonsDisplay() {
    return this.raccoons === 1 ? '1 raccoon' : this.raccoons + ' raccoons';
  },

  get raccoonSurcharge() {
    return this.raccoons > 0 ? '+$' + this.raccoons * 3 + '.00' : '$0.00';
  },

  get lineItems() {
    return [
      { label: 'Base license (one time)', value: '$49.00' },
      { label: this.fmt(this.items) + ' items × $0.00', value: '$0.00' },
      { label: this.members + ' team member' + (this.members === 1 ? '' : 's') + ', unlimited', value: 'included' },
      { label: this.fmt(this.storage) + ' GB storage', value: 'included' },
      { label: 'Shelf-chaos handling fee (' + this.chaos + '%)', value: 'waived' },
      { label: 'Raccoon containment (' + this.raccoons + ')', value: this.raccoonSurcharge + ' → waived' },
      { label: 'Enthusiasm multiplier (' + this.enthusiasm + ')', value: '×1.00' },
      { label: 'Label-maker rebate', value: this.labelMaker ? '–$0.00' : '$0.00' },
    ];
  },

  get quip() {
    const quips = [
      'Math checks out. It always does.',
      'Our imaginary accountant approves.',
      'Suspiciously round. Beautifully flat.',
      'The algorithm has spoken. Loudly. $49.',
      'No matter how you slice the log: $49.',
    ];

    const index = (this.items + this.collections + this.members + this.raccoons + this.enthusiasmOptions.indexOf(this.enthusiasm)) % quips.length;

    return quips[index];
  },

  // Restart the pop animation on the total whenever any input changes. Reading every
  // field makes Alpine track them all as dependencies of this effect.
  pop($el) {
    void [
      this.items,
      this.collections,
      this.members,
      this.storage,
      this.chaos,
      this.giveups,
      this.raccoons,
      this.cloudBackups,
      this.countsShelf,
      this.labelMaker,
      this.namedItems,
      this.enthusiasm,
    ];

    $el.style.animation = 'none';
    $el.offsetHeight;
    $el.style.animation = '';
  },
});
