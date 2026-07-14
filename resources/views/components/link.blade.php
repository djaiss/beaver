@props([
  'turbo' => true,
])

<a @if ($turbo) data-turbo="true" @endif {{
  $attributes->class([
    'inline underline',
    'underline-offset-4',
    'hover:decoration-[1.15px]',
    'decoration-hairline',
    'text-body',
    'hover:text-brand hover:decoration-brand',
    'transition-colors duration-200',
  ])
}}>{{ $slot }}</a>
