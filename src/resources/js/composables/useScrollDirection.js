import { ref, onMounted, onUnmounted, computed } from 'vue'

const scrollDirection = ref(null)
const lastScrollY = ref(0)
const scrollY = ref(0)
const isReady = ref(false) // Flaga gotowości

let ticking = false
let pendingScrollY = 0

const updateScrollDirection = () => {
    scrollY.value = pendingScrollY

    // Ignoruj zmiany kierunku jeśli nie jesteśmy gotowi
    if (!isReady.value) {
        lastScrollY.value = scrollY.value
        ticking = false
        return
    }

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
        pendingScrollY = window.scrollY
        window.requestAnimationFrame(updateScrollDirection)
        ticking = true
    }
}

let isListening = false
let listenerCount = 0
let readyTimeout = null

export const useScrollDirection = () => {
    onMounted(() => {
        listenerCount++

        if (!isListening && typeof window !== 'undefined') {
            lastScrollY.value = window.scrollY
            pendingScrollY = window.scrollY
            scrollY.value = window.scrollY
            isReady.value = false

            // Czekamy ~500ms aż przeglądarka zakończy auto-scroll
            readyTimeout = setTimeout(() => {
                isReady.value = true
                lastScrollY.value = window.scrollY
            }, 500)

            window.addEventListener('scroll', onScroll, { passive: true })
            isListening = true
        }
    })

    onUnmounted(() => {
        listenerCount--

        if (listenerCount === 0 && isListening) {
            window.removeEventListener('scroll', onScroll)
            isListening = false
            isReady.value = false
            scrollDirection.value = null

            if (readyTimeout) {
                clearTimeout(readyTimeout)
                readyTimeout = null
            }
        }
    })

    return {
        scrollDirection,
        scrollY,
        isScrollingDown: computed(() => scrollDirection.value === 'down'),
        isScrollingUp: computed(() => scrollDirection.value === 'up')
    }
}