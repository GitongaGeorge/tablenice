@if(config('tablenice.include_assets'))
    @push('styles')
        <script src="{{ config('tablenice.tailwind_cdn') }}"></script>
    @endpush
    
    @push('scripts')
        <script defer src="{{ config('tablenice.alpine_cdn') }}"></script>
    @endpush
@endif

<div class="w-full">
    @include('tablenice::components.search')
    @include('tablenice::components.table')
    @include('tablenice::components.pagination')
</div>