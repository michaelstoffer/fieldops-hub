import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\DispatchController::index
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/dispatch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DispatchController::index
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DispatchController::index
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\DispatchController::index
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianLocations
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
export const technicianLocations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicianLocations.url(options),
    method: 'get',
})

technicianLocations.definition = {
    methods: ["get","head"],
    url: '/owner/dispatch/technicians',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianLocations
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
technicianLocations.url = (options?: RouteQueryOptions) => {
    return technicianLocations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianLocations
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
technicianLocations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicianLocations.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianLocations
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
technicianLocations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: technicianLocations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianTrail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
export const technicianTrail = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicianTrail.url(args, options),
    method: 'get',
})

technicianTrail.definition = {
    methods: ["get","head"],
    url: '/owner/dispatch/technicians/{user}/trail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianTrail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
technicianTrail.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { user: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: typeof args.user === 'object'
                ? args.user.id
                : args.user,
                }

    return technicianTrail.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianTrail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
technicianTrail.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicianTrail.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\DispatchController::technicianTrail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
technicianTrail.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: technicianTrail.url(args, options),
    method: 'head',
})
const DispatchController = { index, technicianLocations, technicianTrail }

export default DispatchController