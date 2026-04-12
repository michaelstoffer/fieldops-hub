import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\InvoiceController::store
 * @see app/Http/Controllers/Owner/InvoiceController.php:135
 * @route '/owner/invoices/{invoice}/payments'
 */
export const store = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/invoices/{invoice}/payments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::store
 * @see app/Http/Controllers/Owner/InvoiceController.php:135
 * @route '/owner/invoices/{invoice}/payments'
 */
store.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return store.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::store
 * @see app/Http/Controllers/Owner/InvoiceController.php:135
 * @route '/owner/invoices/{invoice}/payments'
 */
store.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})
const payments = {
    store: Object.assign(store, store),
}

export default payments