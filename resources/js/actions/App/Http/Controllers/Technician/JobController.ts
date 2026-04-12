import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:25
 * @route '/technician/jobs'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/technician/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:25
 * @route '/technician/jobs'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:25
 * @route '/technician/jobs'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:25
 * @route '/technician/jobs'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:38
 * @route '/technician/jobs/{job}'
 */
export const show = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/technician/jobs/{job}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:38
 * @route '/technician/jobs/{job}'
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
 * @see app/Http/Controllers/Technician/JobController.php:38
 * @route '/technician/jobs/{job}'
 */
show.get = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::show
 * @see app/Http/Controllers/Technician/JobController.php:38
 * @route '/technician/jobs/{job}'
 */
show.head = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

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
* @see \App\Http\Controllers\Technician\JobController::apiShow
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
export const apiShow = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: apiShow.url(args, options),
    method: 'get',
})

apiShow.definition = {
    methods: ["get","head"],
    url: '/api/technician/jobs/{job}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::apiShow
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
apiShow.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return apiShow.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::apiShow
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
apiShow.get = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: apiShow.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::apiShow
 * @see app/Http/Controllers/Technician/JobController.php:64
 * @route '/api/technician/jobs/{job}'
 */
apiShow.head = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: apiShow.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateStatus
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
export const updateStatus = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

updateStatus.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/status',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateStatus
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
updateStatus.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateStatus.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateStatus
 * @see app/Http/Controllers/Technician/JobController.php:73
 * @route '/api/technician/jobs/{job}/status'
 */
updateStatus.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateNotes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
export const updateNotes = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateNotes.url(args, options),
    method: 'patch',
})

updateNotes.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/notes',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateNotes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
updateNotes.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateNotes.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateNotes
 * @see app/Http/Controllers/Technician/JobController.php:103
 * @route '/api/technician/jobs/{job}/notes'
 */
updateNotes.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateNotes.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateCustomerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
export const updateCustomerNotes = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCustomerNotes.url(args, options),
    method: 'patch',
})

updateCustomerNotes.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/customer-notes',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateCustomerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
updateCustomerNotes.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateCustomerNotes.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateCustomerNotes
 * @see app/Http/Controllers/Technician/JobController.php:116
 * @route '/api/technician/jobs/{job}/customer-notes'
 */
updateCustomerNotes.patch = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCustomerNotes.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::toggleChecklistItem
 * @see app/Http/Controllers/Technician/JobController.php:129
 * @route '/api/technician/jobs/{job}/checklist/{item}'
 */
export const toggleChecklistItem = (args: { job: number | { id: number }, item: number | { id: number } } | [job: number | { id: number }, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleChecklistItem.url(args, options),
    method: 'patch',
})

toggleChecklistItem.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/checklist/{item}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::toggleChecklistItem
 * @see app/Http/Controllers/Technician/JobController.php:129
 * @route '/api/technician/jobs/{job}/checklist/{item}'
 */
toggleChecklistItem.url = (args: { job: number | { id: number }, item: number | { id: number } } | [job: number | { id: number }, item: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return toggleChecklistItem.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace('{item}', parsedArgs.item.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::toggleChecklistItem
 * @see app/Http/Controllers/Technician/JobController.php:129
 * @route '/api/technician/jobs/{job}/checklist/{item}'
 */
toggleChecklistItem.patch = (args: { job: number | { id: number }, item: number | { id: number } } | [job: number | { id: number }, item: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: toggleChecklistItem.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::uploadPhoto
 * @see app/Http/Controllers/Technician/JobController.php:145
 * @route '/api/technician/jobs/{job}/photos'
 */
export const uploadPhoto = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadPhoto.url(args, options),
    method: 'post',
})

uploadPhoto.definition = {
    methods: ["post"],
    url: '/api/technician/jobs/{job}/photos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Technician\JobController::uploadPhoto
 * @see app/Http/Controllers/Technician/JobController.php:145
 * @route '/api/technician/jobs/{job}/photos'
 */
uploadPhoto.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return uploadPhoto.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::uploadPhoto
 * @see app/Http/Controllers/Technician/JobController.php:145
 * @route '/api/technician/jobs/{job}/photos'
 */
uploadPhoto.post = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: uploadPhoto.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Technician\JobController::deletePhoto
 * @see app/Http/Controllers/Technician/JobController.php:172
 * @route '/api/technician/jobs/{job}/photos/{attachment}'
 */
export const deletePhoto = (args: { job: number | { id: number }, attachment: number | { id: number } } | [job: number | { id: number }, attachment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deletePhoto.url(args, options),
    method: 'delete',
})

deletePhoto.definition = {
    methods: ["delete"],
    url: '/api/technician/jobs/{job}/photos/{attachment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Technician\JobController::deletePhoto
 * @see app/Http/Controllers/Technician/JobController.php:172
 * @route '/api/technician/jobs/{job}/photos/{attachment}'
 */
deletePhoto.url = (args: { job: number | { id: number }, attachment: number | { id: number } } | [job: number | { id: number }, attachment: number | { id: number } ], options?: RouteQueryOptions) => {
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

    return deletePhoto.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace('{attachment}', parsedArgs.attachment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::deletePhoto
 * @see app/Http/Controllers/Technician/JobController.php:172
 * @route '/api/technician/jobs/{job}/photos/{attachment}'
 */
deletePhoto.delete = (args: { job: number | { id: number }, attachment: number | { id: number } } | [job: number | { id: number }, attachment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deletePhoto.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Technician\JobController::addLineItem
 * @see app/Http/Controllers/Technician/JobController.php:183
 * @route '/api/technician/jobs/{job}/line-items'
 */
export const addLineItem = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addLineItem.url(args, options),
    method: 'post',
})

addLineItem.definition = {
    methods: ["post"],
    url: '/api/technician/jobs/{job}/line-items',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Technician\JobController::addLineItem
 * @see app/Http/Controllers/Technician/JobController.php:183
 * @route '/api/technician/jobs/{job}/line-items'
 */
addLineItem.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return addLineItem.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::addLineItem
 * @see app/Http/Controllers/Technician/JobController.php:183
 * @route '/api/technician/jobs/{job}/line-items'
 */
addLineItem.post = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addLineItem.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Technician\JobController::updateLineItem
 * @see app/Http/Controllers/Technician/JobController.php:209
 * @route '/api/technician/jobs/{job}/line-items/{lineItem}'
 */
export const updateLineItem = (args: { job: number | { id: number }, lineItem: number | { id: number } } | [job: number | { id: number }, lineItem: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateLineItem.url(args, options),
    method: 'patch',
})

updateLineItem.definition = {
    methods: ["patch"],
    url: '/api/technician/jobs/{job}/line-items/{lineItem}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Technician\JobController::updateLineItem
 * @see app/Http/Controllers/Technician/JobController.php:209
 * @route '/api/technician/jobs/{job}/line-items/{lineItem}'
 */
updateLineItem.url = (args: { job: number | { id: number }, lineItem: number | { id: number } } | [job: number | { id: number }, lineItem: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                    lineItem: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                                lineItem: typeof args.lineItem === 'object'
                ? args.lineItem.id
                : args.lineItem,
                }

    return updateLineItem.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace('{lineItem}', parsedArgs.lineItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::updateLineItem
 * @see app/Http/Controllers/Technician/JobController.php:209
 * @route '/api/technician/jobs/{job}/line-items/{lineItem}'
 */
updateLineItem.patch = (args: { job: number | { id: number }, lineItem: number | { id: number } } | [job: number | { id: number }, lineItem: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateLineItem.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Technician\JobController::deleteLineItem
 * @see app/Http/Controllers/Technician/JobController.php:225
 * @route '/api/technician/jobs/{job}/line-items/{lineItem}'
 */
export const deleteLineItem = (args: { job: number | { id: number }, lineItem: number | { id: number } } | [job: number | { id: number }, lineItem: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteLineItem.url(args, options),
    method: 'delete',
})

deleteLineItem.definition = {
    methods: ["delete"],
    url: '/api/technician/jobs/{job}/line-items/{lineItem}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Technician\JobController::deleteLineItem
 * @see app/Http/Controllers/Technician/JobController.php:225
 * @route '/api/technician/jobs/{job}/line-items/{lineItem}'
 */
deleteLineItem.url = (args: { job: number | { id: number }, lineItem: number | { id: number } } | [job: number | { id: number }, lineItem: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                    lineItem: args[1],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                                lineItem: typeof args.lineItem === 'object'
                ? args.lineItem.id
                : args.lineItem,
                }

    return deleteLineItem.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace('{lineItem}', parsedArgs.lineItem.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::deleteLineItem
 * @see app/Http/Controllers/Technician/JobController.php:225
 * @route '/api/technician/jobs/{job}/line-items/{lineItem}'
 */
deleteLineItem.delete = (args: { job: number | { id: number }, lineItem: number | { id: number } } | [job: number | { id: number }, lineItem: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: deleteLineItem.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Technician\JobController::catalogItems
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
export const catalogItems = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: catalogItems.url(options),
    method: 'get',
})

catalogItems.definition = {
    methods: ["get","head"],
    url: '/api/technician/catalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::catalogItems
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
catalogItems.url = (options?: RouteQueryOptions) => {
    return catalogItems.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::catalogItems
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
catalogItems.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: catalogItems.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::catalogItems
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
catalogItems.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: catalogItems.url(options),
    method: 'head',
})
const JobController = { index, show, today, apiShow, updateStatus, updateNotes, updateCustomerNotes, toggleChecklistItem, uploadPhoto, deletePhoto, addLineItem, updateLineItem, deleteLineItem, catalogItems }

export default JobController