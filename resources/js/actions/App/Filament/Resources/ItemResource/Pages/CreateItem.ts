import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\ItemResource\Pages\CreateItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/CreateItem.php:7
 * @route '/admin/items/create'
 */
const CreateItem = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateItem.url(options),
    method: 'get',
})

CreateItem.definition = {
    methods: ["get","head"],
    url: '/admin/items/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\ItemResource\Pages\CreateItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/CreateItem.php:7
 * @route '/admin/items/create'
 */
CreateItem.url = (options?: RouteQueryOptions) => {
    return CreateItem.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\ItemResource\Pages\CreateItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/CreateItem.php:7
 * @route '/admin/items/create'
 */
CreateItem.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateItem.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\ItemResource\Pages\CreateItem::__invoke
 * @see app/Filament/Resources/ItemResource/Pages/CreateItem.php:7
 * @route '/admin/items/create'
 */
CreateItem.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateItem.url(options),
    method: 'head',
})
export default CreateItem