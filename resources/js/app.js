import './bootstrap';

import Chart from 'chart.js/auto';

Chart.defaults.color = 'rgb(209, 213, 219)';
window.Chart = Chart;

// Translate Livewire navigate events to Alpine-compatible state
document.addEventListener('livewire:navigate', () => {
    window.dispatchEvent(new CustomEvent('navigation-start'));
});

document.addEventListener('livewire:navigated', () => {
    window.dispatchEvent(new CustomEvent('navigation-end'));
});

