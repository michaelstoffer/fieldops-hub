import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\SetupController::store
 * @see app/Http/Controllers/Owner/SetupController.php:122
 * @route '/owner/setup/technicians'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/setup/technicians',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::store
 * @see app/Http/Controllers/Owner/SetupController.php:122
 * @route '/owner/setup/technicians'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::store
 * @see app/Http/Controllers/Owner/SetupController.php:122
 * @route '/owner/setup/technicians'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})
const technicians = {
    store: Object.assign(store, store),
}

export default technicians