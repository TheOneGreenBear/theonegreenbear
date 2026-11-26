import { initCrosshair, initScramble } from './Hud';
import { initParallax } from './Parallax';
import './SmoothScroll';
import './Tabs';

document.addEventListener('DOMContentLoaded', () => {
    initParallax('parallax-logo');
    initScramble();
    initCrosshair();
});
