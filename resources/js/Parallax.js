export function initParallax(elementId) {
    const logo = document.getElementById(elementId);
    
    if (!logo) return;

    window.addEventListener('mousemove', (e) => {
        const x = (window.innerWidth / 2 - e.pageX) / 200;
        const y = (window.innerHeight / 2 - e.pageY) / 200;
        
        logo.style.transform = `translateX(${x}px) translateY(${y}px)`;
    });

    // 2. The Animation Loop (The "Smooth" part)
    function animate() {
        // "Lerping" formula: current + (target - current) * factor
        // 0.1 is the smoothness factor. Lower = slower/smoother.
        currentX += (mouseX - currentX) * 0.1;
        currentY += (mouseY - currentY) * 0.1;

        logo.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;

        // Keep the loop running
        requestAnimationFrame(animate);
    }

    animate();
}

// Auto-init if the script is loaded
document.addEventListener('DOMContentLoaded', () => {
    initParallax('parallax-logo');
});