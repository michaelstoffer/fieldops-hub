import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\ListMessageTemplates::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/ListMessageTemplates.php:7
 * @route '/admin/message-templates'
 */
const ListMessageTemplates = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMessageTemplates.url(options),
    method: 'get',
})

ListMessageTemplates.definition = {
    methods: ["get","head"],
    url: '/admin/message-templates',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\ListMessageTemplates::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/ListMessageTemplates.php:7
 * @route '/admin/message-templates'
 */
ListMessageTemplates.url = (options?: RouteQueryOptions) => {
    return ListMessageTemplates.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\ListMessageTemplates::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/ListMessageTemplates.php:7
 * @route '/admin/message-templates'
 */
ListMessageTemplates.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListMessageTemplates.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\ListMessageTemplates::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/ListMessageTemplates.php:7
 * @route '/admin/message-templates'
 */
ListMessageTemplates.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListMessageTemplates.url(options),
    method: 'head',
})
export default ListMessageTemplates