import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\PropertyController::create
 * @see app/Http/Controllers/Owner/PropertyController.php:18
 * @route '/owner/customers/{customer}/properties/create'
 */
export const create = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/owner/customers/{customer}/properties/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\PropertyController::create
 * @see app/Http/Controllers/Owner/PropertyController.php:18
 * @route '/owner/customers/{customer}/properties/create'
 */
create.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return create.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\PropertyController::create
 * @see app/Http/Controllers/Owner/PropertyController.php:18
 * @route '/owner/customers/{customer}/properties/create'
 */
create.get = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\PropertyController::create
 * @see app/Http/Controllers/Owner/PropertyController.php:18
 * @route '/owner/customers/{customer}/properties/create'
 */
create.head = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\PropertyController::store
 * @see app/Http/Controllers/Owner/PropertyController.php:27
 * @route '/owner/customers/{customer}/properties'
 */
export const store = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/customers/{customer}/properties',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\PropertyController::store
 * @see app/Http/Controllers/Owner/PropertyController.php:27
 * @route '/owner/customers/{customer}/properties'
 */
store.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return store.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\PropertyController::store
 * @see app/Http/Controllers/Owner/PropertyController.php:27
 * @route '/owner/customers/{customer}/properties'
 */
store.post = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\PropertyController::edit
 * @see app/Http/Controllers/Owner/PropertyController.php:48
 * @route '/owner/properties/{property}/edit'
 */
export const edit = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/owner/properties/{property}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\PropertyController::edit
 * @see app/Http/Controllers/Owner/PropertyController.php:48
 * @route '/owner/properties/{property}/edit'
 */
edit.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { property: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { property: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    property: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        property: typeof args.property === 'object'
                ? args.property.id
                : args.property,
                }

    return edit.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\PropertyController::edit
 * @see app/Http/Controllers/Owner/PropertyController.php:48
 * @route '/owner/properties/{property}/edit'
 */
edit.get = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\PropertyController::edit
 * @see app/Http/Controllers/Owner/PropertyController.php:48
 * @route '/owner/properties/{property}/edit'
 */
edit.head = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\PropertyController::update
 * @see app/Http/Controllers/Owner/PropertyController.php:58
 * @route '/owner/properties/{property}'
 */
export const update = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

update.definition = {
    methods: ["patch"],
    url: '/owner/properties/{property}',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\Owner\PropertyController::update
 * @see app/Http/Controllers/Owner/PropertyController.php:58
 * @route '/owner/properties/{property}'
 */
update.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { property: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { property: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    property: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        property: typeof args.property === 'object'
                ? args.property.id
                : args.property,
                }

    return update.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\PropertyController::update
 * @see app/Http/Controllers/Owner/PropertyController.php:58
 * @route '/owner/properties/{property}'
 */
update.patch = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\PropertyController::destroy
 * @see app/Http/Controllers/Owner/PropertyController.php:96
 * @route '/owner/properties/{property}'
 */
export const destroy = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/properties/{property}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\PropertyController::destroy
 * @see app/Http/Controllers/Owner/PropertyController.php:96
 * @route '/owner/properties/{property}'
 */
destroy.url = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { property: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { property: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    property: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        property: typeof args.property === 'object'
                ? args.property.id
                : args.property,
                }

    return destroy.definition.url
            .replace('{property}', parsedArgs.property.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\PropertyController::destroy
 * @see app/Http/Controllers/Owner/PropertyController.php:96
 * @route '/owner/properties/{property}'
 */
destroy.delete = (args: { property: number | { id: number } } | [property: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const PropertyController = { create, store, edit, update, destroy }

export default PropertyController