// Scripts for the public site. Turbo, Alpine and the theme store come from
// base.js, which the application bundle imports too.

import Alpine from './base';

import CopyToClipboard from './components/copy-to-clipboard';
import Faq from './components/faq';
import PricingCalculator from './components/pricing-calculator';

Alpine.data('copyToClipboard', CopyToClipboard);
Alpine.data('faq', Faq);
Alpine.data('pricingCalculator', PricingCalculator);

// navigator.clipboard only exists in secure contexts (https or localhost), so
// fall back to a hidden textarea everywhere else. Shared by the API reference
// and the documentation portal's "Copy for LLM" buttons.
window.docsCopy = function docsCopy(text) {
  if (window.isSecureContext && navigator.clipboard) {
    return navigator.clipboard.writeText(text);
  }

  const textarea = document.createElement('textarea');
  textarea.value = text;
  textarea.style.position = 'fixed';
  textarea.style.opacity = '0';
  document.body.appendChild(textarea);
  textarea.select();
  document.execCommand('copy');
  textarea.remove();

  return Promise.resolve();
};
