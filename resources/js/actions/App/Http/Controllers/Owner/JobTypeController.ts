import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\JobTypeController::quickCreate
 * @see app/Http/Controllers/Owner/JobTypeController.php:76
 * @route '/owner/job-types/quick-create'
 */
export const quickCreate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quickCreate.url(options),
    method: 'post',
})

quickCreate.definition = {
    methods: ["post"],
    url: '/owner/job-types/quick-create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::quickCreate
 * @see app/Http/Controllers/Owner/JobTypeController.php:76
 * @route '/owner/job-types/quick-create'
 */
quickCreate.url = (options?: RouteQueryOptions) => {
    return quickCreate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::quickCreate
 * @see app/Http/Controllers/Owner/JobTypeController.php:76
 * @route '/owner/job-types/quick-create'
 */
quickCreate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quickCreate.url(options),
    method: 'post',
})

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

/**
* @see \App\Http\Controllers\Owner\JobTypeController::index
 * @see app/Http/Controllers/Owner/JobTypeController.php:15
 * @route '/owner/job-types'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/job-types',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::index
 * @see app/Http/Controllers/Owner/JobTypeController.php:15
 * @route '/owner/job-types'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::index
 * @see app/Http/Controllers/Owner/JobTypeController.php:15
 * @route '/owner/job-types'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobTypeController::index
 * @see app/Http/Controllers/Owner/JobTypeController.php:15
 * @route '/owner/job-types'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobTypeController::create
 * @see app/Http/Controllers/Owner/JobTypeController.php:26
 * @route '/owner/job-types/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/owner/job-types/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::create
 * @see app/Http/Controllers/Owner/JobTypeController.php:26
 * @route '/owner/job-types/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::create
 * @see app/Http/Controllers/Owner/JobTypeController.php:26
 * @route '/owner/job-types/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobTypeController::create
 * @see app/Http/Controllers/Owner/JobTypeController.php:26
 * @route '/owner/job-types/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobTypeController::store
 * @see app/Http/Controllers/Owner/JobTypeController.php:31
 * @route '/owner/job-types'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/job-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::store
 * @see app/Http/Controllers/Owner/JobTypeController.php:31
 * @route '/owner/job-types'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::store
 * @see app/Http/Controllers/Owner/JobTypeController.php:31
 * @route '/owner/job-types'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\JobTypeController::edit
 * @see app/Http/Controllers/Owner/JobTypeController.php:50
 * @route '/owner/job-types/{job_type}/edit'
 */
export const edit = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/owner/job-types/{job_type}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::edit
 * @see app/Http/Controllers/Owner/JobTypeController.php:50
 * @route '/owner/job-types/{job_type}/edit'
 */
edit.url = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job_type: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job_type: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job_type: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job_type: typeof args.job_type === 'object'
                ? args.job_type.id
                : args.job_type,
                }

    return edit.definition.url
            .replace('{job_type}', parsedArgs.job_type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::edit
 * @see app/Http/Controllers/Owner/JobTypeController.php:50
 * @route '/owner/job-types/{job_type}/edit'
 */
edit.get = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\JobTypeController::edit
 * @see app/Http/Controllers/Owner/JobTypeController.php:50
 * @route '/owner/job-types/{job_type}/edit'
 */
edit.head = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\JobTypeController::update
 * @see app/Http/Controllers/Owner/JobTypeController.php:59
 * @route '/owner/job-types/{job_type}'
 */
export const update = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/owner/job-types/{job_type}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::update
 * @see app/Http/Controllers/Owner/JobTypeController.php:59
 * @route '/owner/job-types/{job_type}'
 */
update.url = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job_type: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job_type: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job_type: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job_type: typeof args.job_type === 'object'
                ? args.job_type.id
                : args.job_type,
                }

    return update.definition.url
            .replace('{job_type}', parsedArgs.job_type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::update
 * @see app/Http/Controllers/Owner/JobTypeController.php:59
 * @route '/owner/job-types/{job_type}'
 */
update.put = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Owner\JobTypeController::update
 * @see app/Http/Controllers/Owner/JobTypeController.php:59
 * @route '/owner/job-types/{job_type}'
 */
update.patch = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\JobTypeController::destroy
 * @see app/Http/Controllers/Owner/JobTypeController.php:105
 * @route '/owner/job-types/{job_type}'
 */
export const destroy = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/job-types/{job_type}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\JobTypeController::destroy
 * @see app/Http/Controllers/Owner/JobTypeController.php:105
 * @route '/owner/job-types/{job_type}'
 */
destroy.url = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job_type: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job_type: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job_type: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job_type: typeof args.job_type === 'object'
                ? args.job_type.id
                : args.job_type,
                }

    return destroy.definition.url
            .replace('{job_type}', parsedArgs.job_type.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\JobTypeController::destroy
 * @see app/Http/Controllers/Owner/JobTypeController.php:105
 * @route '/owner/job-types/{job_type}'
 */
destroy.delete = (args: { job_type: number | { id: number } } | [job_type: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const JobTypeController = { quickCreate, activate, index, create, store, edit, update, destroy }

export default JobTypeController