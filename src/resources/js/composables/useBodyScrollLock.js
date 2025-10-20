import { onUnmounted } from 'vue'

export const useBodyScrollLock = () => {
    const lock = () => {
        if (typeof document === 'undefined') return
        document.body.style.overflow = 'hidden'
    }

    const unlock = () => {
        if (typeof document === 'undefined') return
        document.body.style.overflow = ''
    }

    // Auto cleanup on unmount
    onUnmounted(() => {
        unlock()
    })

    return {
        lock,
        unlock
    }
}