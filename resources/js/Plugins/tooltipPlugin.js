import { nextTick } from 'vue';
import tooltipDirective from '@/Directives/tooltipDirective.js';

export default {
  install(app) {
    // Регистрируем глобальную директиву v-tooltip
    app.directive('tooltip', tooltipDirective);

    // Авто-применение для элементов с атрибутом tooltip=""
    const applyTooltip = () => {
      if (typeof window === 'undefined') return; // SSR защита

      document.querySelectorAll('[tooltip]').forEach((el) => {
        if (el._tooltipApplied) return; // Уже обработан
        const value = el.getAttribute('tooltip');
        if (!value) return;

        tooltipDirective.mounted(el, { value });
        el._tooltipApplied = true;
      });
    };

    // Выполняем один раз после рендеринга
    if (typeof window !== 'undefined') {
      nextTick(() => applyTooltip());
    }
  }
};
