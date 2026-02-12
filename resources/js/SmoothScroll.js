document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('scroll-container');
    if (!container) return;

    let isScrolling = false;

    // Only mark top-level sections to avoid nested matches.
    const sections = Array.from(
        container.querySelectorAll(':scope > .h-full.w-full, :scope > section.relative')
    );

    sections.forEach((el, index) => {
        el.setAttribute('data-section', index);
    });

    function getCurrentSectionIndex() {
        let index = 0;
        for (let i = 0; i < sections.length; i += 1) {
            if (container.scrollTop >= sections[i].offsetTop - 1) {
                index = i;
            }
        }
        return index;
    }

    function smoothScrollToSection(targetSection) {
        const section = sections[targetSection];
        if (!section) return;

        const targetScroll = section.offsetTop;
        const startScroll = container.scrollTop;
        const distance = targetScroll - startScroll;
        const duration = 250;
        let startTime = null;

        function animate(currentTime) {
            if (!startTime) startTime = currentTime;
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);

            const easeProgress = progress < 0.5
                ? 2 * progress * progress
                : -1 + (4 - 2 * progress) * progress;

            container.scrollTop = startScroll + distance * easeProgress;

            if (progress < 1) {
                requestAnimationFrame(animate);
            } else {
                isScrolling = false;
            }
        }

        requestAnimationFrame(animate);
    }

    container.addEventListener('wheel', (e) => {
        if (isScrolling) return;
        e.preventDefault();

        isScrolling = true;
        const baseIndex = getCurrentSectionIndex();
        let nextIndex = baseIndex + (e.deltaY > 0 ? 1 : -1);
        nextIndex = Math.max(0, Math.min(nextIndex, sections.length - 1));

        smoothScrollToSection(nextIndex);
    }, { passive: false });
});