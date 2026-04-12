import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Owner\CalendarController::index
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/owner/calendar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CalendarController::index
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CalendarController::index
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CalendarController::index
 * @see app/Http/Controllers/Owner/CalendarController.php:14
 * @route '/owner/calendar'
 */
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Owner\CalendarController::events
 * @see app/Http/Controllers/Owner/CalendarController.php:22
 * @route '/owner/calendar/events'
 */
export const events = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})

events.definition = {
    methods: ["get","head"],
    url: '/owner/calendar/events',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Owner\CalendarController::events
 * @see app/Http/Controllers/Owner/CalendarController.php:22
 * @route '/owner/calendar/events'
 */
events.url = (options?: RouteQueryOptions) => {
    return events.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Owner\CalendarController::events
 * @see app/Http/Controllers/Owner/CalendarController.php:22
 * @route '/owner/calendar/events'
 */
events.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: events.url(options),
    method: 'get',
})
/**
* @see \App\Http\Controllers\Owner\CalendarController::events
 * @see app/Http/Controllers/Owner/CalendarController.php:22
 * @route '/owner/calendar/events'
 */
events.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: events.url(options),
    method: 'head',
})
const CalendarController = { index, events }

export default CalendarController