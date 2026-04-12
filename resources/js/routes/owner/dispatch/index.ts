import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\DispatchController::technicians
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
export const technicians = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicians.url(options),
    method: 'get',
})

technicians.definition = {
    methods: ["get","head"],
    url: '/owner/dispatch/technicians',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicians
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
technicians.url = (options?: RouteQueryOptions) => {
    return technicians.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DispatchController::technicians
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
technicians.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicians.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\DispatchController::technicians
 * @see app/Http/Controllers/Owner/DispatchController.php:36
 * @route '/owner/dispatch/technicians'
 */
technicians.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: technicians.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DispatchController::trail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
export const trail = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: trail.url(args, options),
    method: 'get',
})

trail.definition = {
    methods: ["get","head"],
    url: '/owner/dispatch/technicians/{user}/trail',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DispatchController::trail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
trail.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return trail.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DispatchController::trail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
trail.get = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: trail.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\DispatchController::trail
 * @see app/Http/Controllers/Owner/DispatchController.php:105
 * @route '/owner/dispatch/technicians/{user}/trail'
 */
trail.head = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: trail.url(args, options),
    method: 'head',
})
const dispatch = {
    technicians: Object.assign(technicians, technicians),
trail: Object.assign(trail, trail),
}

export default dispatch