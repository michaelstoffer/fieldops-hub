import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\CreateMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/CreateMessageTemplate.php:7
 * @route '/admin/message-templates/create'
 */
const CreateMessageTemplate = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateMessageTemplate.url(options),
    method: 'get',
})

CreateMessageTemplate.definition = {
    methods: ["get","head"],
    url: '/admin/message-templates/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\CreateMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/CreateMessageTemplate.php:7
 * @route '/admin/message-templates/create'
 */
CreateMessageTemplate.url = (options?: RouteQueryOptions) => {
    return CreateMessageTemplate.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\CreateMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/CreateMessageTemplate.php:7
 * @route '/admin/message-templates/create'
 */
CreateMessageTemplate.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateMessageTemplate.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\CreateMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/CreateMessageTemplate.php:7
 * @route '/admin/message-templates/create'
 */
CreateMessageTemplate.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateMessageTemplate.url(options),
    method: 'head',
})
export default CreateMessageTemplate