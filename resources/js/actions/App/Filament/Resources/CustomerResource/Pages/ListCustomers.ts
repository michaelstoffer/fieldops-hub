import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\CustomerResource\Pages\ListCustomers::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/ListCustomers.php:7
 * @route '/admin/customers'
 */
const ListCustomers = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCustomers.url(options),
    method: 'get',
})

ListCustomers.definition = {
    methods: ["get","head"],
    url: '/admin/customers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\CustomerResource\Pages\ListCustomers::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/ListCustomers.php:7
 * @route '/admin/customers'
 */
ListCustomers.url = (options?: RouteQueryOptions) => {
    return ListCustomers.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\CustomerResource\Pages\ListCustomers::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/ListCustomers.php:7
 * @route '/admin/customers'
 */
ListCustomers.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListCustomers.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\CustomerResource\Pages\ListCustomers::__invoke
 * @see app/Filament/Resources/CustomerResource/Pages/ListCustomers.php:7
 * @route '/admin/customers'
 */
ListCustomers.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListCustomers.url(options),
    method: 'head',
})
export default ListCustomers