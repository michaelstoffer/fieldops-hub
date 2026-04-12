import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\EditMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/EditMessageTemplate.php:7
 * @route '/admin/message-templates/{record}/edit'
 */
const EditMessageTemplate = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditMessageTemplate.url(args, options),
    method: 'get',
})

EditMessageTemplate.definition = {
    methods: ["get","head"],
    url: '/admin/message-templates/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\EditMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/EditMessageTemplate.php:7
 * @route '/admin/message-templates/{record}/edit'
 */
EditMessageTemplate.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditMessageTemplate.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\EditMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/EditMessageTemplate.php:7
 * @route '/admin/message-templates/{record}/edit'
 */
EditMessageTemplate.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditMessageTemplate.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\MessageTemplateResource\Pages\EditMessageTemplate::__invoke
 * @see app/Filament/Resources/MessageTemplateResource/Pages/EditMessageTemplate.php:7
 * @route '/admin/message-templates/{record}/edit'
 */
EditMessageTemplate.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditMessageTemplate.url(args, options),
    method: 'head',
})
export default EditMessageTemplate