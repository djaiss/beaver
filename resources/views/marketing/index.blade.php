<x-marketing-layout :announcement="$announcement" :footer-columns="$footerColumns">
  @include('marketing.home.hero', ['githubStats' => $githubStats])
  @include('marketing.home.dashboard', ['dashboard' => $dashboard])
  @include('marketing.home.trust', ['trustCards' => $trustCards])
  @include('marketing.home.organize', ['organizeFeatures' => $organizeFeatures])
  @include('marketing.home.types', ['itemTypes' => $itemTypes])
  @include('marketing.home.copies', ['copies' => $copies, 'copyFields' => $copyFields])
  @include('marketing.home.supported', ['supported' => $supported])
  @include('marketing.home.openSource', ['openSourcePoints' => $openSourcePoints, 'githubStats' => $githubStats])
  @include('marketing.home.pricing', ['selfHostFeatures' => $selfHostFeatures, 'cloudFeatures' => $cloudFeatures])
  @include('marketing.home.roadmap', ['shipped' => $shipped, 'coming' => $coming])
  @include('marketing.home.faq', ['faqs' => $faqs])
  @include('marketing.home.cta')
</x-marketing-layout>
