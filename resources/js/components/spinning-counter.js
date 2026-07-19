// Spins each digit of a number up to its value, like a mechanical reel.
//
// The element renders its final value as plain text on the server, so a visitor
// without JavaScript, or one who asked for reduced motion, reads the number
// straight away. On init the text is swapped for one clipped column per digit,
// each holding a strip of 0-9 cells that is translated up by (spins * 10 +
// digit) cells to spin and then land.
//
// The motion streak is a vertical-only SVG blur rather than the CSS blur()
// filter, which would smear the digits sideways as well. Each column owns its
// own filter so the blur can decay to zero as that column settles.

const SPINS = 3;
const STAGGER = 90;
const DURATION = 1400;
const MAX_BLUR = 3;

let uid = 0;

export default () => ({
  init() {
    const value = this.$el.textContent.trim();

    if (value === '' || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    // The cells have to line up with the type the number was already rendered
    // in, so the reel is measured from the element itself rather than a fixed
    // size in the stylesheet.
    const cell = Math.round(parseFloat(window.getComputedStyle(this.$el).lineHeight) || 30);

    this.$el.style.setProperty('--reel-cell', `${cell}px`);
    this.$el.classList.add('t-reel');
    this.$el.setAttribute('aria-label', value);
    this.$el.textContent = '';

    const strips = this.build(value, cell);

    // A frame at the starting offset before the transition is attached, so the
    // browser has something to animate away from.
    requestAnimationFrame(() => strips.forEach((strip) => this.spin(strip, cell)));
  },

  /*
   * Build one column per character. Digits get a spinning strip, anything else
   * (a currency symbol, a separator, a percent sign) is a static cell that the
   * reel travels past.
   */
  build(value, cell) {
    const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('width', '0');
    svg.setAttribute('height', '0');
    svg.setAttribute('aria-hidden', 'true');
    svg.style.position = 'absolute';
    svg.appendChild(defs);

    const strips = [];
    let column = 0;

    for (const character of value) {
      if (!/[0-9]/.test(character)) {
        const separator = document.createElement('span');
        separator.className = 't-reel-digit';
        separator.setAttribute('aria-hidden', 'true');
        this.$el.appendChild(separator);
        separator.textContent = character;
        continue;
      }

      const id = `reel-blur-${uid++}`;
      const blur = document.createElementNS('http://www.w3.org/2000/svg', 'feGaussianBlur');
      blur.setAttribute('stdDeviation', `0 ${MAX_BLUR}`);

      const filter = document.createElementNS('http://www.w3.org/2000/svg', 'filter');
      filter.setAttribute('id', id);
      filter.appendChild(blur);
      defs.appendChild(filter);

      const strip = document.createElement('div');
      strip.className = 't-reel-strip';
      strip.style.filter = `url(#${id})`;

      const cells = SPINS * 10 + Number(character);

      for (let index = 0; index <= cells; index++) {
        const digit = document.createElement('div');
        digit.className = 't-reel-digit';
        digit.textContent = String(index % 10);
        strip.appendChild(digit);
      }

      const wrapper = document.createElement('div');
      wrapper.className = 't-reel-col';
      wrapper.setAttribute('aria-hidden', 'true');
      wrapper.appendChild(strip);
      this.$el.appendChild(wrapper);

      strips.push({ strip, blur, cells, delay: column * STAGGER });
      column++;
    }

    this.$el.appendChild(svg);

    return strips;
  },

  /*
   * Tween one column: translate the strip up to its landing cell, and decay its
   * blur to zero over the same window so the streak clears as the reel stops.
   */
  spin({ strip, blur, cells, delay }, cell) {
    strip.style.transition = `transform ${DURATION}ms var(--reel-ease) ${delay}ms`;
    strip.style.transform = `translateY(-${cells * cell}px)`;

    const started = performance.now() + delay;

    const decay = (now) => {
      const progress = Math.min(1, Math.max(0, (now - started) / DURATION));

      blur.setAttribute('stdDeviation', `0 ${(MAX_BLUR * (1 - progress)).toFixed(2)}`);

      if (progress < 1) {
        requestAnimationFrame(decay);
        return;
      }

      // The filter is dropped once the reel has landed so the digits render
      // crisply rather than through a no-op blur.
      strip.style.filter = '';
    };

    requestAnimationFrame(decay);
  },
});
