/*
 * The Suspiciously Accurate Pricing Calculator. Every slider, toggle and option feeds an
 * itemized estimate that, no matter how you drag it, always resolves to exactly $49.
 * That is the joke, and also the entire business model.
 *
 * Every user-facing string is passed in from Blade so it goes through __(), placeholders
 * and all, which is why nothing here concatenates sentences. The enthusiasm levels stay
 * English in the state and are only translated on the way out, so the comparisons in the
 * markup keep working in every locale.
 */
const fill = (template, replacements) =>
  Object.entries(replacements).reduce((text, [key, value]) => text.replaceAll(`:${key}`, value), template);

export default ({ locale, enthusiasms, labels, quips }) => ({
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

  enthusiasmOptions: Object.keys(enthusiasms),

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
    return Number(n).toLocaleString(locale);
  },

  get itemsDisplay() {
    return fill(labels.items, { count: this.fmt(this.items) });
  },

  get storageDisplay() {
    return fill(labels.storage, { count: this.fmt(this.storage) });
  },

  get giveupsDisplay() {
    return this.fmt(this.giveups) + '×';
  },

  get raccoonsDisplay() {
    return this.raccoons === 1 ? labels.raccoonOne : fill(labels.raccoonMany, { count: this.raccoons });
  },

  get raccoonSurcharge() {
    return this.raccoons > 0 ? '+$' + this.raccoons * 3 + '.00' : '$0.00';
  },

  get lineItems() {
    return [
      { label: labels.baseLicense, value: '$49.00' },
      { label: fill(labels.itemsLine, { count: this.fmt(this.items) }), value: '$0.00' },
      {
        label: this.members === 1 ? labels.memberOne : fill(labels.memberMany, { count: this.members }),
        value: labels.included,
      },
      { label: fill(labels.storageLine, { count: this.fmt(this.storage) }), value: labels.included },
      { label: fill(labels.chaosLine, { percent: this.chaos }), value: labels.waived },
      {
        label: fill(labels.raccoonLine, { count: this.raccoons }),
        value: fill(labels.surchargeWaived, { amount: this.raccoonSurcharge }),
      },
      { label: fill(labels.enthusiasmLine, { level: enthusiasms[this.enthusiasm] }), value: '×1.00' },
      { label: labels.rebateLine, value: this.labelMaker ? '–$0.00' : '$0.00' },
    ];
  },

  get quip() {
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
