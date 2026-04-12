import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import company890735 from './company'
import integrations3d3f6e from './integrations'
/**
* @see \App\Http\Controllers\Owner\SettingsController::company
 * @see app/Http/Controllers/Owner/SettingsController.php:24
 * @route '/owner/settings/company'
 */
export const company = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: company.url(options),
    method: 'get',
})

company.definition = {
    methods: ["get","head"],
    url: '/owner/settings/company',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::company
 * @see app/Http/Controllers/Owner/SettingsController.php:24
 * @route '/owner/settings/company'
 */
company.url = (options?: RouteQueryOptions) => {
    return company.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::company
 * @see app/Http/Controllers/Owner/SettingsController.php:24
 * @route '/owner/settings/company'
 */
company.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: company.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SettingsController::company
 * @see app/Http/Controllers/Owner/SettingsController.php:24
 * @route '/owner/settings/company'
 */
company.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: company.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:83
 * @route '/owner/settings/integrations'
 */
export const integrations = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: integrations.url(options),
    method: 'get',
})

integrations.definition = {
    methods: ["get","head"],
    url: '/owner/settings/integrations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:83
 * @route '/owner/settings/integrations'
 */
integrations.url = (options?: RouteQueryOptions) => {
    return integrations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:83
 * @route '/owner/settings/integrations'
 */
integrations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: integrations.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:83
 * @route '/owner/settings/integrations'
 */
integrations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: integrations.url(options),
    method: 'head',
})
const settings = {
    company: Object.assign(company, company890735),
integrations: Object.assign(integrations, integrations3d3f6e),
}

export default settings