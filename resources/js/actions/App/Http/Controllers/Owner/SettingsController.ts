import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Owner\SettingsController::updateCompany
 * @see app/Http/Controllers/Owner/SettingsController.php:44
 * @route '/owner/settings/company'
 */
export const updateCompany = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompany.url(options),
    method: 'post',
})

updateCompany.definition = {
    methods: ["post"],
    url: '/owner/settings/company',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::updateCompany
 * @see app/Http/Controllers/Owner/SettingsController.php:44
 * @route '/owner/settings/company'
 */
updateCompany.url = (options?: RouteQueryOptions) => {
    return updateCompany.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::updateCompany
 * @see app/Http/Controllers/Owner/SettingsController.php:44
 * @route '/owner/settings/company'
 */
updateCompany.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateCompany.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SettingsController::removeLogo
 * @see app/Http/Controllers/Owner/SettingsController.php:81
 * @route '/owner/settings/company/logo'
 */
export const removeLogo = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeLogo.url(options),
    method: 'delete',
})

removeLogo.definition = {
    methods: ["delete"],
    url: '/owner/settings/company/logo',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::removeLogo
 * @see app/Http/Controllers/Owner/SettingsController.php:81
 * @route '/owner/settings/company/logo'
 */
removeLogo.url = (options?: RouteQueryOptions) => {
    return removeLogo.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::removeLogo
 * @see app/Http/Controllers/Owner/SettingsController.php:81
 * @route '/owner/settings/company/logo'
 */
removeLogo.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeLogo.url(options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:95
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
 * @see app/Http/Controllers/Owner/SettingsController.php:95
 * @route '/owner/settings/integrations'
 */
integrations.url = (options?: RouteQueryOptions) => {
    return integrations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:95
 * @route '/owner/settings/integrations'
 */
integrations.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: integrations.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SettingsController::integrations
 * @see app/Http/Controllers/Owner/SettingsController.php:95
 * @route '/owner/settings/integrations'
 */
integrations.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: integrations.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\SettingsController::updateIntegrations
 * @see app/Http/Controllers/Owner/SettingsController.php:118
 * @route '/owner/settings/integrations'
 */
export const updateIntegrations = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateIntegrations.url(options),
    method: 'post',
})

updateIntegrations.definition = {
    methods: ["post"],
    url: '/owner/settings/integrations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::updateIntegrations
 * @see app/Http/Controllers/Owner/SettingsController.php:118
 * @route '/owner/settings/integrations'
 */
updateIntegrations.url = (options?: RouteQueryOptions) => {
    return updateIntegrations.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::updateIntegrations
 * @see app/Http/Controllers/Owner/SettingsController.php:118
 * @route '/owner/settings/integrations'
 */
updateIntegrations.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: updateIntegrations.url(options),
    method: 'post',
})
const SettingsController = { company, updateCompany, removeLogo, integrations, updateIntegrations }

export default SettingsController