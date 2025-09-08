{{-- resources/views/components/forms/fields/datetime-field.blade.php --}}
<div
    wire:key="{{ $field->getName() }}_{{ rand() }}"
    x-data="{
        value: @entangle('form_data.' . $field->getName()),
        instance: null,
        init() {
            Alpine.nextTick(() => {
                 this.instance = new Litepicker({
                    element: this.$refs.input,
                    singleMode: true,
                    allowRepick: true,
                    format: 'YYYY-MM-DD HH:mm',
                    plugins: ['mobilefriendly'],
                    timepicker: true,
                    setup: (picker) => {
                        picker.on('selected', (date) => {
                            this.value = dayjs(date.dateInstance).format('YYYY-MM-DD HH:mm');
                        });
                    }
                });

                if (this.value) {
                    this.instance.setDate(dayjs(this.value, 'YYYY-MM-DD HH:mm').toDate());
                }

                this.$watch('value', (newValue) => {
                    if(this.instance) {
                        this.instance.setDate(dayjs(newValue, 'YYYY-MM-DD HH:mm').toDate());
                    }
                });
            });
        }
    }"
    wire:ignore
>
    <label for="{{ $field->getName() }}_display" class="block text-sm font-medium text-gray-700 dark:text-slate-300">{{ $field->getLabel() }}</label>
    <div class="mt-1 relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
            <x-tablenice-icon name="heroicon-s-calendar-days" class="h-5 w-5 text-gray-400" />
        </div>
        <input 
            x-ref="input"
            type="text"
            id="{{ $field->getName() }}_display"
            @if($isViewOnly) disabled @endif
            @class([
                'block w-full sm:text-sm rounded-md shadow-sm bg-white/50 border-0 transition-all duration-200 focus:ring-2 dark:bg-slate-900/50 dark:text-slate-200 dark:placeholder-slate-400',
                'pl-10 pr-4 py-3',
                $theme['ring'] ?? 'focus:ring-indigo-500',
                'ring-1 ring-inset ring-gray-300 dark:ring-slate-700' => !$errors->has('form_data.' . $field->getName()),
                'ring-1 ring-inset ring-red-500' => $errors->has('form_data.' . $field->getName()),
            ])
            placeholder="YYYY-MM-DD HH:MM"
            x-on:change="value = $event.target.value"
            :value="value"
        >
    </div>
</div>

