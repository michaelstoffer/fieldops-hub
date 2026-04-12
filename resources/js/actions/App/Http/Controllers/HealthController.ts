import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\HealthController::liveness
 * @see app/Http/Controllers/HealthController.php:16
 * @route '/health'
 */
export const liveness = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: liveness.url(options),
    method: 'get',
})

liveness.definition = {
    methods: ["get","head"],
    url: '/health',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\HealthController::liveness
 * @see app/Http/Controllers/HealthController.php:16
 * @route '/health'
 */
liveness.url = (options?: RouteQueryOptions) => {
    return liveness.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\HealthController::liveness
 * @see app/Http/Controllers/HealthController.php:16
 * @route '/health'
 */
liveness.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: liveness.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\HealthController::liveness
 * @see app/Http/Controllers/HealthController.php:16
 * @route '/health'
 */
liveness.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: liveness.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\HealthController::readiness
 * @see app/Http/Controllers/HealthController.php:25
 * @route '/health/ready'
 */
export const readiness = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: readiness.url(options),
    method: 'get',
})

readiness.definition = {
    methods: ["get","head"],
    url: '/health/ready',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\HealthController::readiness
 * @see app/Http/Controllers/HealthController.php:25
 * @route '/health/ready'
 */
readiness.url = (options?: RouteQueryOptions) => {
    return readiness.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\HealthController::readiness
 * @see app/Http/Controllers/HealthController.php:25
 * @route '/health/ready'
 */
readiness.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: readiness.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\HealthController::readiness
 * @see app/Http/Controllers/HealthController.php:25
 * @route '/health/ready'
 */
readiness.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: readiness.url(options),
    method: 'head',
})
const HealthController = { liveness, readiness }

export default HealthController