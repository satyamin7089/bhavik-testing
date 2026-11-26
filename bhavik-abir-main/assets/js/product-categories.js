/* Home/About tab interaction only. All product content stays in static PHP/HTML. */
(() => {
  document.querySelectorAll('[data-category-tabs]').forEach((root) => {
    const rail = root.querySelector('.ab-category-polymers');
    const tabs = root.querySelectorAll('.ab-category-tab');
    const panels = root.querySelectorAll('.ab-category-panel');
    const toolbar = root.querySelector('.ab-category-toolbar');
    const previous = root.querySelector('.ab-category-prev');
    const next = root.querySelector('.ab-category-next');
    const position = root.querySelector('.ab-category-position');
    if (!rail || !toolbar || !previous || !next || !position || !tabs.length || tabs.length !== panels.length || root.dataset.tabsReady === 'true') return;
    root.dataset.tabsReady = 'true';
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let active = 0;
    let animation;
    rail.setAttribute('role', 'tablist');
    rail.setAttribute('aria-orientation', 'horizontal');
    tabs.forEach((tab, index) => {
      tab.setAttribute('role', 'tab');
      tab.setAttribute('aria-controls', panels[index].id);
      panels[index].setAttribute('role', 'tabpanel');
      panels[index].tabIndex = 0;
      if (tab.dataset.material === root.dataset.initialMaterial) active = index;
    });
    toolbar.hidden = false;
    // Only move the horizontal rail, never the surrounding page or header.
    const centerTab = (smooth) => {
      const tabRect = tabs[active].getBoundingClientRect();
      const railRect = rail.getBoundingClientRect();
      const target = rail.scrollLeft + tabRect.left - railRect.left - (rail.clientWidth - tabRect.width) / 2;
      rail.scrollTo({ left: Math.max(0, Math.min(target, rail.scrollWidth - rail.clientWidth)), behavior: smooth && !reducedMotion.matches ? 'smooth' : 'auto' });
    };
    const select = (index, animate = true, focus = false) => {
      if (index < 0 || index >= tabs.length) return;
      const direction = index >= active ? 1 : -1;
      const changed = active !== index;
      active = index;
      if (animation) animation.cancel();
      tabs.forEach((tab, tabIndex) => {
        const selected = tabIndex === index;
        tab.setAttribute('aria-selected', String(selected));
        tab.tabIndex = selected ? 0 : -1;
        panels[tabIndex].hidden = !selected;
      });
      previous.disabled = index === 0;
      next.disabled = index === tabs.length - 1;
      position.textContent = `${index + 1} of ${tabs.length} · ${tabs[index].querySelector('strong').textContent}`;
      if (focus) tabs[index].focus({ preventScroll: true });
      centerTab(animate);
      if (changed && animate && !reducedMotion.matches && typeof panels[index].animate === 'function') {
        animation = panels[index].animate([
          { opacity: 0, transform: `translateY(${direction * 22}px)` },
          { opacity: 1, transform: 'translateY(0)' },
        ], { duration: 260, easing: 'cubic-bezier(.22,1,.36,1)' });
      }
    };
    tabs.forEach((tab, index) => {
      tab.addEventListener('click', (event) => {
        if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        select(index);
      });
      tab.addEventListener('keydown', (event) => {
        let target;
        if (event.key === 'ArrowRight' || event.key === 'ArrowDown') target = (index + 1) % tabs.length;
        else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') target = (index - 1 + tabs.length) % tabs.length;
        else if (event.key === 'Home') target = 0;
        else if (event.key === 'End') target = tabs.length - 1;
        else if (event.key === ' ') target = index;
        else return;
        event.preventDefault();
        select(target, true, true);
      });
    });
    previous.addEventListener('click', () => select(active - 1));
    next.addEventListener('click', () => select(active + 1));
    select(active, false);
    if ('ResizeObserver' in window) new ResizeObserver(() => centerTab(false)).observe(rail);
    else window.addEventListener('resize', () => centerTab(false), { passive: true });
  });
})();
