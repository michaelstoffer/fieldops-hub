import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\TeamController::index
 * @see app/Http/Controllers/Owner/TeamController.php:24
 * @route '/owner/team'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/team',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\TeamController::index
 * @see app/Http/Controllers/Owner/TeamController.php:24
 * @route '/owner/team'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\TeamController::index
 * @see app/Http/Controllers/Owner/TeamController.php:24
 * @route '/owner/team'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\TeamController::index
 * @see app/Http/Controllers/Owner/TeamController.php:24
 * @route '/owner/team'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\TeamController::store
 * @see app/Http/Controllers/Owner/TeamController.php:51
 * @route '/owner/team'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/team',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\TeamController::store
 * @see app/Http/Controllers/Owner/TeamController.php:51
 * @route '/owner/team'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\TeamController::store
 * @see app/Http/Controllers/Owner/TeamController.php:51
 * @route '/owner/team'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\TeamController::update
 * @see app/Http/Controllers/Owner/TeamController.php:85
 * @route '/owner/team/{user}'
 */
export const update = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/owner/team/{user}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\TeamController::update
 * @see app/Http/Controllers/Owner/TeamController.php:85
 * @route '/owner/team/{user}'
 */
update.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { user: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: typeof args.user === 'object'
                ? args.user.id
                : args.user,
                }

    return update.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\TeamController::update
 * @see app/Http/Controllers/Owner/TeamController.php:85
 * @route '/owner/team/{user}'
 */
update.patch = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\TeamController::destroy
 * @see app/Http/Controllers/Owner/TeamController.php:121
 * @route '/owner/team/{user}'
 */
export const destroy = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/team/{user}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\TeamController::destroy
 * @see app/Http/Controllers/Owner/TeamController.php:121
 * @route '/owner/team/{user}'
 */
destroy.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { user: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: typeof args.user === 'object'
                ? args.user.id
                : args.user,
                }

    return destroy.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\TeamController::destroy
 * @see app/Http/Controllers/Owner/TeamController.php:121
 * @route '/owner/team/{user}'
 */
destroy.delete = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\TeamController::passwordReset
 * @see app/Http/Controllers/Owner/TeamController.php:111
 * @route '/owner/team/{user}/password-reset'
 */
export const passwordReset = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: passwordReset.url(args, options),
    method: 'post',
})

passwordReset.definition = {
    methods: ["post"],
    url: '/owner/team/{user}/password-reset',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\TeamController::passwordReset
 * @see app/Http/Controllers/Owner/TeamController.php:111
 * @route '/owner/team/{user}/password-reset'
 */
passwordReset.url = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { user: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { user: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    user: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        user: typeof args.user === 'object'
                ? args.user.id
                : args.user,
                }

    return passwordReset.definition.url
            .replace('{user}', parsedArgs.user.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\TeamController::passwordReset
 * @see app/Http/Controllers/Owner/TeamController.php:111
 * @route '/owner/team/{user}/password-reset'
 */
passwordReset.post = (args: { user: number | { id: number } } | [user: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: passwordReset.url(args, options),
    method: 'post',
})
const team = {
    index: Object.assign(index, index),
store: Object.assign(store, store),
update: Object.assign(update, update),
destroy: Object.assign(destroy, destroy),
passwordReset: Object.assign(passwordReset, passwordReset),
}

export default team