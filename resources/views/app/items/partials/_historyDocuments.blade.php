{{--
  The documents about the copy as a whole: the paperwork that belongs to the
  object itself rather than to one transaction or valuation. Documents tied to a
  specific record live in that record's own panel, and show up there.
--}}

@php
  $user = auth()->user();
  $canManage = $user->account->allowsManagementBy($user);
@endphp

<div>
  <div class="mb-5">
    <div class="flex items-center gap-2">
      <p class="text-lg font-semibold text-ink">{{ __('Documents') }}</p>
      <x-help id="history.documents" />
    </div>
    <p class="mt-1 max-w-xl text-[13px] leading-relaxed text-muted">{{ __('Files and links about this copy as a whole: certificates, provenance paperwork, photographs. Documents that belong to a transaction, a valuation or another record are attached there instead.') }}</p>
  </div>

  @include('app.items.partials._documentsFor', [
      'documentable' => $selectedCopy,
      'catalog' => $catalog,
      'item' => $item,
      'selectedCopy' => $selectedCopy,
      'canManage' => $canManage,
  ])
</div>
