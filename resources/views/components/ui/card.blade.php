@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->merge(['class' => 'bg-card border border-border rounded-lg shadow-sm']) }}>
    @if ($title)
        <div class="p-6 border-b border-border">
            <h3 class="text-lg">{{ $title }}</h3>
            @if($subtitle)
                <p class="text-sm text-muted-foreground mt-1">{{ $subtitle }}</p>
            @endif
        </div>
    @endif
    <div class="p-6">{{ $slot }}</div>
</div>
