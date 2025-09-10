{{-- This file should be included in the

<head> of the user's main layout file --}}
    <script>
        // This script block ensures that core libraries and Alpine.js stores are available globally
        // before the rest of the application's JavaScript is executed. This prevents race conditions.

        // Make JS libraries available on the window object for Alpine components
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart !== 'undefined') window.Chart = Chart;
            if (typeof Litepicker !== 'undefined') window.Litepicker = Litepicker;
            if (typeof dayjs !== 'undefined') window.dayjs = dayjs;
        });

        document.addEventListener('alpine:init', () => {
            // Global store for theme management
            Alpine.store('theme', {
                isDark: document.documentElement.classList.contains('dark'),
                toggle() {
                    this.isDark = !this.isDark;
                    document.documentElement.classList.toggle('dark', this.isDark);
                    localStorage.setItem('color-theme', this.isDark ? 'dark' : 'light');
                    window.dispatchEvent(new CustomEvent('theme-changed'));
                }
            });

            // Global store for tooltips
            Alpine.store('tooltip', {
                visible: false,
                content: '',
                element: null,
                popperInstance: null,
                show(el, content) {
                    if (this.element === el) return;
                    this.hide();
                    this.element = el;
                    this.content = content;
                    this.visible = true;
                    Alpine.nextTick(() => {
                        const tooltipEl = document.querySelector('#global-tooltip');
                        if (tooltipEl && typeof Popper !== 'undefined') {
                            this.popperInstance = Popper.createPopper(el, tooltipEl, {
                                placement: 'top',
                                modifiers: [{ name: 'offset', options: { offset: [0, 8] } }],
                            });
                        }
                    });
                },
                hide() {
                    this.visible = false;
                    this.element = null;
                    if (this.popperInstance) {
                        this.popperInstance.destroy();
                        this.popperInstance = null;
                    }
                }
            });

            // Global store for clipboard functionality
            Alpine.store('clipboard', {
                copy(text, successCallback = () => { }) {
                    if (!text) return;
                    const textarea = document.createElement('textarea');
                    textarea.value = text;
                    textarea.style.position = 'fixed';
                    textarea.style.opacity = 0;
                    document.body.appendChild(textarea);
                    textarea.select();
                    try {
                        document.execCommand('copy');
                        if (successCallback && typeof successCallback === 'function') {
                            successCallback();
                        }
                    } catch (err) {
                        console.error('Unable to copy to clipboard', err);
                    }
                    document.body.removeChild(textarea);
                }
            });
        });
    </script>