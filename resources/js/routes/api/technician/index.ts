import jobs from './jobs'
import catalog from './catalog'
import location from './location'
const technician = {
    jobs: Object.assign(jobs, jobs),
catalog: Object.assign(catalog, catalog),
location: Object.assign(location, location),
}

export default technician