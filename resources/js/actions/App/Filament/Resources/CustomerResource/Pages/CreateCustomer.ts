import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CustomerResource\Pages\CreateCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/CreateCustomer.php:7
 * @route '/admin/customers/create'
 */
const CreateCustomer = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateCustomer.url(options),
    method: 'get',
})

CreateCustomer.definition = {
    methods: ["get","head"],
    url: '/admin/customers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CustomerResource\Pages\CreateCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/CreateCustomer.php:7
 * @route '/admin/customers/create'
 */
CreateCustomer.url = (options?: RouteQueryOptions) => {
    return CreateCustomer.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\CustomerResource\Pages\CreateCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/CreateCustomer.php:7
 * @route '/admin/customers/create'
 */
CreateCustomer.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateCustomer.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CustomerResource\Pages\CreateCustomer::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/CreateCustomer.php:7
 * @route '/admin/customers/create'
 */
CreateCustomer.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateCustomer.url(options),
    method: 'head',
})
export default CreateCustomer