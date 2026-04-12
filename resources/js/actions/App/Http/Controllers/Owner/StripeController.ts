import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\StripeController::createCheckoutSession
 * @see app/Http/Controllers/Owner/StripeController.php:14
 * @route '/owner/invoices/{invoice}/checkout'
 */
export const createCheckoutSession = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createCheckoutSession.url(args, options),
    method: 'post',
})

createCheckoutSession.definition = {
    methods: ["post"],
    url: '/owner/invoices/{invoice}/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\StripeController::createCheckoutSession
 * @see app/Http/Controllers/Owner/StripeController.php:14
 * @route '/owner/invoices/{invoice}/checkout'
 */
createCheckoutSession.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return createCheckoutSession.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\StripeController::createCheckoutSession
 * @see app/Http/Controllers/Owner/StripeController.php:14
 * @route '/owner/invoices/{invoice}/checkout'
 */
createCheckoutSession.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: createCheckoutSession.url(args, options),
    method: 'post',
})
const StripeController = { createCheckoutSession }

export default StripeController