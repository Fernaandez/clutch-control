import { defineStore } from 'pinia';

export const useRoutesStore = defineStore('routes', {
    state: () => ({
        routes: [],
        lastFetch: null,
    }),
    actions: {
        setRoutes(routesData) {
            this.routes = routesData;
            this.lastFetch = new Date().getTime();
        },
    },
    persist: true,
});
