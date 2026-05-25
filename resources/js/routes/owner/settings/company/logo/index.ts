import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\SettingsController::destroy
 * @see app/Http/Controllers/Owner/SettingsController.php:81
 * @route '/owner/settings/company/logo'
 */
export const destroy = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/settings/company/logo',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::destroy
 * @see app/Http/Controllers/Owner/SettingsController.php:81
 * @route '/owner/settings/company/logo'
 */
destroy.url = (options?: RouteQueryOptions) => {
    return destroy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::destroy
 * @see app/Http/Controllers/Owner/SettingsController.php:81
 * @route '/owner/settings/company/logo'
 */
destroy.delete = (options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(options),
    method: 'delete',
})
const logo = {
    destroy: Object.assign(destroy, destroy),
}

export default logo