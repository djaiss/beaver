// Copy a block of text and say so for a couple of seconds. Used by the media kit, where
// every piece of boilerplate is there to be pasted into an article. Most callers hand the
// text over up front; the press address is only known at click time, once the script that
// carries it has filled the link in, so copy() takes it then instead.
//
// navigator.clipboard only exists in a secure context (https or localhost), so an
// instance served over plain http falls back to a hidden textarea.
export default (source = null) => ({
  copied: false,
  timer: null,

  async copy(clicked = null) {
    const text = clicked ?? source;

    try {
      await navigator.clipboard.writeText(text);
    } catch (error) {
      const area = document.createElement('textarea');
      area.value = text;
      area.style.position = 'fixed';
      area.style.opacity = '0';
      document.body.appendChild(area);
      area.select();
      document.execCommand('copy');
      area.remove();
    }

    this.copied = true;
    clearTimeout(this.timer);
    this.timer = setTimeout(() => (this.copied = false), 2000);
  },
});
