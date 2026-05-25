import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\CustomerController::store
 * @see app/Http/Controllers/Owner/CustomerController.php:123
 * @route '/owner/customers/import'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/customers/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::store
 * @see app/Http/Controllers/Owner/CustomerController.php:123
 * @route '/owner/customers/import'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::store
 * @see app/Http/Controllers/Owner/CustomerController.php:123
 * @route '/owner/customers/import'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})
const importMethod = {
    store: Object.assign(store, store),
}

export default importMethod