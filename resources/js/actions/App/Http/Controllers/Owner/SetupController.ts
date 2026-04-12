import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\SetupController::show
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
export const show = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/owner/setup',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::show
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
show.url = (options?: RouteQueryOptions) => {
    return show.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::show
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
show.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SetupController::show
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
show.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::saveCompany
 * @see app/Http/Controllers/Owner/SetupController.php:68
 * @route '/owner/setup/company'
 */
export const saveCompany = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveCompany.url(options),
    method: 'post',
})

saveCompany.definition = {
    methods: ["post"],
    url: '/owner/setup/company',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::saveCompany
 * @see app/Http/Controllers/Owner/SetupController.php:68
 * @route '/owner/setup/company'
 */
saveCompany.url = (options?: RouteQueryOptions) => {
    return saveCompany.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::saveCompany
 * @see app/Http/Controllers/Owner/SetupController.php:68
 * @route '/owner/setup/company'
 */
saveCompany.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: saveCompany.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::addJobType
 * @see app/Http/Controllers/Owner/SetupController.php:96
 * @route '/owner/setup/job-types'
 */
export const addJobType = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addJobType.url(options),
    method: 'post',
})

addJobType.definition = {
    methods: ["post"],
    url: '/owner/setup/job-types',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::addJobType
 * @see app/Http/Controllers/Owner/SetupController.php:96
 * @route '/owner/setup/job-types'
 */
addJobType.url = (options?: RouteQueryOptions) => {
    return addJobType.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::addJobType
 * @see app/Http/Controllers/Owner/SetupController.php:96
 * @route '/owner/setup/job-types'
 */
addJobType.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addJobType.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::removeJobType
 * @see app/Http/Controllers/Owner/SetupController.php:114
 * @route '/owner/setup/job-types/{jobType}'
 */
export const removeJobType = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeJobType.url(args, options),
    method: 'delete',
})

removeJobType.definition = {
    methods: ["delete"],
    url: '/owner/setup/job-types/{jobType}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::removeJobType
 * @see app/Http/Controllers/Owner/SetupController.php:114
 * @route '/owner/setup/job-types/{jobType}'
 */
removeJobType.url = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { jobType: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { jobType: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    jobType: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        jobType: typeof args.jobType === 'object'
                ? args.jobType.id
                : args.jobType,
                }

    return removeJobType.definition.url
            .replace('{jobType}', parsedArgs.jobType.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::removeJobType
 * @see app/Http/Controllers/Owner/SetupController.php:114
 * @route '/owner/setup/job-types/{jobType}'
 */
removeJobType.delete = (args: { jobType: number | { id: number } } | [jobType: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: removeJobType.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::addTechnician
 * @see app/Http/Controllers/Owner/SetupController.php:122
 * @route '/owner/setup/technicians'
 */
export const addTechnician = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addTechnician.url(options),
    method: 'post',
})

addTechnician.definition = {
    methods: ["post"],
    url: '/owner/setup/technicians',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::addTechnician
 * @see app/Http/Controllers/Owner/SetupController.php:122
 * @route '/owner/setup/technicians'
 */
addTechnician.url = (options?: RouteQueryOptions) => {
    return addTechnician.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::addTechnician
 * @see app/Http/Controllers/Owner/SetupController.php:122
 * @route '/owner/setup/technicians'
 */
addTechnician.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: addTechnician.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\SetupController::complete
 * @see app/Http/Controllers/Owner/SetupController.php:144
 * @route '/owner/setup/complete'
 */
export const complete = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})

complete.definition = {
    methods: ["post"],
    url: '/owner/setup/complete',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::complete
 * @see app/Http/Controllers/Owner/SetupController.php:144
 * @route '/owner/setup/complete'
 */
complete.url = (options?: RouteQueryOptions) => {
    return complete.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::complete
 * @see app/Http/Controllers/Owner/SetupController.php:144
 * @route '/owner/setup/complete'
 */
complete.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: complete.url(options),
    method: 'post',
})
const SetupController = { show, saveCompany, addJobType, removeJobType, addTechnician, complete }

export default SetupController