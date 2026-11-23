/**
 * In-place tabs. Markup:
 *
 *   <div data-tabs="status" data-tab-active="classes…" data-tab-idle="classes…">
 *     <a href="?status=a" data-tab="a">A</a> …
 *     <div data-tab-panel="a">…</div> …
 *   </div>
 *
 * The links are real links, so everything works without JavaScript. With it, clicking a tab shows its
 * panel without reloading, replays the panel's entrance animation and keeps the URL in sync
 * (data-tabs names the query parameter; an empty data-tab removes it).
 *
 * Switching from a long list to a short one would normally make the browser snap the scroll position
 * (the page suddenly gets shorter). Instead the old height is held briefly while the page glides to
 * where it can rest, then released, so nothing jumps.
 */

const prefersReducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/** Swap panels without the page jumping, scrolling smoothly to a position that still exists afterwards. */
function switchWithoutJump(root, swapPanels) {
    const scroller = root.closest('.overflow-y-auto');
    if (!scroller) {
        swapPanels();
        return;
    }

    const tabs = root.querySelector('nav') || root;
    const navbarHeight = scroller.querySelector('.site-nav')?.offsetHeight ?? 0;
    const previousHeight = root.offsetHeight;
    const startScroll = scroller.scrollTop;

    swapPanels();

    // Where the scroll can end up with the new content: never past the new bottom of the page, and no
    // lower than the point where the tabs sit just under the navbar.
    const maxScroll = scroller.scrollHeight - scroller.clientHeight;
    const tabsUnderNavbar = startScroll + tabs.getBoundingClientRect().top - scroller.getBoundingClientRect().top - navbarHeight - 16;
    const target = Math.max(0, Math.min(startScroll, maxScroll, tabsUnderNavbar));

    if (target === startScroll) return;

    // Hold the old height so the browser can't clamp the scroll instantly, glide there, then let go.
    root.style.minHeight = `${previousHeight}px`;
    scroller.scrollTop = startScroll;

    let released = false;
    const release = (force = false) => {
        // The panel swap itself fires scroll events; only let go once the glide has actually arrived.
        if (released || (!force && Math.abs(scroller.scrollTop - target) > 2)) return;

        released = true;
        root.style.minHeight = '';
        scroller.removeEventListener('scrollend', onScrollEnd);
    };
    const onScrollEnd = () => release();

    if (prefersReducedMotion()) {
        scroller.scrollTop = target;
        release(true);
        return;
    }

    scroller.addEventListener('scrollend', onScrollEnd);
    setTimeout(() => release(true), 900); // Fallback for browsers without the scrollend event.
    scroller.scrollTo({ top: target, behavior: 'smooth' });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-tabs]').forEach((root) => {
        const param = root.dataset.tabs;
        const activeClasses = (root.dataset.tabActive || '').split(' ').filter(Boolean);
        const idleClasses = (root.dataset.tabIdle || '').split(' ').filter(Boolean);
        const tabs = [...root.querySelectorAll('[data-tab]')];
        const panels = [...root.querySelectorAll('[data-tab-panel]')];

        const show = (key) => {
            tabs.forEach((tab) => {
                const active = tab.dataset.tab === key;
                tab.classList.remove(...(active ? idleClasses : activeClasses));
                tab.classList.add(...(active ? activeClasses : idleClasses));
                active ? tab.setAttribute('aria-current', 'page') : tab.removeAttribute('aria-current');
            });

            switchWithoutJump(root, () => {
                panels.forEach((panel) => {
                    const active = panel.dataset.tabPanel === key;
                    panel.hidden = !active;

                    if (active) {
                        // Restart the entrance animation (see .tab-enter in app.css).
                        panel.classList.remove('tab-enter');
                        void panel.offsetWidth;
                        panel.classList.add('tab-enter');
                    }
                });
            });

            const url = new URL(window.location.href);
            key ? url.searchParams.set(param, key) : url.searchParams.delete(param);
            window.history.replaceState(null, '', url);
        };

        tabs.forEach((tab) => {
            tab.addEventListener('click', (event) => {
                // Let modified clicks (new tab/window) behave like normal links.
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || event.button !== 0) return;

                event.preventDefault();
                show(tab.dataset.tab);
            });
        });
    });
});
