import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\CustomerController::importForm
 * @see app/Http/Controllers/Owner/CustomerController.php:118
 * @route '/owner/customers/import'
 */
export const importForm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: importForm.url(options),
    method: 'get',
})

importForm.definition = {
    methods: ["get","head"],
    url: '/owner/customers/import',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::importForm
 * @see app/Http/Controllers/Owner/CustomerController.php:118
 * @route '/owner/customers/import'
 */
importForm.url = (options?: RouteQueryOptions) => {
    return importForm.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::importForm
 * @see app/Http/Controllers/Owner/CustomerController.php:118
 * @route '/owner/customers/import'
 */
importForm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: importForm.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CustomerController::importForm
 * @see app/Http/Controllers/Owner/CustomerController.php:118
 * @route '/owner/customers/import'
 */
importForm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: importForm.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::importMethod
 * @see app/Http/Controllers/Owner/CustomerController.php:123
 * @route '/owner/customers/import'
 */
export const importMethod = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

importMethod.definition = {
    methods: ["post"],
    url: '/owner/customers/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::importMethod
 * @see app/Http/Controllers/Owner/CustomerController.php:123
 * @route '/owner/customers/import'
 */
importMethod.url = (options?: RouteQueryOptions) => {
    return importMethod.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::importMethod
 * @see app/Http/Controllers/Owner/CustomerController.php:123
 * @route '/owner/customers/import'
 */
importMethod.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: importMethod.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::quickCreate
 * @see app/Http/Controllers/Owner/CustomerController.php:103
 * @route '/owner/customers/quick-create'
 */
export const quickCreate = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quickCreate.url(options),
    method: 'post',
})

quickCreate.definition = {
    methods: ["post"],
    url: '/owner/customers/quick-create',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::quickCreate
 * @see app/Http/Controllers/Owner/CustomerController.php:103
 * @route '/owner/customers/quick-create'
 */
quickCreate.url = (options?: RouteQueryOptions) => {
    return quickCreate.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::quickCreate
 * @see app/Http/Controllers/Owner/CustomerController.php:103
 * @route '/owner/customers/quick-create'
 */
quickCreate.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: quickCreate.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::index
 * @see app/Http/Controllers/Owner/CustomerController.php:17
 * @route '/owner/customers'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/customers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::index
 * @see app/Http/Controllers/Owner/CustomerController.php:17
 * @route '/owner/customers'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::index
 * @see app/Http/Controllers/Owner/CustomerController.php:17
 * @route '/owner/customers'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CustomerController::index
 * @see app/Http/Controllers/Owner/CustomerController.php:17
 * @route '/owner/customers'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::create
 * @see app/Http/Controllers/Owner/CustomerController.php:58
 * @route '/owner/customers/create'
 */
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/owner/customers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::create
 * @see app/Http/Controllers/Owner/CustomerController.php:58
 * @route '/owner/customers/create'
 */
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::create
 * @see app/Http/Controllers/Owner/CustomerController.php:58
 * @route '/owner/customers/create'
 */
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CustomerController::create
 * @see app/Http/Controllers/Owner/CustomerController.php:58
 * @route '/owner/customers/create'
 */
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::store
 * @see app/Http/Controllers/Owner/CustomerController.php:63
 * @route '/owner/customers'
 */
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/owner/customers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::store
 * @see app/Http/Controllers/Owner/CustomerController.php:63
 * @route '/owner/customers'
 */
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::store
 * @see app/Http/Controllers/Owner/CustomerController.php:63
 * @route '/owner/customers'
 */
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::show
 * @see app/Http/Controllers/Owner/CustomerController.php:41
 * @route '/owner/customers/{customer}'
 */
export const show = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/owner/customers/{customer}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::show
 * @see app/Http/Controllers/Owner/CustomerController.php:41
 * @route '/owner/customers/{customer}'
 */
show.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return show.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::show
 * @see app/Http/Controllers/Owner/CustomerController.php:41
 * @route '/owner/customers/{customer}'
 */
show.get = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CustomerController::show
 * @see app/Http/Controllers/Owner/CustomerController.php:41
 * @route '/owner/customers/{customer}'
 */
show.head = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::edit
 * @see app/Http/Controllers/Owner/CustomerController.php:74
 * @route '/owner/customers/{customer}/edit'
 */
export const edit = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/owner/customers/{customer}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::edit
 * @see app/Http/Controllers/Owner/CustomerController.php:74
 * @route '/owner/customers/{customer}/edit'
 */
edit.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return edit.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::edit
 * @see app/Http/Controllers/Owner/CustomerController.php:74
 * @route '/owner/customers/{customer}/edit'
 */
edit.get = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CustomerController::edit
 * @see app/Http/Controllers/Owner/CustomerController.php:74
 * @route '/owner/customers/{customer}/edit'
 */
edit.head = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::update
 * @see app/Http/Controllers/Owner/CustomerController.php:83
 * @route '/owner/customers/{customer}'
 */
export const update = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/owner/customers/{customer}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::update
 * @see app/Http/Controllers/Owner/CustomerController.php:83
 * @route '/owner/customers/{customer}'
 */
update.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return update.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::update
 * @see app/Http/Controllers/Owner/CustomerController.php:83
 * @route '/owner/customers/{customer}'
 */
update.put = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})
/**
* @see \App\Http\Controllers\Owner\CustomerController::update
 * @see app/Http/Controllers/Owner/CustomerController.php:83
 * @route '/owner/customers/{customer}'
 */
update.patch = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Owner\CustomerController::destroy
 * @see app/Http/Controllers/Owner/CustomerController.php:93
 * @route '/owner/customers/{customer}'
 */
export const destroy = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/customers/{customer}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\CustomerController::destroy
 * @see app/Http/Controllers/Owner/CustomerController.php:93
 * @route '/owner/customers/{customer}'
 */
destroy.url = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { customer: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { customer: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    customer: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        customer: typeof args.customer === 'object'
                ? args.customer.id
                : args.customer,
                }

    return destroy.definition.url
            .replace('{customer}', parsedArgs.customer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CustomerController::destroy
 * @see app/Http/Controllers/Owner/CustomerController.php:93
 * @route '/owner/customers/{customer}'
 */
destroy.delete = (args: { customer: number | { id: number } } | [customer: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})
const CustomerController = { importForm, importMethod, quickCreate, index, create, store, show, edit, update, destroy, import: importMethod }

export default CustomerController