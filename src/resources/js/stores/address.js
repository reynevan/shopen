import { defineStore } from 'pinia'
import {useAuthStore} from "./auth";

export const useAddressStore = defineStore('address', () => {

    const auth = useAuthStore();

    const validate = (address, errors) => {
        errors.value = {};
        if (!address.first_name) {
            errors.value.first_name = 'Wpisz imię';
        }
        if (!address.last_name) {
            errors.value.last_name = 'Wpisz nazwisko';
        }
        if (!address.address_line) {
            errors.value.address_line = 'Wpisz nazwę i numer ulicy';
        }
        if (!address.city) {
            errors.value.city = 'Wpisz nazwę miasta';
        }
        if (!address.phone) {
            errors.value.phone = 'Podaj numer telefonu';
        }
        if (!address.postal_code) {
            errors.value.postal_code = 'Wpisz kod pocztowy';
        } else if (!address.postal_code.trim().match(/^\d{2}-?\d{3}$/)) {
            errors.value.postal_code = 'Niepoprawny kod pocztowy';
        }
        if (!auth.isLoggedIn && !address.email) {
            errors.value.email = 'Podaj adres e-mail';
        }
        return Object.keys(errors.value).length === 0;
    }

    return {
        validate
    }
})