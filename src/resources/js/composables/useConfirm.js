import { createApp, h } from 'vue';
import ConfirmationModal from '@shopen/components/frontend/ui/ConfirmationModal.vue';

export function useConfirm() {
    const confirm = (options = {}) => {
        return new Promise((resolve) => {
            const mountEl = document.createElement('div');
            document.body.appendChild(mountEl);

            const cleanup = () => {
                if (app) {
                    app.unmount();
                }
                if (mountEl.parentNode) {
                    document.body.removeChild(mountEl);
                }
            };

            const app = createApp({
                render() {
                    return h(ConfirmationModal, {
                        // Przekazujemy wszystkie opcje, ale już bez `show`
                        ...options,

                        // Te funkcje zostaną wywołane z opóźnieniem przez setTimeout
                        // wewnątrz ConfirmationModal, po zakończeniu animacji wyjścia.
                        onConfirm: () => {
                            cleanup();
                            resolve(true);
                        },
                        onCancel: () => {
                            cleanup();
                            resolve(false);
                        },
                    });
                },
            });

            app.mount(mountEl);
        });
    };

    return { confirm };
}