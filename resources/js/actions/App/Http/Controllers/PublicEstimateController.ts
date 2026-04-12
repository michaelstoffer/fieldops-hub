import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\PublicEstimateController::show
 * @see app/Http/Controllers/PublicEstimateController.php:13
 * @route '/estimates/{token}'
 */
export const show = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/estimates/{token}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\PublicEstimateController::show
 * @see app/Http/Controllers/PublicEstimateController.php:13
 * @route '/estimates/{token}'
 */
show.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    token: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        token: args.token,
                }

    return show.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEstimateController::show
 * @see app/Http/Controllers/PublicEstimateController.php:13
 * @route '/estimates/{token}'
 */
show.get = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\PublicEstimateController::show
 * @see app/Http/Controllers/PublicEstimateController.php:13
 * @route '/estimates/{token}'
 */
show.head = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\PublicEstimateController::accept
 * @see app/Http/Controllers/PublicEstimateController.php:29
 * @route '/estimates/{token}/accept'
 */
export const accept = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accept.url(args, options),
    method: 'post',
})

accept.definition = {
    methods: ["post"],
    url: '/estimates/{token}/accept',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PublicEstimateController::accept
 * @see app/Http/Controllers/PublicEstimateController.php:29
 * @route '/estimates/{token}/accept'
 */
accept.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    token: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        token: args.token,
                }

    return accept.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEstimateController::accept
 * @see app/Http/Controllers/PublicEstimateController.php:29
 * @route '/estimates/{token}/accept'
 */
accept.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: accept.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\PublicEstimateController::decline
 * @see app/Http/Controllers/PublicEstimateController.php:54
 * @route '/estimates/{token}/decline'
 */
export const decline = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: decline.url(args, options),
    method: 'post',
})

decline.definition = {
    methods: ["post"],
    url: '/estimates/{token}/decline',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\PublicEstimateController::decline
 * @see app/Http/Controllers/PublicEstimateController.php:54
 * @route '/estimates/{token}/decline'
 */
decline.url = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { token: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    token: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        token: args.token,
                }

    return decline.definition.url
            .replace('{token}', parsedArgs.token.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\PublicEstimateController::decline
 * @see app/Http/Controllers/PublicEstimateController.php:54
 * @route '/estimates/{token}/decline'
 */
decline.post = (args: { token: string | number } | [token: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: decline.url(args, options),
    method: 'post',
})
const PublicEstimateController = { show, accept, decline }

export default PublicEstimateController