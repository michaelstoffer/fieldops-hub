import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\ItemResource\Pages\EditItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/EditItem.php:7
 * @route '/admin/items/{record}/edit'
 */
const EditItem = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditItem.url(args, options),
    method: 'get',
})

EditItem.definition = {
    methods: ["get","head"],
    url: '/admin/items/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\ItemResource\Pages\EditItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/EditItem.php:7
 * @route '/admin/items/{record}/edit'
 */
EditItem.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditItem.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\ItemResource\Pages\EditItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/EditItem.php:7
 * @route '/admin/items/{record}/edit'
 */
EditItem.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditItem.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\ItemResource\Pages\EditItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/EditItem.php:7
 * @route '/admin/items/{record}/edit'
 */
EditItem.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditItem.url(args, options),
    method: 'head',
})
export default EditItem