import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\JobTypeController::activate
 * @see app/Http/Controllers/Owner/JobTypeController.php:96
 * @route '/owner/job-types/{jobType}/activate'
 */
export const activate = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: activate.url(args, options),
    method: 'patch',
})

activate.definition = {
    methods: ["patch"],
    url: '/owner/job-types/{jobType}/activate',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::activate
 * @see app/Http/Controllers/Owner/JobTypeController.php:96
 * @route '/owner/job-types/{jobType}/activate'
 */
activate.url = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return activate.definition.url
            .replace('{jobType}', parsedArgs.jobType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::activate
 * @see app/Http/Controllers/Owner/JobTypeController.php:96
 * @route '/owner/job-types/{jobType}/activate'
 */
activate.patch = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: activate.url(args, options),
    method: 'patch',
})
const jobTypes = {
    activate: Object.assign(activate, activate),
}

export default jobTypes