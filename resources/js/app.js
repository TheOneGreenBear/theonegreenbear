import { initParallax } from './parallax';

// Wait for the page to load, then start the parallax on your specific ID
document.addEventListener('DOMContentLoaded', () => {
    initParallax('parallax-logo');
});