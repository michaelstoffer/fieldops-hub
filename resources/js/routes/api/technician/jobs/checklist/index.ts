import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Technician\JobController::toggle
 * @see app/Http/Controllers/Technician/JobController.php:129
 * @route '/api/technician/jobs/{job}/checklist/{item}'
 */
export const toggle = (args: { job: number | { id: number }, item: number | { id: number } } | [job: number | { id: number }, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})

toggle.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/checklist/{item}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::toggle
 * @see app/Http/Controllers/Technician/JobController.php:129
 * @route '/api/technician/jobs/{job}/checklist/{item}'
 */
toggle.url = (args: { job: number | { id: number }, item: number | { id: number } } | [job: number | { id: number }, item: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                    item: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                                item: typeof args.item === 'object'
                ? args.item.id
                : args.item,
                }

    return toggle.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::toggle
 * @see app/Http/Controllers/Technician/JobController.php:129
 * @route '/api/technician/jobs/{job}/checklist/{item}'
 */
toggle.patch = (args: { job: number | { id: number }, item: number | { id: number } } | [job: number | { id: number }, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggle.url(args, options),
    method: 'patch',
})
const checklist = {
    toggle: Object.assign(toggle, toggle),
}

export default checklist