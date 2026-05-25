import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\SubscriptionController::index
 * @see app/Http/Controllers/Owner/SubscriptionController.php:23
 * @route '/owner/subscription'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/subscription',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::index
 * @see app/Http/Controllers/Owner/SubscriptionController.php:23
 * @route '/owner/subscription'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::index
 * @see app/Http/Controllers/Owner/SubscriptionController.php:23
 * @route '/owner/subscription'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SubscriptionController::index
 * @see app/Http/Controllers/Owner/SubscriptionController.php:23
 * @route '/owner/subscription'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::checkout
 * @see app/Http/Controllers/Owner/SubscriptionController.php:61
 * @route '/owner/subscription/checkout'
 */
export const checkout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

checkout.definition = {
    methods: ["post"],
    url: '/owner/subscription/checkout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::checkout
 * @see app/Http/Controllers/Owner/SubscriptionController.php:61
 * @route '/owner/subscription/checkout'
 */
checkout.url = (options?: RouteQueryOptions) => {
    return checkout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::checkout
 * @see app/Http/Controllers/Owner/SubscriptionController.php:61
 * @route '/owner/subscription/checkout'
 */
checkout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: checkout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::success
 * @see app/Http/Controllers/Owner/SubscriptionController.php:93
 * @route '/owner/subscription/success'
 */
export const success = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(options),
    method: 'get',
})

success.definition = {
    methods: ["get","head"],
    url: '/owner/subscription/success',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::success
 * @see app/Http/Controllers/Owner/SubscriptionController.php:93
 * @route '/owner/subscription/success'
 */
success.url = (options?: RouteQueryOptions) => {
    return success.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::success
 * @see app/Http/Controllers/Owner/SubscriptionController.php:93
 * @route '/owner/subscription/success'
 */
success.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: success.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SubscriptionController::success
 * @see app/Http/Controllers/Owner/SubscriptionController.php:93
 * @route '/owner/subscription/success'
 */
success.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: success.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::expired
 * @see app/Http/Controllers/Owner/SubscriptionController.php:48
 * @route '/owner/subscription/expired'
 */
export const expired = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: expired.url(options),
    method: 'get',
})

expired.definition = {
    methods: ["get","head"],
    url: '/owner/subscription/expired',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::expired
 * @see app/Http/Controllers/Owner/SubscriptionController.php:48
 * @route '/owner/subscription/expired'
 */
expired.url = (options?: RouteQueryOptions) => {
    return expired.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SubscriptionController::expired
 * @see app/Http/Controllers/Owner/SubscriptionController.php:48
 * @route '/owner/subscription/expired'
 */
expired.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: expired.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SubscriptionController::expired
 * @see app/Http/Controllers/Owner/SubscriptionController.php:48
 * @route '/owner/subscription/expired'
 */
expired.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: expired.url(options),
    method: 'head',
})
const subscription = {
    index: Object.assign(index, index),
checkout: Object.assign(checkout, checkout),
success: Object.assign(success, success),
expired: Object.assign(expired, expired),
}

export default subscription