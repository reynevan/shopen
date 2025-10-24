import { ref, onMounted, onUnmounted, computed } from 'vue'

const scrollDirection = ref(null)
const lastScrollY = ref(0)
const scrollY = ref(0)

let ticking = false
let pendingScrollY = 0

const updateScrollDirection = () => {
    // Używamy zapisanej wartości zamiast odczytywać window.scrollY tutaj
    scrollY.value = pendingScrollY

    if (scrollY.value > lastScrollY.value && scrollY.value > 50) {
        scrollDirection.value = 'down'
    } else if (scrollY.value < lastScrollY.value) {
        scrollDirection.value = 'up'
    }

    lastScrollY.value = scrollY.value
    ticking = false
}

const onScroll = () => {
    if (!ticking) {
        // Odczytujemy scrollY TUTAJ (w event handlerze), nie w rAF
        pendingScrollY = window.scrollY
        window.requestAnimationFrame(updateScrollDirection)
        ticking = true
    }
}

let isListening = false
let listenerCount = 0

export const useScrollDirection = () => {
    onMounted(() => {
        listenerCount++

        if (!isListening && typeof window !== 'undefined') {
            lastScrollY.value = window.scrollY
            pendingScrollY = window.scrollY
            window.addEventListener('scroll', onScroll, { passive: true })
            isListening = true
        }
    })

    onUnmounted(() => {
        listenerCount--

        if (listenerCount === 0 && isListening) {
            window.removeEventListener('scroll', onScroll)
            isListening = false
        }
    })

    return {
        scrollDirection,
        scrollY,
        isScrollingDown: computed(() => scrollDirection.value === 'down'),
        isScrollingUp: computed(() => scrollDirection.value === 'up')
    }
}