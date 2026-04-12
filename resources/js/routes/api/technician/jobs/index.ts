import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
import checklist from './checklist'
import photos from './photos'
import lineItems from './line-items'
/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
export const today = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: today.url(options),
    method: 'get',
})

today.definition = {
    methods: ["get","head"],
    url: '/api/technician/jobs/today',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
today.url = (options?: RouteQueryOptions) => {
    return today.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
today.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: today.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::today
 * @see app/Http/Controllers/Technician/JobController.php:54
 * @route '/api/technician/jobs/today'
 */
today.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: today.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
export const show = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/api/technician/jobs/{job}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
show.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
show.get = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
show.head = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Technician\JobController::status
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
export const status = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

status.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::status
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
status.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return status.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::status
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
status.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::notes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
export const notes = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: notes.url(args, options),
    method: 'patch',
})

notes.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/notes',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::notes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
notes.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return notes.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::notes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
notes.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: notes.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::customerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
export const customerNotes = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: customerNotes.url(args, options),
    method: 'patch',
})

customerNotes.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/customer-notes',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::customerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
customerNotes.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return customerNotes.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::customerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
customerNotes.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: customerNotes.url(args, options),
    method: 'patch',
})
const jobs = {
    today: Object.assign(today, today),
show: Object.assign(show, show),
status: Object.assign(status, status),
notes: Object.assign(notes, notes),
customerNotes: Object.assign(customerNotes, customerNotes),
checklist: Object.assign(checklist, checklist),
photos: Object.assign(photos, photos),
lineItems: Object.assign(lineItems, lineItems),
}

export default jobs