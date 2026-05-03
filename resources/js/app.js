import './bootstrap';

import Alpine from 'alpinejs';

import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

window.Alpine = Alpine;

// Alpine.start();

// NProgress
NProgress.configure({ showSpinner: false })

document.addEventListener('livewire:navigating', () => {
    NProgress.start()
})

document.addEventListener('livewire:navigated', () => {
    NProgress.done()
})