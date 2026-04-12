import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\PropertyResource\Pages\EditProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/EditProperty.php:7
 * @route '/admin/properties/{record}/edit'
 */
const EditProperty = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditProperty.url(args, options),
    method: 'get',
})

EditProperty.definition = {
    methods: ["get","head"],
    url: '/admin/properties/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\PropertyResource\Pages\EditProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/EditProperty.php:7
 * @route '/admin/properties/{record}/edit'
 */
EditProperty.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { record: args }
    }

    
    if (Array.isArray(args)) {
        args = {
                    record: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        record: args.record,
                }

    return EditProperty.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\PropertyResource\Pages\EditProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/EditProperty.php:7
 * @route '/admin/properties/{record}/edit'
 */
EditProperty.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditProperty.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\PropertyResource\Pages\EditProperty::__invoke
 * @see app/Filament/Resources/PropertyResource/Pages/EditProperty.php:7
 * @route '/admin/properties/{record}/edit'
 */
EditProperty.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditProperty.url(args, options),
    method: 'head',
})
export default EditProperty