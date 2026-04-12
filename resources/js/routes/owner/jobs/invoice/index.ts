import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\InvoiceController::generate
 * @see app/Http/Controllers/Owner/InvoiceController.php:62
 * @route '/owner/jobs/{job}/invoice'
 */
export const generate = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})

generate.definition = {
    methods: ["post"],
    url: '/owner/jobs/{job}/invoice',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::generate
 * @see app/Http/Controllers/Owner/InvoiceController.php:62
 * @route '/owner/jobs/{job}/invoice'
 */
generate.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return generate.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::generate
 * @see app/Http/Controllers/Owner/InvoiceController.php:62
 * @route '/owner/jobs/{job}/invoice'
 */
generate.post = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generate.url(args, options),
    method: 'post',
})
const invoice = {
    generate: Object.assign(generate, generate),
}

export default invoice