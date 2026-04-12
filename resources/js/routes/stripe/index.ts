import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\StripeWebhookController::webhook
 * @see app/Http/Controllers/StripeWebhookController.php:15
 * @route '/stripe/webhook'
 */
export const webhook = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})

webhook.definition = {
    methods: ["post"],
    url: '/stripe/webhook',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\StripeWebhookController::webhook
 * @see app/Http/Controllers/StripeWebhookController.php:15
 * @route '/stripe/webhook'
 */
webhook.url = (options?: RouteQueryOptions) => {
    return webhook.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\StripeWebhookController::webhook
 * @see app/Http/Controllers/StripeWebhookController.php:15
 * @route '/stripe/webhook'
 */
webhook.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: webhook.url(options),
    method: 'post',
})
const stripe = {
    webhook: Object.assign(webhook, webhook),
}

export default stripe