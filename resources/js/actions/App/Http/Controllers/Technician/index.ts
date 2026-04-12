import DashboardController from './DashboardController'
import JobController from './JobController'
import LocationController from './LocationController'
const Technician = {
    DashboardController: Object.assign(DashboardController, DashboardController),
JobController: Object.assign(JobController, JobController),
LocationController: Object.assign(LocationController, LocationController),
}

export default Technician