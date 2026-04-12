import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
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
const properties = {
    create: Object.assign(create, create),
store: Object.assign(store, store),
}

export default properties