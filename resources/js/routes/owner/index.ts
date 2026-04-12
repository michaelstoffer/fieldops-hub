import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
import setup90f0be from './setup'
import customers from './customers'
import properties from './properties'
import jobs from './jobs'
import calendarFa95d0 from './calendar'
import estimates from './estimates'
import dispatchF56169 from './dispatch'
import reports from './reports'
import settings from './settings'
import invoices from './invoices'
/**
* @see \App\Http\Controllers\Owner\SetupController::setup
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
export const setup = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setup.url(options),
    method: 'get',
})

setup.definition = {
    methods: ["get","head"],
    url: '/owner/setup',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\SetupController::setup
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
setup.url = (options?: RouteQueryOptions) => {
    return setup.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\SetupController::setup
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
setup.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setup.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\SetupController::setup
 * @see app/Http/Controllers/Owner/SetupController.php:41
 * @route '/owner/setup'
 */
setup.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setup.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CalendarController::calendar
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
export const calendar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: calendar.url(options),
    method: 'get',
})

calendar.definition = {
    methods: ["get","head"],
    url: '/owner/calendar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CalendarController::calendar
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
calendar.url = (options?: RouteQueryOptions) => {
    return calendar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CalendarController::calendar
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
calendar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: calendar.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CalendarController::calendar
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
calendar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: calendar.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\DispatchController::dispatch
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
export const dispatch = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dispatch.url(options),
    method: 'get',
})

dispatch.definition = {
    methods: ["get","head"],
    url: '/owner/dispatch',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\DispatchController::dispatch
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
dispatch.url = (options?: RouteQueryOptions) => {
    return dispatch.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\DispatchController::dispatch
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
dispatch.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dispatch.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\DispatchController::dispatch
 * @see app/Http/Controllers/Owner/DispatchController.php:17
 * @route '/owner/dispatch'
 */
dispatch.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dispatch.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\BillingController::billing
 * @see app/Http/Controllers/Owner/BillingController.php:14
 * @route '/owner/billing'
 */
export const billing = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: billing.url(options),
    method: 'get',
})

billing.definition = {
    methods: ["get","head"],
    url: '/owner/billing',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\BillingController::billing
 * @see app/Http/Controllers/Owner/BillingController.php:14
 * @route '/owner/billing'
 */
billing.url = (options?: RouteQueryOptions) => {
    return billing.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\BillingController::billing
 * @see app/Http/Controllers/Owner/BillingController.php:14
 * @route '/owner/billing'
 */
billing.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: billing.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\BillingController::billing
 * @see app/Http/Controllers/Owner/BillingController.php:14
 * @route '/owner/billing'
 */
billing.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: billing.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\ReportingController::dashboard
 * @see app/Http/Controllers/Owner/ReportingController.php:20
 * @route '/owner/dashboard'
 */
export const dashboard = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})

dashboard.definition = {
    methods: ["get","head"],
    url: '/owner/dashboard',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\ReportingController::dashboard
 * @see app/Http/Controllers/Owner/ReportingController.php:20
 * @route '/owner/dashboard'
 */
dashboard.url = (options?: RouteQueryOptions) => {
    return dashboard.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\ReportingController::dashboard
 * @see app/Http/Controllers/Owner/ReportingController.php:20
 * @route '/owner/dashboard'
 */
dashboard.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dashboard.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\ReportingController::dashboard
 * @see app/Http/Controllers/Owner/ReportingController.php:20
 * @route '/owner/dashboard'
 */
dashboard.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dashboard.url(options),
    method: 'head',
})
const owner = {
    setup: Object.assign(setup, setup90f0be),
customers: Object.assign(customers, customers),
properties: Object.assign(properties, properties),
jobs: Object.assign(jobs, jobs),
calendar: Object.assign(calendar, calendarFa95d0),
estimates: Object.assign(estimates, estimates),
dispatch: Object.assign(dispatch, dispatchF56169),
billing: Object.assign(billing, billing),
dashboard: Object.assign(dashboard, dashboard),
reports: Object.assign(reports, reports),
settings: Object.assign(settings, settings),
invoices: Object.assign(invoices, invoices),
}

export default owner