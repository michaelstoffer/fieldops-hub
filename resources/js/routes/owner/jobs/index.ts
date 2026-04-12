import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import invoice from './invoice'
/**
* @see \App\Http\Controllers\Owner\JobController::index
 * @see app/Http/Controllers/Owner/JobController.php:23
 * @route '/owner/jobs'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobController::index
 * @see app/Http/Controllers/Owner/JobController.php:23
 * @route '/owner/jobs'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::index
 * @see app/Http/Controllers/Owner/JobController.php:23
 * @route '/owner/jobs'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobController::index
 * @see app/Http/Controllers/Owner/JobController.php:23
 * @route '/owner/jobs'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobController::create
 * @see app/Http/Controllers/Owner/JobController.php:67
 * @route '/owner/jobs/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/owner/jobs/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobController::create
 * @see app/Http/Controllers/Owner/JobController.php:67
 * @route '/owner/jobs/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::create
 * @see app/Http/Controllers/Owner/JobController.php:67
 * @route '/owner/jobs/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobController::create
 * @see app/Http/Controllers/Owner/JobController.php:67
 * @route '/owner/jobs/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobController::store
 * @see app/Http/Controllers/Owner/JobController.php:88
 * @route '/owner/jobs'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/jobs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\JobController::store
 * @see app/Http/Controllers/Owner/JobController.php:88
 * @route '/owner/jobs'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::store
 * @see app/Http/Controllers/Owner/JobController.php:88
 * @route '/owner/jobs'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\JobController::show
 * @see app/Http/Controllers/Owner/JobController.php:55
 * @route '/owner/jobs/{job}'
 */
export const show = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/owner/jobs/{job}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobController::show
 * @see app/Http/Controllers/Owner/JobController.php:55
 * @route '/owner/jobs/{job}'
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
* @see \App\Http\Controllers\Owner\JobController::show
 * @see app/Http/Controllers/Owner/JobController.php:55
 * @route '/owner/jobs/{job}'
 */
show.get = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobController::show
 * @see app/Http/Controllers/Owner/JobController.php:55
 * @route '/owner/jobs/{job}'
 */
show.head = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobController::edit
 * @see app/Http/Controllers/Owner/JobController.php:102
 * @route '/owner/jobs/{job}/edit'
 */
export const edit = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/owner/jobs/{job}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobController::edit
 * @see app/Http/Controllers/Owner/JobController.php:102
 * @route '/owner/jobs/{job}/edit'
 */
edit.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return edit.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::edit
 * @see app/Http/Controllers/Owner/JobController.php:102
 * @route '/owner/jobs/{job}/edit'
 */
edit.get = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobController::edit
 * @see app/Http/Controllers/Owner/JobController.php:102
 * @route '/owner/jobs/{job}/edit'
 */
edit.head = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobController::update
 * @see app/Http/Controllers/Owner/JobController.php:125
 * @route '/owner/jobs/{job}'
 */
export const update = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/owner/jobs/{job}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Owner\JobController::update
 * @see app/Http/Controllers/Owner/JobController.php:125
 * @route '/owner/jobs/{job}'
 */
update.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::update
 * @see app/Http/Controllers/Owner/JobController.php:125
 * @route '/owner/jobs/{job}'
 */
update.put = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Owner\JobController::update
 * @see app/Http/Controllers/Owner/JobController.php:125
 * @route '/owner/jobs/{job}'
 */
update.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\JobController::destroy
 * @see app/Http/Controllers/Owner/JobController.php:135
 * @route '/owner/jobs/{job}'
 */
export const destroy = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/jobs/{job}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\JobController::destroy
 * @see app/Http/Controllers/Owner/JobController.php:135
 * @route '/owner/jobs/{job}'
 */
destroy.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return destroy.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::destroy
 * @see app/Http/Controllers/Owner/JobController.php:135
 * @route '/owner/jobs/{job}'
 */
destroy.delete = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\JobController::status
 * @see app/Http/Controllers/Owner/JobController.php:145
 * @route '/owner/jobs/{job}/status'
 */
export const status = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

status.definition = {
    methods: ["patch"],
    url: '/owner/jobs/{job}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\JobController::status
 * @see app/Http/Controllers/Owner/JobController.php:145
 * @route '/owner/jobs/{job}/status'
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
* @see \App\Http\Controllers\Owner\JobController::status
 * @see app/Http/Controllers/Owner/JobController.php:145
 * @route '/owner/jobs/{job}/status'
 */
status.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\JobController::reschedule
 * @see app/Http/Controllers/Owner/JobController.php:168
 * @route '/owner/jobs/{job}/reschedule'
 */
export const reschedule = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reschedule.url(args, options),
    method: 'patch',
})

reschedule.definition = {
    methods: ["patch"],
    url: '/owner/jobs/{job}/reschedule',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\JobController::reschedule
 * @see app/Http/Controllers/Owner/JobController.php:168
 * @route '/owner/jobs/{job}/reschedule'
 */
reschedule.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return reschedule.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::reschedule
 * @see app/Http/Controllers/Owner/JobController.php:168
 * @route '/owner/jobs/{job}/reschedule'
 */
reschedule.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reschedule.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\JobController::reassign
 * @see app/Http/Controllers/Owner/JobController.php:181
 * @route '/owner/jobs/{job}/reassign'
 */
export const reassign = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reassign.url(args, options),
    method: 'patch',
})

reassign.definition = {
    methods: ["patch"],
    url: '/owner/jobs/{job}/reassign',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\JobController::reassign
 * @see app/Http/Controllers/Owner/JobController.php:181
 * @route '/owner/jobs/{job}/reassign'
 */
reassign.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return reassign.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobController::reassign
 * @see app/Http/Controllers/Owner/JobController.php:181
 * @route '/owner/jobs/{job}/reassign'
 */
reassign.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: reassign.url(args, options),
    method: 'patch',
})
const jobs = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
status: Object.assign(status, status),
reschedule: Object.assign(reschedule, reschedule),
reassign: Object.assign(reassign, reassign),
invoice: Object.assign(invoice, invoice),
}

export default jobs