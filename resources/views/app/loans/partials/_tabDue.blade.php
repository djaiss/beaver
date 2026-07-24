{{-- The Due & overdue tab: overdue, due soon, and open-ended, top to bottom. --}}
<div class="flex flex-col gap-5">
  @include('app.loans.partials._group', [
    'title' => __('Overdue'),
    'description' => __('past due date · from the overdue check'),
    'dot' => 'bg-error',
    'loans' => $tabData['overdue'],
    'emptyMessage' => __('Nothing here — nice.'),
  ])

  @include('app.loans.partials._group', [
    'title' => __('Due soon'),
    'description' => __('within 30 days'),
    'dot' => 'bg-badge-orange',
    'loans' => $tabData['dueSoon'],
    'emptyMessage' => __('Nothing here — nice.'),
  ])

  @include('app.loans.partials._group', [
    'title' => __('Open-ended'),
    'description' => __('active, no due date set'),
    'dot' => 'bg-muted',
    'loans' => $tabData['openEnded'],
    'emptyMessage' => __('Nothing here — nice.'),
  ])
</div>
