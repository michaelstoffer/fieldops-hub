import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CustomerResource\Pages\EditCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/EditCustomer.php:7
 * @route '/admin/customers/{record}/edit'
 */
const EditCustomer = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCustomer.url(args, options),
    method: 'get',
})

EditCustomer.definition = {
    methods: ["get","head"],
    url: '/admin/customers/{record}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CustomerResource\Pages\EditCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/EditCustomer.php:7
 * @route '/admin/customers/{record}/edit'
 */
EditCustomer.url = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return EditCustomer.definition.url
            .replace('{record}', parsedArgs.record.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Filament\Resources\CustomerResource\Pages\EditCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/EditCustomer.php:7
 * @route '/admin/customers/{record}/edit'
 */
EditCustomer.get = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: EditCustomer.url(args, options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CustomerResource\Pages\EditCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/EditCustomer.php:7
 * @route '/admin/customers/{record}/edit'
 */
EditCustomer.head = (args: { record: string | number } | [record: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: EditCustomer.url(args, options),
    method: 'head',
})
export default EditCustomer