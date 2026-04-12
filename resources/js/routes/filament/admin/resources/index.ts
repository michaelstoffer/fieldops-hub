import customers from './customers'
import items from './items'
import jobs from './jobs'
import jobTypes from './job-types'
import messageTemplates from './message-templates'
import properties from './properties'
const resources = {
    customers: Object.assign(customers, customers),
items: Object.assign(items, items),
jobs: Object.assign(jobs, jobs),
jobTypes: Object.assign(jobTypes, jobTypes),
messageTemplates: Object.assign(messageTemplates, messageTemplates),
properties: Object.assign(properties, properties),
}

export default resources