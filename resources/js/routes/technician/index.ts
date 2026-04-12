import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import jobs from './jobs'
/**
* @see \App\Http\Controllers\Technician\DashboardController::dashboard
 * @see app/Http/Controllers/Technician/DashboardController.php:13
 * @route '/technician/dashboard'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/technician/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Technician\DashboardController::dashboard
 * @see app/Http/Controllers/Technician/DashboardController.php:13
 * @route '/technician/dashboard'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Technician\DashboardController::dashboard
 * @see app/Http/Controllers/Technician/DashboardController.php:13
 * @route '/technician/dashboard'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Technician\DashboardController::dashboard
 * @see app/Http/Controllers/Technician/DashboardController.php:13
 * @route '/technician/dashboard'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})
const technician = {
    dashboard: Object.assign(dashboard, dashboard),
jobs: Object.assign(jobs, jobs),
}

export default technician