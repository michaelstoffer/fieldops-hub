import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\JobTypeResource\Pages\EditJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/EditJobType.php:7
 * @route '/admin/job-types/{record}/edit'
 */
const EditJobType = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditJobType.url(args, options),
    method: 'get',
})

EditJobType.definition = {
    methods: ["get","head"],
    url: '/admin/job-types/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\JobTypeResource\Pages\EditJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/EditJobType.php:7
 * @route '/admin/job-types/{record}/edit'
 */
EditJobType.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditJobType.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\JobTypeResource\Pages\EditJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/EditJobType.php:7
 * @route '/admin/job-types/{record}/edit'
 */
EditJobType.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditJobType.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\JobTypeResource\Pages\EditJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/EditJobType.php:7
 * @route '/admin/job-types/{record}/edit'
 */
EditJobType.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditJobType.url(args, options),
    method: 'head',
})
export default EditJobType