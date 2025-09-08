{{-- resources/views/livewire/components/form-modal.blade.php --}}
<div x-data="{
        show: $wire.entangle('show'),
        size: $wire.entangle('size'),
        sizeClasses: {
            'xs': 'max-w-xs',
            'sm': 'max-w-sm',
            'md': 'max-w-md',
            'lg': 'max-w-lg',
            'xl': 'max-w-xl',
            '2xl': 'max-w-2xl',
            '3xl': 'max-w-3xl',
            '4xl': 'max-w-4xl',
            '5xl': 'max-w-5xl',
            '6xl': 'max-w-6xl',
            '7xl': 'max-w-7xl'
        }
    }" 
    x-show="show" 
    x-on:keydown.escape.window="$wire.close()" 
    style="display: none;" 
    class="fixed inset-0 z-[100] overflow-y-auto">

    <div class="flex items-center justify-center min-h-screen px-4 py-6 text-center sm:px-6">
        
        <!-- Enhanced backdrop with better blur and animations -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0 backdrop-blur-none"
             x-transition:enter-end="opacity-100 backdrop-blur-md" 
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 backdrop-blur-md" 
             x-transition:leave-end="opacity-0 backdrop-blur-none"
             @class([
                'fixed inset-0 transition-all duration-300',
                $theme['backdropBg'] ?? 'bg-gray-900/40 backdrop-blur-md'
             ]) 
             @click="$wire.close()">
        </div>
        
        <!-- Enhanced modal container with better animations -->
        <div x-show="show" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-4 rotate-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0 rotate-0"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0 rotate-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-4 rotate-1"
             @class([
                'relative w-full overflow-hidden text-left align-middle transform transition-all duration-300 shadow-2xl ring-1 ring-black/5 dark:ring-white/10',
                $theme['modalContainer'] ?? 'rounded-2xl'
             ])
             :class="sizeClasses[size] || sizeClasses['2xl']" 
             @click.stop>
            
            <!-- Enhanced header with gradient and pattern -->
            <div @class([
                'relative flex items-center justify-between p-6 overflow-hidden',
                $theme['headerBgSolid'] ?? 'bg-slate-50 dark:bg-slate-700/50'
            ])>
                <!-- Background pattern/decoration -->
                <div @class([
                    'absolute inset-0 opacity-10',
                    $theme['headerPattern'] ?? ''
                ])></div>
                
                <!-- Gradient overlay for depth -->
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/5 to-transparent"></div>
                
                <h3 @class([
                    'relative text-lg font-semibold leading-6 tracking-tight',
                     isset($theme['headerBgSolid']) ? 'text-white drop-shadow-sm' : 'text-gray-900 dark:text-white'
                ])>
                    {{ $title }}
                </h3>
                
                <button @click="$wire.close()" 
                        @class([
                    'relative transition-all duration-200 rounded-lg p-1.5 hover:scale-110',
                    isset($theme['headerBgSolid']) ? 'text-white/70 hover:text-white hover:bg-white/10' : 'text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700'
                ])>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Enhanced modal body with theme support and visual interest -->
            <div @class([
                'relative overflow-hidden',
                $theme['modalBodyBg'] ?? 'bg-white dark:bg-slate-800'
            ])>
                <!-- Background decorative elements -->
                <div @class([
                    'absolute inset-0 opacity-30',
                    $theme['modalBodyPattern'] ?? ''
                ])></div>
                
                <!-- Subtle gradient overlay -->
                <div @class([
                    'absolute inset-0',
                    $theme['modalBodyOverlay'] ?? 'bg-gradient-to-br from-transparent via-white/10 to-transparent dark:via-slate-700/10'
                ])></div>
                
                <!-- Content container with better spacing and backdrop -->
                <div @class([
                    'relative p-6 backdrop-blur-sm',
                    $theme['modalBodyContainer'] ?? ''
                ])>
                    @if($component)
                        <div @class([
                            'relative rounded-xl p-4 shadow-inner',
                            $theme['modalContentBg'] ?? 'bg-white/50 dark:bg-slate-900/30 backdrop-blur-sm border border-white/20 dark:border-slate-700/50'
                        ])>
                            @livewire($component, $params, key($component . now()))
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Enhanced footer with theme support -->
            @if(isset($showFooter) && $showFooter)
            <div @class([
                'relative flex items-center justify-between px-6 py-4 border-t backdrop-blur-sm',
                $theme['modalFooterBg'] ?? 'bg-gray-50/80 dark:bg-slate-700/50 border-gray-200 dark:border-slate-600'
            ])>
                <!-- Footer gradient overlay -->
                <div @class([
                    'absolute inset-0',
                    $theme['modalFooterOverlay'] ?? 'bg-gradient-to-r from-transparent via-white/5 to-transparent'
                ])></div>
                
                <div class="relative flex items-center space-x-3">
                    @if(isset($footerContent))
                        {!! $footerContent !!}
                    @endif
                </div>
                
                <div class="relative flex items-center space-x-3">
                    @if(isset($footerActions))
                        {!! $footerActions !!}
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>