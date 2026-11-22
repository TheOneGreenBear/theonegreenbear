export function initParallax(elementId) {
    const logo = document.getElementById(elementId);

    if (!logo) return;

    // Where the mouse wants the logo to be, and where it currently is.
    let targetX = 0;
    let targetY = 0;
    let currentX = 0;
    let currentY = 0;

    window.addEventListener('mousemove', (e) => {
        targetX = (window.innerWidth / 2 - e.clientX) / 200;
        targetY = (window.innerHeight / 2 - e.clientY) / 200;
    });

    // Ease towards the target each frame ("lerp"). Lower factor = smoother/slower.
    function animate() {
        currentX += (targetX - currentX) * 0.1;
        currentY += (targetY - currentY) * 0.1;

        logo.style.transform = `translate3d(${currentX}px, ${currentY}px, 0)`;

        requestAnimationFrame(animate);
    }

    animate();
}
