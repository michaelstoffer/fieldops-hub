import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\PropertyResource\Pages\ListProperties::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/ListProperties.php:7
 * @route '/admin/properties'
 */
const ListProperties = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListProperties.url(options),
    method: 'get',
})

ListProperties.definition = {
    methods: ["get","head"],
    url: '/admin/properties',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\PropertyResource\Pages\ListProperties::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/ListProperties.php:7
 * @route '/admin/properties'
 */
ListProperties.url = (options?: RouteQueryOptions) => {
    return ListProperties.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\PropertyResource\Pages\ListProperties::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/ListProperties.php:7
 * @route '/admin/properties'
 */
ListProperties.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListProperties.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\PropertyResource\Pages\ListProperties::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/ListProperties.php:7
 * @route '/admin/properties'
 */
ListProperties.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListProperties.url(options),
    method: 'head',
})
export default ListProperties