import { router } from '@inertiajs/vue3';

const KEY = 'cc_nav_stack';
const MAX = 50;

export function getPathWithSearch() {
    return window.location.pathname + window.location.search;
}

function loadStack() {
    try {
        const raw = sessionStorage.getItem(KEY);
        if (!raw) return [];
        const arr = JSON.parse(raw);
        return Array.isArray(arr) ? arr : [];
    } catch {
        return [];
    }
}

function saveStack(stack) {
    sessionStorage.setItem(KEY, JSON.stringify(stack.slice(-MAX)));
}

/** Crida quan canvia la URL visible d’Inertia (p. ex. des d’AppLayout). */
export function recordNavigationVisit() {
    if (typeof window === 'undefined') return;
    const path = getPathWithSearch();
    const stack = loadStack();
    if (stack[stack.length - 1] !== path) {
        stack.push(path);
        saveStack(stack);
    }
}

/**
 * Torna a la pantalla anterior dins l’app; si no n’hi ha prou històric, va al fallback.
 * @param {string} fallbackUrl — ruta interna (p. ex. retorn de route())
 */
export function smartBack(fallbackUrl) {
    if (typeof window === 'undefined') return;
    let stack = loadStack();
    const cur = getPathWithSearch();
    while (stack.length && stack[stack.length - 1] !== cur) {
        stack.pop();
    }
    if (stack.length && stack[stack.length - 1] === cur) {
        stack.pop();
    }
    saveStack(stack);
    const prev = stack[stack.length - 1];
    if (prev) {
        stack.pop();
        saveStack(stack);
        router.visit(prev);
    } else if (fallbackUrl) {
        router.visit(fallbackUrl);
    }
}
