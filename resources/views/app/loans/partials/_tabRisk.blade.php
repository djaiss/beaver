{{-- The Risk & exceptions tab: the loans that need a second look, two per row. --}}
@php
  $cards = [
    ['title' => __('Overdue'), 'description' => __('Past the agreed return date.'), 'dot' => 'bg-error', 'loans' => $tabData['overdue']],
    ['title' => __('Lost'), 'description' => __('Never came back — needs a decision.'), 'dot' => 'bg-error', 'loans' => $tabData['lost']],
    ['title' => __('Returned worse'), 'description' => __('Condition-in is below condition-out.'), 'dot' => 'bg-badge-orange', 'loans' => $tabData['returnedWorse']],
    ['title' => __('No due date'), 'description' => __('Open-ended loans drift out of sight.'), 'dot' => 'bg-badge-orange', 'loans' => $tabData['noDueDate']],
    ['title' => __('Missing condition-out'), 'description' => __('No baseline to compare a return against.'), 'dot' => 'bg-muted', 'loans' => $tabData['missingConditionOut']],
    ['title' => __('Active, no documents'), 'description' => __('Lent out with nothing on file.'), 'dot' => 'bg-muted', 'loans' => $tabData['activeNoDocuments']],
  ];
@endphp

<div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
  @foreach ($cards as $card)
    @include('app.loans.partials._group', [
      'title' => $card['title'],
      'description' => $card['description'],
      'dot' => $card['dot'],
      'loans' => $card['loans'],
      'emptyMessage' => __('Clear.'),
    ])
  @endforeach
</div>
