import './bootstrap';
import * as bootstrap from 'bootstrap';
import NProgress from 'nprogress';

window.bootstrap = bootstrap;

NProgress.configure({showSpinner: false});
document.addEventListener('livewire:navigating', () => NProgress.start());
document.addEventListener('livewire:navigated', () => NProgress.done());
