@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
@php
    $baseStyle = 'display:inline-block;padding:14px 34px;font-weight:600;font-size:15px;text-decoration:none;border-radius:999px;letter-spacing:0.02em;text-transform:none;border:0;';
    $palettes = [
        'primary' => 'color:#FAFAFF;background-color:#EF4444;background-image:linear-gradient(120deg,#EF4444 0%,#F97316 50%,#EF4444 100%);',
        'success' => 'color:#FAFAFF;background-color:#10B981;background-image:linear-gradient(120deg,#10B981 0%,#34D399 100%);',
        'error' => 'color:#FAFAFF;background-color:#DC2626;background-image:linear-gradient(120deg,#DC2626 0%,#EF4444 100%);',
        'secondary' => 'color:#F4F4FA;background-color:transparent;border:1px solid #444459;',
    ];
    $buttonStyle = $baseStyle . ($palettes[$color] ?? $palettes['primary']);
@endphp
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener" style="{{ $buttonStyle }}">{{ $slot }}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
