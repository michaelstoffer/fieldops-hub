import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\EstimateController::index
 * @see app/Http/Controllers/Owner/EstimateController.php:22
 * @route '/owner/estimates'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/estimates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::index
 * @see app/Http/Controllers/Owner/EstimateController.php:22
 * @route '/owner/estimates'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::index
 * @see app/Http/Controllers/Owner/EstimateController.php:22
 * @route '/owner/estimates'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\EstimateController::index
 * @see app/Http/Controllers/Owner/EstimateController.php:22
 * @route '/owner/estimates'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::create
 * @see app/Http/Controllers/Owner/EstimateController.php:66
 * @route '/owner/estimates/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/owner/estimates/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::create
 * @see app/Http/Controllers/Owner/EstimateController.php:66
 * @route '/owner/estimates/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::create
 * @see app/Http/Controllers/Owner/EstimateController.php:66
 * @route '/owner/estimates/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\EstimateController::create
 * @see app/Http/Controllers/Owner/EstimateController.php:66
 * @route '/owner/estimates/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::store
 * @see app/Http/Controllers/Owner/EstimateController.php:87
 * @route '/owner/estimates'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/estimates',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::store
 * @see app/Http/Controllers/Owner/EstimateController.php:87
 * @route '/owner/estimates'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::store
 * @see app/Http/Controllers/Owner/EstimateController.php:87
 * @route '/owner/estimates'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::show
 * @see app/Http/Controllers/Owner/EstimateController.php:52
 * @route '/owner/estimates/{estimate}'
 */
export const show = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/owner/estimates/{estimate}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::show
 * @see app/Http/Controllers/Owner/EstimateController.php:52
 * @route '/owner/estimates/{estimate}'
 */
show.url = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { estimate: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { estimate: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    estimate: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        estimate: typeof args.estimate === 'object'
                ? args.estimate.id
                : args.estimate,
                }

    return show.definition.url
            .replace('{estimate}', parsedArgs.estimate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::show
 * @see app/Http/Controllers/Owner/EstimateController.php:52
 * @route '/owner/estimates/{estimate}'
 */
show.get = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\EstimateController::show
 * @see app/Http/Controllers/Owner/EstimateController.php:52
 * @route '/owner/estimates/{estimate}'
 */
show.head = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::edit
 * @see app/Http/Controllers/Owner/EstimateController.php:133
 * @route '/owner/estimates/{estimate}/edit'
 */
export const edit = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/owner/estimates/{estimate}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::edit
 * @see app/Http/Controllers/Owner/EstimateController.php:133
 * @route '/owner/estimates/{estimate}/edit'
 */
edit.url = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { estimate: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { estimate: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    estimate: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        estimate: typeof args.estimate === 'object'
                ? args.estimate.id
                : args.estimate,
                }

    return edit.definition.url
            .replace('{estimate}', parsedArgs.estimate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::edit
 * @see app/Http/Controllers/Owner/EstimateController.php:133
 * @route '/owner/estimates/{estimate}/edit'
 */
edit.get = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\EstimateController::edit
 * @see app/Http/Controllers/Owner/EstimateController.php:133
 * @route '/owner/estimates/{estimate}/edit'
 */
edit.head = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::update
 * @see app/Http/Controllers/Owner/EstimateController.php:158
 * @route '/owner/estimates/{estimate}'
 */
export const update = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/owner/estimates/{estimate}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::update
 * @see app/Http/Controllers/Owner/EstimateController.php:158
 * @route '/owner/estimates/{estimate}'
 */
update.url = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { estimate: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { estimate: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    estimate: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        estimate: typeof args.estimate === 'object'
                ? args.estimate.id
                : args.estimate,
                }

    return update.definition.url
            .replace('{estimate}', parsedArgs.estimate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::update
 * @see app/Http/Controllers/Owner/EstimateController.php:158
 * @route '/owner/estimates/{estimate}'
 */
update.put = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Owner\EstimateController::update
 * @see app/Http/Controllers/Owner/EstimateController.php:158
 * @route '/owner/estimates/{estimate}'
 */
update.patch = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::destroy
 * @see app/Http/Controllers/Owner/EstimateController.php:261
 * @route '/owner/estimates/{estimate}'
 */
export const destroy = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/estimates/{estimate}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::destroy
 * @see app/Http/Controllers/Owner/EstimateController.php:261
 * @route '/owner/estimates/{estimate}'
 */
destroy.url = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { estimate: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { estimate: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    estimate: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        estimate: typeof args.estimate === 'object'
                ? args.estimate.id
                : args.estimate,
                }

    return destroy.definition.url
            .replace('{estimate}', parsedArgs.estimate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::destroy
 * @see app/Http/Controllers/Owner/EstimateController.php:261
 * @route '/owner/estimates/{estimate}'
 */
destroy.delete = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::send
 * @see app/Http/Controllers/Owner/EstimateController.php:203
 * @route '/owner/estimates/{estimate}/send'
 */
export const send = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(args, options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/owner/estimates/{estimate}/send',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::send
 * @see app/Http/Controllers/Owner/EstimateController.php:203
 * @route '/owner/estimates/{estimate}/send'
 */
send.url = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { estimate: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { estimate: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    estimate: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        estimate: typeof args.estimate === 'object'
                ? args.estimate.id
                : args.estimate,
                }

    return send.definition.url
            .replace('{estimate}', parsedArgs.estimate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::send
 * @see app/Http/Controllers/Owner/EstimateController.php:203
 * @route '/owner/estimates/{estimate}/send'
 */
send.post = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\EstimateController::convert
 * @see app/Http/Controllers/Owner/EstimateController.php:220
 * @route '/owner/estimates/{estimate}/convert'
 */
export const convert = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: convert.url(args, options),
    method: 'post',
})

convert.definition = {
    methods: ["post"],
    url: '/owner/estimates/{estimate}/convert',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\EstimateController::convert
 * @see app/Http/Controllers/Owner/EstimateController.php:220
 * @route '/owner/estimates/{estimate}/convert'
 */
convert.url = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { estimate: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { estimate: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    estimate: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        estimate: typeof args.estimate === 'object'
                ? args.estimate.id
                : args.estimate,
                }

    return convert.definition.url
            .replace('{estimate}', parsedArgs.estimate.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\EstimateController::convert
 * @see app/Http/Controllers/Owner/EstimateController.php:220
 * @route '/owner/estimates/{estimate}/convert'
 */
convert.post = (args: { estimate: number | { id: number } } | [estimate: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: convert.url(args, options),
    method: 'post',
})
const estimates = {
    index: Object.assign(index, index),
create: Object.assign(create, create),
store: Object.assign(store, store),
show: Object.assign(show, show),
edit: Object.assign(edit, edit),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
send: Object.assign(send, send),
convert: Object.assign(convert, convert),
}

export default estimates