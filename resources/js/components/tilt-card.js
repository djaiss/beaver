// Tilts a card toward the pointer, with a glare that tracks it like light.
//
// The pointer is tracked on the outer element, which never transforms, so the
// card moving underneath cannot feed its own movement back into the reading and
// oscillate. The four values the stylesheet needs are written as custom
// properties: two rotations, and the glare position.
//
// `is-tilting` swaps in a short follow transition while the pointer moves so the
// tilt tracks it closely; dropping the class on leave lets the card ease back to
// flat over the longer return duration.

const MAX_ROTATION = 9;

export default () => ({
  init() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      return;
    }

    // The root and the card are held in the closure rather than on the Alpine
    // component, which would deep-proxy the DOM nodes for no benefit.
    const root = this.$el;
    const card = root.querySelector('.t-tilt-card');

    if (card === null) {
      return;
    }

    /*
     * Map the pointer to a rotation around the centre of the card. Moving right
     * turns the card to the right, moving down tips the top away, so the card
     * leans toward the pointer rather than away from it.
     */
    const track = (event) => {
      const bounds = root.getBoundingClientRect();

      if (bounds.width === 0 || bounds.height === 0) {
        return;
      }

      const x = (event.clientX - bounds.left) / bounds.width;
      const y = (event.clientY - bounds.top) / bounds.height;

      card.classList.add('is-tilting');
      root.style.setProperty('--tilt-ry', `${((x - 0.5) * 2 * MAX_ROTATION).toFixed(2)}deg`);
      root.style.setProperty('--tilt-rx', `${((0.5 - y) * 2 * MAX_ROTATION).toFixed(2)}deg`);
      root.style.setProperty('--tilt-gx', `${(x * 100).toFixed(2)}%`);
      root.style.setProperty('--tilt-gy', `${(y * 100).toFixed(2)}%`);
    };

    const reset = () => {
      root.classList.remove('is-hover');
      card.classList.remove('is-tilting');
      root.style.setProperty('--tilt-rx', '0deg');
      root.style.setProperty('--tilt-ry', '0deg');
    };

    root.addEventListener('pointerenter', () => root.classList.add('is-hover'));
    root.addEventListener('pointermove', track);
    root.addEventListener('pointerleave', reset);
    root.addEventListener('pointercancel', reset);
  },
});
