import { ref, watch } from 'vue';

const STORAGE_KEY = 'clutch-theme';

// Reactive state shared between all components that use this composable
const isDark = ref(true);

/**
 * Reads the saved preference from localStorage and syncs the DOM.
 * Call this ONCE on mount in the root layout.
 */
function initTheme() {
    const saved = localStorage.getItem(STORAGE_KEY);
    if (saved === 'light') {
        isDark.value = false;
        document.documentElement.classList.add('light');
    } else {
        isDark.value = true;
        document.documentElement.classList.remove('light');
    }
}

/** Toggle between dark and light. */
function toggleTheme() {
    isDark.value = !isDark.value;
    applyTheme();
}

/** Explicitly set a theme: 'dark' | 'light' */
function setTheme(mode) {
    isDark.value = mode !== 'light';
    applyTheme();
}

function applyTheme() {
    if (isDark.value) {
        document.documentElement.classList.remove('light');
        localStorage.setItem(STORAGE_KEY, 'dark');
    } else {
        document.documentElement.classList.add('light');
        localStorage.setItem(STORAGE_KEY, 'light');
    }
}

export function useTheme() {
    return { isDark, initTheme, toggleTheme, setTheme };
}
