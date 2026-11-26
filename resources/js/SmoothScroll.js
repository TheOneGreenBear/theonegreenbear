// Home page: one wheel step moves to the next or previous section.
// Where the browser supports View Transitions, the move is a cinematic dissolve: the current scene
// blurs, pushes in and fades out while the next one comes up out of a blur (styles under
// "Scene transitions" in app.css). Elsewhere, or with reduced motion, it's a quick smooth scroll.
document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('scroll-container');
    if (!container) return;

    let isScrolling = false;

    // Snap points are the container's direct children marked with data-snap (not styling classes,
    // which change with the design).
    const sections = Array.from(container.querySelectorAll(':scope > [data-snap]'));

    const canDissolve = () => typeof document.startViewTransition === 'function'
        && !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

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

    function dissolveToSection(targetSection) {
        const section = sections[targetSection];
        const root = document.documentElement;

        root.classList.add('scene-transition');

        // The browser snapshots the current view, we jump instantly underneath it, then the CSS
        // dissolves the old snapshot into the new one.
        const transition = document.startViewTransition(() => {
            container.scrollTo({ top: section.offsetTop, behavior: 'instant' });
        });

        transition.finished.finally(() => {
            root.classList.remove('scene-transition');
            // A short pause so the tail of a trackpad swipe doesn't trigger another scene change.
            setTimeout(() => { isScrolling = false; }, 150);
        });
    }

    container.addEventListener('wheel', (e) => {
        e.preventDefault();
        if (isScrolling) return;

        const baseIndex = getCurrentSectionIndex();
        let nextIndex = baseIndex + (e.deltaY > 0 ? 1 : -1);
        nextIndex = Math.max(0, Math.min(nextIndex, sections.length - 1));

        if (nextIndex === baseIndex && Math.abs(container.scrollTop - sections[baseIndex].offsetTop) < 2) {
            return;
        }

        isScrolling = true;

        if (canDissolve()) {
            dissolveToSection(nextIndex);
        } else {
            smoothScrollToSection(nextIndex);
        }
    }, { passive: false });
});
