import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\SetupController::store
 * @see app/Http/Controllers/Owner/SetupController.php:96
 * @route '/owner/setup/job-types'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/setup/job-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::store
 * @see app/Http/Controllers/Owner/SetupController.php:96
 * @route '/owner/setup/job-types'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::store
 * @see app/Http/Controllers/Owner/SetupController.php:96
 * @route '/owner/setup/job-types'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::destroy
 * @see app/Http/Controllers/Owner/SetupController.php:114
 * @route '/owner/setup/job-types/{jobType}'
 */
export const destroy = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/setup/job-types/{jobType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::destroy
 * @see app/Http/Controllers/Owner/SetupController.php:114
 * @route '/owner/setup/job-types/{jobType}'
 */
destroy.url = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { jobType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { jobType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    jobType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        jobType: typeof args.jobType === 'object'
                ? args.jobType.id
                : args.jobType,
                }

    return destroy.definition.url
            .replace('{jobType}', parsedArgs.jobType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::destroy
 * @see app/Http/Controllers/Owner/SetupController.php:114
 * @route '/owner/setup/job-types/{jobType}'
 */
destroy.delete = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const jobTypes = {
    store: Object.assign(store, store),
destroy: Object.assign(destroy, destroy),
}

export default jobTypes