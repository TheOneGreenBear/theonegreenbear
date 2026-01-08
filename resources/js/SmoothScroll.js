  const container = document.getElementById('scroll-container');
    let isScrolling = false;
    let currentSection = 0;
    let sectionIndex = 0;
    
    // Mark sections
    container.querySelectorAll('.h-full.w-full, section.relative').forEach(el => {
        el.setAttribute('data-section', sectionIndex++);
    });
    
    function smoothScrollToSection(targetSection) {
        const section = container.querySelector(`[data-section="${targetSection}"]`);
        if (!section) return;
        
        const targetScroll = section.offsetTop;
        const startScroll = container.scrollTop;
        const distance = targetScroll - startScroll;
        const duration = 1000; // 1 second smooth animation
        let startTime = null;
        
        function animate(currentTime) {
            if (!startTime) startTime = currentTime;
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            
            // Easing function for smooth animation
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
        currentSection += e.deltaY > 0 ? 1 : -1;
        currentSection = Math.max(0, Math.min(currentSection, sectionIndex - 1));
        
        smoothScrollToSection(currentSection);
    }, { passive: false });