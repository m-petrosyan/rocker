import { nextTick } from 'vue';
import tooltipDirective from '@/Directives/tooltipDirective.js';

export default {
  install(app) {
    // Регистрируем директиву v-tooltip
    app.directive('tooltip', tooltipDirective);

    // Глобальная обработка атрибута tooltip
    const applyTooltip = () => {
      if (typeof window === 'undefined') return; // Защита для SSR

      // Находим все элементы с атрибутом tooltip
      const elements = document.querySelectorAll('[tooltip]');
      elements.forEach((el) => {
        // el.classList.add('cursor-help'); // Добавляем класс для указателя
        // Пропускаем, если элемент уже обработан
        if (el._tooltipProcessed) return;

        const tooltipValue = el.getAttribute('tooltip');
        if (tooltipValue) {
          // Применяем директиву v-tooltip
          tooltipDirective.mounted(el, { value: tooltipValue });
          el._tooltipBinding = { value: tooltipValue }; // Сохраняем для обновления
          el._tooltipProcessed = true; // Помечаем как обработанный
        }
      });
    };

    // Выполняем обработку после рендеринга DOM
    app.mixin({
      mounted() {
        if (typeof window === 'undefined') return; // Защита для SSR
        nextTick(() => applyTooltip()); // Ждём готовности DOM
      },
      updated() {
        if (typeof window === 'undefined') return; // Защита для SSR
        nextTick(() => {
          const elements = document.querySelectorAll('[tooltip]');
          elements.forEach((el) => {
            console.log(el);

            // Пример дополнительного действия при обновлении
            const tooltipValue = el.getAttribute('tooltip');
            if (el._tooltipBinding && el._tooltipBinding.value !== tooltipValue) {
              tooltipDirective.updated(el, { value: tooltipValue, oldValue: el._tooltipBinding.value });
              el._tooltipBinding.value = tooltipValue;
            }
          });
        });
      },
      unmounted() {
        if (typeof window === 'undefined') return; // Защита для SSR
        const elements = document.querySelectorAll('[tooltip]');
        elements.forEach((el) => {
          if (el._tooltipData) {
            tooltipDirective.unmounted(el);
            el._tooltipProcessed = false; // Сбрасываем флаг
          }
        });
      }
    });

    // Инициализация при загрузке приложения
    if (typeof window !== 'undefined') {
      nextTick(() => applyTooltip());
    }
  }
};
