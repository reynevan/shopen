import { defineStore } from 'pinia'
import {computed} from 'vue'
import {router, usePage} from "@inertiajs/vue3";

export const useAuthStore = defineStore('auth', () => {

    const page = usePage();
    const user = computed(() => page.props.auth.user)

    const isLoggedIn = computed(() =>  page.props.auth.user && !!page.props.auth.user.id);

    const logout = () => {
        router.post('/logout');
    }

    return {
        user,
        isLoggedIn,
        logout
    }
})