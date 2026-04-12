import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../../wayfinder'
/**
* @see \App\Filament\Resources\JobTypeResource\Pages\CreateJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/CreateJobType.php:7
 * @route '/admin/job-types/create'
 */
const CreateJobType = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateJobType.url(options),
    method: 'get',
})

CreateJobType.definition = {
    methods: ["get","head"],
    url: '/admin/job-types/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Filament\Resources\JobTypeResource\Pages\CreateJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/CreateJobType.php:7
 * @route '/admin/job-types/create'
 */
CreateJobType.url = (options?: RouteQueryOptions) => {
    return CreateJobType.definition.url + queryParams(options)
}

/**
* @see \App\Filament\Resources\JobTypeResource\Pages\CreateJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/CreateJobType.php:7
 * @route '/admin/job-types/create'
 */
CreateJobType.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CreateJobType.url(options),
    method: 'get',
})
/**
* @see \App\Filament\Resources\JobTypeResource\Pages\CreateJobType::__invoke
 * @see app/Filament/Resources/JobTypeResource/Pages/CreateJobType.php:7
 * @route '/admin/job-types/create'
 */
CreateJobType.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CreateJobType.url(options),
    method: 'head',
})
export default CreateJobType