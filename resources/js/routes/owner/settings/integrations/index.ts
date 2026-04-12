import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\SettingsController::update
 * @see app/Http/Controllers/Owner/SettingsController.php:106
 * @route '/owner/settings/integrations'
 */
export const update = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})

update.definition = {
    methods: ["post"],
    url: '/owner/settings/integrations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SettingsController::update
 * @see app/Http/Controllers/Owner/SettingsController.php:106
 * @route '/owner/settings/integrations'
 */
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SettingsController::update
 * @see app/Http/Controllers/Owner/SettingsController.php:106
 * @route '/owner/settings/integrations'
 */
update.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: update.url(options),
    method: 'post',
})
const integrations = {
    update: Object.assign(update, update),
}

export default integrations