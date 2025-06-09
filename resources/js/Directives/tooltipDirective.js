const tooltipDirective = {
    mounted(el, binding) {
        // Создаем элемент tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'tooltip tooltip-top';
        tooltip.textContent = binding.value || ''; // Принимаем строку напрямую

        // Стили для tooltip
        tooltip.style.position = 'absolute';
        tooltip.style.background = '#333';
        tooltip.style.color = '#fff';
        tooltip.style.padding = '5px 10px';
        tooltip.style.borderRadius = '4px';
        tooltip.style.fontSize = '12px';
        tooltip.style.zIndex = '10';
        tooltip.style.display = 'none';
        tooltip.style.whiteSpace = 'nowrap';

        // Добавляем tooltip как дочерний элемент контейнера
        el.style.position = 'relative';
        el.appendChild(tooltip);

        // Функция для позиционирования tooltip (всегда сверху)
        function updatePosition() {
            const rect = el.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();
            tooltip.style.left = `${rect.width / 2 - tooltipRect.width / 2}px`;
            tooltip.style.top = `-${tooltipRect.height + 8}px`;
        }

        // Показываем tooltip при наведении
        el.addEventListener('mouseenter', () => {
            tooltip.style.display = 'block';
            updatePosition();
        });

        // Скрываем tooltip при уходе курсора
        el.addEventListener('mouseleave', () => {
            tooltip.style.display = 'none';
        });

        // Обновляем позицию при скролле или изменении размеров
        window.addEventListener('scroll', updatePosition, true);
        window.addEventListener('resize', updatePosition);

        // Сохраняем элемент и функцию для последующего удаления
        el._tooltip = tooltip;
        el._tooltip._updatePosition = updatePosition;
    },
    unmounted(el) {
        // Удаляем tooltip и слушатели событий
        if (el._tooltip) {
            el._tooltip.remove();
            window.removeEventListener('scroll', el._tooltip._updatePosition, true);
            window.removeEventListener('resize', el._tooltip._updatePosition);
        }
    }
};

export default tooltipDirective;
