import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\PropertyResource\Pages\CreateProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/CreateProperty.php:7
 * @route '/admin/properties/create'
 */
const CreateProperty = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateProperty.url(options),
    method: 'get',
})

CreateProperty.definition = {
    methods: ["get","head"],
    url: '/admin/properties/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\PropertyResource\Pages\CreateProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/CreateProperty.php:7
 * @route '/admin/properties/create'
 */
CreateProperty.url = (options?: RouteQueryOptions) => {
    return CreateProperty.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\PropertyResource\Pages\CreateProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/CreateProperty.php:7
 * @route '/admin/properties/create'
 */
CreateProperty.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateProperty.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\PropertyResource\Pages\CreateProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/CreateProperty.php:7
 * @route '/admin/properties/create'
 */
CreateProperty.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateProperty.url(options),
    method: 'head',
})
export default CreateProperty