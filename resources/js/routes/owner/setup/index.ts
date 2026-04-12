import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import jobTypes from './job-types'
import technicians from './technicians'
/**
* @see \App\Http\Controllers\Owner\SetupController::company
 * @see app/Http/Controllers/Owner/SetupController.php:68
 * @route '/owner/setup/company'
 */
export const company = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: company.url(options),
    method: 'post',
})

company.definition = {
    methods: ["post"],
    url: '/owner/setup/company',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::company
 * @see app/Http/Controllers/Owner/SetupController.php:68
 * @route '/owner/setup/company'
 */
company.url = (options?: RouteQueryOptions) => {
    return company.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::company
 * @see app/Http/Controllers/Owner/SetupController.php:68
 * @route '/owner/setup/company'
 */
company.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: company.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::complete
 * @see app/Http/Controllers/Owner/SetupController.php:144
 * @route '/owner/setup/complete'
 */
export const complete = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/owner/setup/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::complete
 * @see app/Http/Controllers/Owner/SetupController.php:144
 * @route '/owner/setup/complete'
 */
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::complete
 * @see app/Http/Controllers/Owner/SetupController.php:144
 * @route '/owner/setup/complete'
 */
complete.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})
const setup = {
    company: Object.assign(company, company),
jobTypes: Object.assign(jobTypes, jobTypes),
technicians: Object.assign(technicians, technicians),
complete: Object.assign(complete, complete),
}

export default setup