// resources/js/Directives/tooltipDirective.js
const tooltipDirective = {
    mounted(el, binding) {
        if (typeof window === 'undefined') return; // Защита для SSR

        // Создаем элемент tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip tooltip-top';
        tooltip.textContent = binding.value || 'No content'; // Поддержка строк и переменных
        tooltip.setAttribute('role', 'tooltip'); // Доступность

        // Стили для tooltip
        Object.assign(tooltip.style, {
            position: 'absolute',
            background: '#333',
            color: '#fff',
            padding: '8px 12px',
            borderRadius: '6px',
            fontSize: '14px',
            zIndex: '1000',
            display: 'none',
            whiteSpace: 'pre-line',
            maxWidth: '300px',
            pointerEvents: 'none',
            transition: 'opacity 0.2s ease-in-out, transform 0.2s ease-in-out',
            transform: 'translateY(4px)'
        });

        // Добавляем tooltip в DOM
        el.style.position = 'relative';
        el.appendChild(tooltip);

        // Обновление позиции tooltip
        const updatePosition = () => {
            const rect = el.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            tooltip.style.left = `${rect.width / 2 - tooltipRect.width / 2}px`;
            tooltip.style.top = `-${tooltipRect.height + 10}px`;
        };

        // Показ и скрытие tooltip
        const showTooltip = () => {
            tooltip.style.display = 'block';
            tooltip.style.opacity = '1';
            tooltip.style.transform = 'translateY(0)';
            updatePosition();
        };
        const hideTooltip = () => {
            tooltip.style.opacity = '0';
            tooltip.style.transform = 'translateY(4px)';
            setTimeout(() => {
                tooltip.style.display = 'none';
            }, 200);
        };

        // Слушатели событий
        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);
        window.addEventListener('scroll', updatePosition, { passive: true });
        window.addEventListener('resize', updatePosition, { passive: true });

        // Сохраняем данные для очистки
        el._tooltipData = { tooltip, updatePosition, showTooltip, hideTooltip };
    },

    updated(el, binding) {
        if (typeof window === 'undefined') return; // Защита для SSR

        // Обновляем текст tooltip при изменении binding.value
        if (el._tooltipData?.tooltip && binding.value !== binding.oldValue) {
            el._tooltipData.tooltip.textContent = binding.value || 'No content';
            el._tooltipData.updatePosition();
        }
    },

    unmounted(el) {
        if (typeof window === 'undefined') return; // Защита для SSR

        // Очистка
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
