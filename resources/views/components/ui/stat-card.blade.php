@props(['label', 'value', 'hint' => null])

<div class="bg-card border border-border rounded-lg p-5 shadow-sm">
    <p class="text-sm text-muted-foreground">{{ $label }}</p>
    <p class="text-3xl mt-1">{{ $value }}</p>
    @if($hint)
        <p class="text-xs text-muted-foreground mt-2">{{ $hint }}</p>
    @endif
</div>
