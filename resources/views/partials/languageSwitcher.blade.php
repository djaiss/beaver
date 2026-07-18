<x-form method="put" :action="route('locale.update')" class="mx-auto max-w-xs">
  <x-select
    id="locale"
    :options="['en' => __('English'), 'fr_FR' => __('French'), 'es_ES' => __('Spanish'), 'de_DE' => __('German'), 'pt_BR' => __('Portuguese'), 'zh_CN' => __('Chinese'), 'ja_JP' => __('Japanese')]"
    selected="{{ app()->getLocale() }}"
    aria-label="{{ __('Language') }}"
    onchange="this.form.submit()"
  />
</x-form>
