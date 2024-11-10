@props(['class' => ''])

<svg {{ $attributes->merge(['class' => $class]) }} xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="none" viewBox="0 0 100 100">
  <path d="M0 0h100v100H0z"/>
  <rect width="20" height="20" x="51" y="29.5" fill="currentColor" rx="2.5"/>
  <rect width="20" height="20" x="28" y="29" fill="currentColor" rx="2.5"/>
  <rect width="20" height="20" x="28" y="51.5" fill="currentColor" rx="2.5"/>
  <rect width="20" height="20" x="51" y="51.5" fill="currentColor" rx="2.5"/>
</svg>
