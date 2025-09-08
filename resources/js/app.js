import { createPopper } from '@popperjs/core';
import Chart from 'chart.js/auto';
import 'trix';
import { Litepicker } from 'litepicker';
import 'litepicker/dist/plugins/mobilefriendly';
import dayjs from 'dayjs';
import customParseFormat from 'dayjs/plugin/customParseFormat';

// Make Litepicker and dayjs globally available
window.Litepicker = Litepicker;
window.dayjs = dayjs;
dayjs.extend(customParseFormat);


document.addEventListener('alpine:init', () => {
    // Create a global store for theme management
    Alpine.store('theme', {
        isDark: document.documentElement.classList.contains('dark'),

        toggle() {
            this.isDark = !this.isDark;
            document.documentElement.classList.toggle('dark', this.isDark);
            localStorage.setItem('color-theme', this.isDark ? 'dark' : 'light');
        }
    });

    // Tooltip Store
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
                if (tooltipEl) {
                    this.popperInstance = createPopper(el, tooltipEl, {
                        strategy: 'fixed',
                        placement: 'top',
                        modifiers: [
                            { name: 'offset', options: { offset: [0, 8] } },
                            { name: 'computeStyles', options: { gpuAcceleration: false } },
                        ],
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

    // --- START: CLIPBOARD STORE (THE DEFINITIVE FIX) ---
    // This store provides a simple, reliable way to copy text to the clipboard.
    Alpine.store('clipboard', {
        copy(text, successCallback = () => { }) {
            if (!text) return;

            // Use a temporary textarea to perform the copy command.
            // This is the most reliable method, especially in iFrames.
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = 0;
            document.body.appendChild(textarea);

            textarea.select();

            try {
                document.execCommand('copy');
                // Execute the callback on successful copy
                if (successCallback && typeof successCallback === 'function') {
                    successCallback();
                }
            } catch (err) {
                console.error('Unable to copy to clipboard', err);
            }

            document.body.removeChild(textarea);
        }
    });
    // --- END: CLIPBOARD STORE (THE DEFINITIVE FIX) ---

    // Chart Initializer
    Alpine.data('cardChart', (config) => ({
        chart: null,
        init() {
            Alpine.nextTick(() => {
                this.renderChart();
                this.$watch('$store.theme.isDark', () => {
                    this.updateChartColors();
                });
            });
        },
        getColors() {
            const isDark = Alpine.store('theme').isDark;
            return {
                gridColor: isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)',
                labelColor: isDark ? '#788891' : '#000000',
            };
        },
        renderChart() {
            const existingChart = Chart.getChart(this.$el);
            if (existingChart) {
                existingChart.destroy();
            }
            const { gridColor, labelColor } = this.getColors();
            let datasetBackgroundColor;
            const isPieOrDoughnut = ['pie', 'doughnut'].includes(config.type);
            if (isPieOrDoughnut && config.colors && typeof config.colors === 'object' && !Array.isArray(config.colors)) {
                datasetBackgroundColor = config.labels.map(label => config.colors[label] || '#6b7280');
            } else if (config.type === 'line') {
                datasetBackgroundColor = config.colors?.[0] || '#84cc16';
            } else if (config.type === 'bar') {
                datasetBackgroundColor = config.colors?.[0] || '#3b82f6';
            } else {
                datasetBackgroundColor = config.colors || ['#3b82f6', '#ef4444', '#84cc16', '#f97316', '#6366f1'];
            }
            this.chart = new Chart(this.$el, {
                type: config.type,
                data: {
                    labels: config.labels,
                    datasets: [{
                        data: config.values,
                        backgroundColor: datasetBackgroundColor,
                        borderColor: config.type === 'line' ? datasetBackgroundColor : 'transparent',
                        borderWidth: config.type === 'line' ? 2 : 0,
                        fill: config.type === 'line',
                        tension: 0.4,
                        pointBackgroundColor: config.type === 'line' ? datasetBackgroundColor : 'transparent',
                        pointBorderColor: 'transparent',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: ['pie', 'doughnut'].includes(config.type),
                            position: 'bottom',
                            labels: { color: labelColor, boxWidth: 12, padding: 20, }
                        },
                        tooltip: {
                            enabled: true,
                            mode: 'index',
                            intersect: false,
                            callbacks: {
                                label: (context) => config.tooltips[context.dataIndex] || context.formattedValue,
                            }
                        },
                    },
                    scales: {
                        y: {
                            display: ['bar', 'line'].includes(config.type),
                            beginAtZero: true,
                            grid: { color: gridColor },
                            ticks: { color: labelColor }
                        },
                        x: {
                            display: ['bar', 'line'].includes(config.type),
                            grid: { display: false },
                            ticks: { color: labelColor }
                        }
                    }
                }
            });
        },
        updateChartColors() {
            if (!this.chart) return;
            const { gridColor, labelColor } = this.getColors();
            if (this.chart.options.scales.y) {
                this.chart.options.scales.y.grid.color = gridColor;
                this.chart.options.scales.y.ticks.color = labelColor;
            }
            if (this.chart.options.scales.x) {
                this.chart.options.scales.x.ticks.color = labelColor;
            }
            if (this.chart.options.plugins.legend) {
                this.chart.options.plugins.legend.labels.color = labelColor;
            }
            this.chart.update('none');
        }
    }));

    // Alpine component for the Trix rich text editor
    Alpine.data('trixEditor', ({ content, isViewOnly, placeholder }) => ({
        editor: null,
        content: content,
        isViewOnly: isViewOnly,

        init() {
            this.editor = this.$refs.trix;

            if (!this.editor) {
                console.error('Trix editor element not found.');
                return;
            }

            this.editor.editor.loadHTML(this.content || '');
            this.editor.setAttribute('placeholder', placeholder);

            if (this.isViewOnly) {
                this.editor.setAttribute('contenteditable', 'false');
            }

            this.editor.addEventListener('trix-change', (event) => {
                if (this.content !== event.target.value) {
                    this.content = event.target.value;
                }
            }, false);

            this.$watch('content', (newValue) => {
                if (this.editor && newValue !== this.editor.value) {
                    this.editor.editor.loadHTML(newValue || '');
                }
            });
        }
    }));

    // --- TRIX ATTACHMENT UPLOAD HANDLER ---
    document.addEventListener('trix-attachment-add', function (event) {
        if (event.attachment.file) {
            uploadTrixFile(event.attachment);
        }
    });

    function uploadTrixFile(attachment) {
        const formData = new FormData();
        formData.append("attachment", attachment.file);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        attachment.setUploadProgress(0);

        fetch('/trix/attachments', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.url) {
                    attachment.setUploadProgress(100);
                    attachment.setAttributes({
                        url: data.url,
                        href: data.url
                    });
                }
            })
            .catch(error => {
                console.error('Trix attachment upload failed:', error);
                attachment.remove();
            });
    }
});

