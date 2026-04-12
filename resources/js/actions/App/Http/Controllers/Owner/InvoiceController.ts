import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\InvoiceController::index
 * @see app/Http/Controllers/Owner/InvoiceController.php:19
 * @route '/owner/invoices'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/invoices',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::index
 * @see app/Http/Controllers/Owner/InvoiceController.php:19
 * @route '/owner/invoices'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::index
 * @see app/Http/Controllers/Owner/InvoiceController.php:19
 * @route '/owner/invoices'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\InvoiceController::index
 * @see app/Http/Controllers/Owner/InvoiceController.php:19
 * @route '/owner/invoices'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\InvoiceController::show
 * @see app/Http/Controllers/Owner/InvoiceController.php:48
 * @route '/owner/invoices/{invoice}'
 */
export const show = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/owner/invoices/{invoice}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::show
 * @see app/Http/Controllers/Owner/InvoiceController.php:48
 * @route '/owner/invoices/{invoice}'
 */
show.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return show.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::show
 * @see app/Http/Controllers/Owner/InvoiceController.php:48
 * @route '/owner/invoices/{invoice}'
 */
show.get = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\InvoiceController::show
 * @see app/Http/Controllers/Owner/InvoiceController.php:48
 * @route '/owner/invoices/{invoice}'
 */
show.head = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\InvoiceController::destroy
 * @see app/Http/Controllers/Owner/InvoiceController.php:181
 * @route '/owner/invoices/{invoice}'
 */
export const destroy = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/owner/invoices/{invoice}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::destroy
 * @see app/Http/Controllers/Owner/InvoiceController.php:181
 * @route '/owner/invoices/{invoice}'
 */
destroy.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return destroy.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::destroy
 * @see app/Http/Controllers/Owner/InvoiceController.php:181
 * @route '/owner/invoices/{invoice}'
 */
destroy.delete = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

/**
* @see \App\Http\Controllers\Owner\InvoiceController::generateFromJob
 * @see app/Http/Controllers/Owner/InvoiceController.php:62
 * @route '/owner/jobs/{job}/invoice'
 */
export const generateFromJob = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateFromJob.url(args, options),
    method: 'post',
})

generateFromJob.definition = {
    methods: ["post"],
    url: '/owner/jobs/{job}/invoice',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::generateFromJob
 * @see app/Http/Controllers/Owner/InvoiceController.php:62
 * @route '/owner/jobs/{job}/invoice'
 */
generateFromJob.url = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { job: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { job: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    job: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        job: typeof args.job === 'object'
                ? args.job.id
                : args.job,
                }

    return generateFromJob.definition.url
            .replace('{job}', parsedArgs.job.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::generateFromJob
 * @see app/Http/Controllers/Owner/InvoiceController.php:62
 * @route '/owner/jobs/{job}/invoice'
 */
generateFromJob.post = (args: { job: number | { id: number } } | [job: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: generateFromJob.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\InvoiceController::send
 * @see app/Http/Controllers/Owner/InvoiceController.php:103
 * @route '/owner/invoices/{invoice}/send'
 */
export const send = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(args, options),
    method: 'post',
})

send.definition = {
    methods: ["post"],
    url: '/owner/invoices/{invoice}/send',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::send
 * @see app/Http/Controllers/Owner/InvoiceController.php:103
 * @route '/owner/invoices/{invoice}/send'
 */
send.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return send.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::send
 * @see app/Http/Controllers/Owner/InvoiceController.php:103
 * @route '/owner/invoices/{invoice}/send'
 */
send.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: send.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\InvoiceController::voidMethod
 * @see app/Http/Controllers/Owner/InvoiceController.php:121
 * @route '/owner/invoices/{invoice}/void'
 */
export const voidMethod = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

voidMethod.definition = {
    methods: ["post"],
    url: '/owner/invoices/{invoice}/void',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::voidMethod
 * @see app/Http/Controllers/Owner/InvoiceController.php:121
 * @route '/owner/invoices/{invoice}/void'
 */
voidMethod.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return voidMethod.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::voidMethod
 * @see app/Http/Controllers/Owner/InvoiceController.php:121
 * @route '/owner/invoices/{invoice}/void'
 */
voidMethod.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: voidMethod.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Owner\InvoiceController::recordPayment
 * @see app/Http/Controllers/Owner/InvoiceController.php:135
 * @route '/owner/invoices/{invoice}/payments'
 */
export const recordPayment = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordPayment.url(args, options),
    method: 'post',
})

recordPayment.definition = {
    methods: ["post"],
    url: '/owner/invoices/{invoice}/payments',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Owner\InvoiceController::recordPayment
 * @see app/Http/Controllers/Owner/InvoiceController.php:135
 * @route '/owner/invoices/{invoice}/payments'
 */
recordPayment.url = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { invoice: args }
    }

            if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
            args = { invoice: args.id }
        }
    
    if (Array.isArray(args)) {
        args = {
                    invoice: args[0],
                }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
                        invoice: typeof args.invoice === 'object'
                ? args.invoice.id
                : args.invoice,
                }

    return recordPayment.definition.url
            .replace('{invoice}', parsedArgs.invoice.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\InvoiceController::recordPayment
 * @see app/Http/Controllers/Owner/InvoiceController.php:135
 * @route '/owner/invoices/{invoice}/payments'
 */
recordPayment.post = (args: { invoice: number | { id: number } } | [invoice: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: recordPayment.url(args, options),
    method: 'post',
})
const InvoiceController = { index, show, destroy, generateFromJob, send, voidMethod, recordPayment, void: voidMethod }

export default InvoiceController