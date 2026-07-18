/*
 * Tells you what is wrong with a pasted collection type schema before you
 * submit it. A deliberately shallow mirror of the ImportCollectionType action:
 * the server still validates the whole document from scratch, and its errors
 * are what actually block the import.
 *
 * Every user-facing string is passed in from Blade so it goes through __(),
 * placeholders and all, which is why nothing here concatenates sentences.
 */
const fill = (template, replacements) =>
  Object.entries(replacements).reduce((text, [key, value]) => text.replaceAll(`:${key}`, value), template);

export default ({ sample, fieldTypes, maxLength, labels }) => ({
  json: '',
  sample,

  init() {
    this.json = this.$refs.editor.value;
  },

  get result() {
    const text = this.json.trim();

    if (text === '') {
      return { status: 'idle', errors: [], summary: null };
    }

    if (text.length > maxLength) {
      return this.failed(labels.tooLarge);
    }

    let document;

    try {
      document = JSON.parse(text);
    } catch (exception) {
      return this.failed(fill(labels.invalidJson, { message: exception.message }));
    }

    if (!this.isObject(document)) {
      return this.failed(labels.rootMustBeObject);
    }

    if (!Number.isInteger(document.schemaVersion)) {
      return this.failed(labels.missingSchemaVersion);
    }

    if (!this.isObject(document.type)) {
      return this.failed(labels.missingType);
    }

    return this.readType(document.type);
  },

  readType(type) {
    const errors = [];

    if (!this.isNamed(type.name)) {
      errors.push(fill(labels.needsName, { label: labels.theType }));
    }

    const groups = type.groups ?? [];
    let fields = 0;

    if (!Array.isArray(groups)) {
      errors.push(labels.groupsMustBeArray);
    } else {
      groups.forEach((group, index) => {
        const fallback = fill(labels.group, { position: index + 1 });

        if (!this.isObject(group)) {
          errors.push(fill(labels.mustBeObject, { label: fallback }));
          return;
        }

        if (!this.isNamed(group.name)) {
          errors.push(fill(labels.needsName, { label: fallback }));
        }

        fields += this.readFields(group.fields, this.isNamed(group.name) ? group.name : fallback, errors);
      });
    }

    fields += this.readFields(type.standaloneFields, labels.standaloneFields, errors);

    if (errors.length > 0) {
      return { status: 'error', errors, summary: null };
    }

    return {
      status: 'valid',
      errors: [],
      summary: {
        name: type.name,
        color: /^#[0-9A-Fa-f]{6}$/.test(type.color ?? '') ? type.color : '#6B7280',
        groups: groups.length,
        fields,
      },
    };
  },

  readFields(list, label, errors) {
    if (list === undefined || list === null) {
      return 0;
    }

    if (!Array.isArray(list)) {
      errors.push(fill(labels.fieldsMustBeArray, { label }));
      return 0;
    }

    list.forEach((field, index) => {
      const fallback = fill(labels.field, { label, position: index + 1 });
      const named = this.isObject(field) && this.isNamed(field.name);
      const name = named ? field.name : fallback;

      if (!named) {
        errors.push(fill(labels.needsName, { label: name }));
      }

      if (!fieldTypes.includes(field?.type)) {
        errors.push(fill(labels.unknownType, { label: name, types: fieldTypes.join(', ') }));
      }

      if (field?.type === 'select' && (!Array.isArray(field.options) || field.options.length === 0)) {
        errors.push(fill(labels.needsOptions, { label: name }));
      }
    });

    return list.length;
  },

  isObject(value) {
    return typeof value === 'object' && value !== null && !Array.isArray(value);
  },

  isNamed(value) {
    return typeof value === 'string' && value.trim() !== '';
  },

  failed(message) {
    return { status: 'error', errors: [message], summary: null };
  },

  get status() {
    return this.result.status;
  },

  get errors() {
    return this.result.errors;
  },

  get summary() {
    return this.result.summary ?? { name: '', color: '#6B7280', groups: 0, fields: 0 };
  },

  get charLabel() {
    return this.json.length === 0 ? labels.empty : fill(labels.chars, { count: this.json.length });
  },

  get statusTitle() {
    if (this.status === 'idle') {
      return labels.idleTitle;
    }

    if (this.status === 'valid') {
      return labels.validTitle;
    }

    return this.errors.length === 1 ? labels.oneProblem : fill(labels.manyProblems, { count: this.errors.length });
  },

  get editorBorderClass() {
    return { idle: 'border-hairline', error: 'border-error/50', valid: 'border-success/50' }[this.status];
  },

  get statusBoxClass() {
    return { idle: 'border-hairline', error: 'border-error/40 bg-error/5', valid: 'border-success/40 bg-success/5' }[this.status];
  },

  get statusIconClass() {
    return { idle: 'bg-card text-muted-soft', error: 'bg-error/15 text-error', valid: 'bg-success/15 text-success' }[this.status];
  },

  get statusTitleClass() {
    return { idle: 'text-muted', error: 'text-error', valid: 'text-success' }[this.status];
  },
});
