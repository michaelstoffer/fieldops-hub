import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\JobResource\Pages\ListJobs::__invoke
 * @see app/Filament/Resources/JobResource/Pages/ListJobs.php:7
 * @route '/admin/jobs'
 */
const ListJobs = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListJobs.url(options),
    method: 'get',
})

ListJobs.definition = {
    methods: ["get","head"],
    url: '/admin/jobs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\JobResource\Pages\ListJobs::__invoke
 * @see app/Filament/Resources/JobResource/Pages/ListJobs.php:7
 * @route '/admin/jobs'
 */
ListJobs.url = (options?: RouteQueryOptions) => {
    return ListJobs.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\JobResource\Pages\ListJobs::__invoke
 * @see app/Filament/Resources/JobResource/Pages/ListJobs.php:7
 * @route '/admin/jobs'
 */
ListJobs.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ListJobs.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\JobResource\Pages\ListJobs::__invoke
 * @see app/Filament/Resources/JobResource/Pages/ListJobs.php:7
 * @route '/admin/jobs'
 */
ListJobs.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ListJobs.url(options),
    method: 'head',
})
export default ListJobs