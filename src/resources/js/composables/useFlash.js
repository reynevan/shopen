import { ref } from 'vue';

const flashMessages = ref([]);
let messageId = 0;

export function useFlash() {
    const show = (message, type = 'info', duration = 5000) => {
        const id = messageId++;
        const flashMessage = {
            id,
            message,
            type,
            visible: true
        };

        flashMessages.value.push(flashMessage);

        if (duration > 0) {
            setTimeout(() => {
                remove(id);
            }, duration);
        }

        return id;
    };

    const remove = (id) => {
        const index = flashMessages.value.findIndex(msg => msg.id === id);
        if (index !== -1) {
            flashMessages.value.splice(index, 1);
        }
    };

    const success = (message, duration = 5000) => {
        return show(message, 'success', duration);
    };

    const error = (message, duration = 5000) => {
        return show(message, 'error', duration);
    };

    const info = (message, duration = 5000) => {
        return show(message, 'info', duration);
    };

    const warning = (message, duration = 5000) => {
        return show(message, 'warning', duration);
    };

    return {
        messages: flashMessages,
        show,
        success,
        error,
        info,
        warning,
        remove
    };
}