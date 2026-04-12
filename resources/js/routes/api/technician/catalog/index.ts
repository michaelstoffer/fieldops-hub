import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/api/technician/catalog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\JobController::index
 * @see app/Http/Controllers/Technician/JobController.php:235
 * @route '/api/technician/catalog'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})
const catalog = {
    index: Object.assign(index, index),
}

export default catalog