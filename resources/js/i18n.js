import { createI18n } from 'vue-i18n';
import es from './locales/es.json';
import en from './locales/en.json';
import ca from './locales/ca.json';

// Get default locale from localStorage or fallback to Spanish
const savedLocale = localStorage.getItem('locale') || 'es';

export const i18n = createI18n({
    legacy: false, // We must use Composition API mode for Vue 3 setup script usage
    globalInjection: true,
    locale: savedLocale,
    fallbackLocale: 'es',
    messages: {
        es,
        en,
        ca
    }
});
