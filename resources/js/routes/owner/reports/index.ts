import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\ReportingController::jobsByType
 * @see app/Http/Controllers/Owner/ReportingController.php:68
 * @route '/owner/reports/jobs-by-type'
 */
export const jobsByType = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: jobsByType.url(options),
    method: 'get',
})

jobsByType.definition = {
    methods: ["get","head"],
    url: '/owner/reports/jobs-by-type',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\ReportingController::jobsByType
 * @see app/Http/Controllers/Owner/ReportingController.php:68
 * @route '/owner/reports/jobs-by-type'
 */
jobsByType.url = (options?: RouteQueryOptions) => {
    return jobsByType.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\ReportingController::jobsByType
 * @see app/Http/Controllers/Owner/ReportingController.php:68
 * @route '/owner/reports/jobs-by-type'
 */
jobsByType.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: jobsByType.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\ReportingController::jobsByType
 * @see app/Http/Controllers/Owner/ReportingController.php:68
 * @route '/owner/reports/jobs-by-type'
 */
jobsByType.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: jobsByType.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\ReportingController::jobProfitability
 * @see app/Http/Controllers/Owner/ReportingController.php:113
 * @route '/owner/reports/job-profitability'
 */
export const jobProfitability = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: jobProfitability.url(options),
    method: 'get',
})

jobProfitability.definition = {
    methods: ["get","head"],
    url: '/owner/reports/job-profitability',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\ReportingController::jobProfitability
 * @see app/Http/Controllers/Owner/ReportingController.php:113
 * @route '/owner/reports/job-profitability'
 */
jobProfitability.url = (options?: RouteQueryOptions) => {
    return jobProfitability.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\ReportingController::jobProfitability
 * @see app/Http/Controllers/Owner/ReportingController.php:113
 * @route '/owner/reports/job-profitability'
 */
jobProfitability.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: jobProfitability.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\ReportingController::jobProfitability
 * @see app/Http/Controllers/Owner/ReportingController.php:113
 * @route '/owner/reports/job-profitability'
 */
jobProfitability.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: jobProfitability.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\ReportingController::technicianPerformance
 * @see app/Http/Controllers/Owner/ReportingController.php:172
 * @route '/owner/reports/technician-performance'
 */
export const technicianPerformance = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicianPerformance.url(options),
    method: 'get',
})

technicianPerformance.definition = {
    methods: ["get","head"],
    url: '/owner/reports/technician-performance',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\ReportingController::technicianPerformance
 * @see app/Http/Controllers/Owner/ReportingController.php:172
 * @route '/owner/reports/technician-performance'
 */
technicianPerformance.url = (options?: RouteQueryOptions) => {
    return technicianPerformance.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\ReportingController::technicianPerformance
 * @see app/Http/Controllers/Owner/ReportingController.php:172
 * @route '/owner/reports/technician-performance'
 */
technicianPerformance.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: technicianPerformance.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\ReportingController::technicianPerformance
 * @see app/Http/Controllers/Owner/ReportingController.php:172
 * @route '/owner/reports/technician-performance'
 */
technicianPerformance.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: technicianPerformance.url(options),
    method: 'head',
})
const reports = {
    jobsByType: Object.assign(jobsByType, jobsByType),
jobProfitability: Object.assign(jobProfitability, jobProfitability),
technicianPerformance: Object.assign(technicianPerformance, technicianPerformance),
}

export default reports