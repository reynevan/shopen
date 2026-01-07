export default {
    install: (app, options = {}) => {
        const defaultLocale = options.locale || 'pl-PL'
        const defaultCurrency = options.currency || 'PLN'

        app.config.globalProperties.$currency = (value, currency = defaultCurrency, locale = defaultLocale) => {
            return new Intl.NumberFormat(locale, {
                style: 'currency',
                currency: currency
            }).format(value)
        }
    }
}