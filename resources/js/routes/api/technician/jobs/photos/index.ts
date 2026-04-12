import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Technician\JobController::store
 * @see app/Http/Controllers/Technician/JobController.php:145
 * @route '/api/technician/jobs/{job}/photos'
 */
export const store = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/api/technician/jobs/{job}/photos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Technician\JobController::store
 * @see app/Http/Controllers/Technician/JobController.php:145
 * @route '/api/technician/jobs/{job}/photos'
 */
store.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                }

    return store.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::store
 * @see app/Http/Controllers/Technician/JobController.php:145
 * @route '/api/technician/jobs/{job}/photos'
 */
store.post = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Technician\JobController::destroy
 * @see app/Http/Controllers/Technician/JobController.php:172
 * @route '/api/technician/jobs/{job}/photos/{attachment}'
 */
export const destroy = (args: { job: number | { id: number }, attachment: number | { id: number } } | [job: number | { id: number }, attachment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/api/technician/jobs/{job}/photos/{attachment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Technician\JobController::destroy
 * @see app/Http/Controllers/Technician/JobController.php:172
 * @route '/api/technician/jobs/{job}/photos/{attachment}'
 */
destroy.url = (args: { job: number | { id: number }, attachment: number | { id: number } } | [job: number | { id: number }, attachment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                    attachment: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                                attachment: typeof args.attachment === 'object'
                ? args.attachment.id
                : args.attachment,
                }

    return destroy.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace('{attachment}', parsedArgs.attachment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::destroy
 * @see app/Http/Controllers/Technician/JobController.php:172
 * @route '/api/technician/jobs/{job}/photos/{attachment}'
 */
destroy.delete = (args: { job: number | { id: number }, attachment: number | { id: number } } | [job: number | { id: number }, attachment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const photos = {
    store: Object.assign(store, store),
destroy: Object.assign(destroy, destroy),
}

export default photos