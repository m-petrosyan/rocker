const tooltipDirective = {
  mounted(el, binding) {
    if (typeof window === 'undefined') return;

    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip tooltip-top';
    tooltip.textContent = binding.value || 'No content';
    tooltip.setAttribute('role', 'tooltip');

    Object.assign(tooltip.style, {
      position: 'absolute',
      background: '#333',
      color: '#fff',
      padding: '8px 12px',
      borderRadius: '6px',
      fontSize: '14px',
      zIndex: '1000',
      display: 'none',
      whiteSpace: 'nowrap',
      pointerEvents: 'none',
      transition: 'opacity 0.2s ease-in-out, transform 0.2s ease-in-out',
      transform: 'translateY(4px)'
    });

    el.style.position = 'relative';
    el.appendChild(tooltip);

    const updatePosition = () => {
      const rect = el.getBoundingClientRect();
      const tooltipRect = tooltip.getBoundingClientRect();
      tooltip.style.left = `${rect.width / 2 - tooltipRect.width / 2}px`;
      tooltip.style.top = `-${tooltipRect.height + 10}px`;
    };

    const showTooltip = () => {
      tooltip.style.display = 'block';
      tooltip.style.opacity = '1';
      tooltip.style.transform = 'translateY(0)';
      updatePosition();
    };

    const hideTooltip = () => {
      tooltip.style.opacity = '0';
      tooltip.style.transform = 'translateY(4px)';
      setTimeout(() => tooltip.style.display = 'none', 200);
    };

    el.addEventListener('mouseenter', showTooltip);
    el.addEventListener('mouseleave', hideTooltip);
    window.addEventListener('scroll', updatePosition, { passive: true });
    window.addEventListener('resize', updatePosition, { passive: true });

    el._tooltipData = { tooltip, updatePosition, showTooltip, hideTooltip };
  },

  updated(el, binding) {
    if (typeof window === 'undefined') return;

    if (el._tooltipData?.tooltip && binding.value !== binding.oldValue) {
      el._tooltipData.tooltip.textContent = binding.value || 'No content';
      el._tooltipData.updatePosition();
    }
  },

  unmounted(el) {
    if (typeof window === 'undefined') return;

    const { tooltip, updatePosition, showTooltip, hideTooltip } = el._tooltipData || {};
    if (tooltip) {
      el.removeEventListener('mouseenter', showTooltip);
      el.removeEventListener('mouseleave', hideTooltip);
      window.removeEventListener('scroll', updatePosition);
      window.removeEventListener('resize', updatePosition);
      tooltip.remove();
      delete el._tooltipData;
    }
  }
};

export default tooltipDirective;