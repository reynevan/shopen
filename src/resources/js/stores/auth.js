import { defineStore } from 'pinia'
import {computed} from 'vue'
import {router, usePage} from "@inertiajs/vue3";

export const useAuthStore = defineStore('auth', () => {

    const page = usePage();

    const isLoggedIn = computed(() =>  page.props.auth);

    const logout = () => {
        router.post('/logout');
    }
    const logoutAdmin = () => {
        router.post('/admin/logout');
    }

    return {
        isLoggedIn,
        logout,
        logoutAdmin
    }
})