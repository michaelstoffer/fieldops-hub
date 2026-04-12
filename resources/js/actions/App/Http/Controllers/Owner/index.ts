import SetupController from './SetupController'
import CustomerController from './CustomerController'
import PropertyController from './PropertyController'
import JobController from './JobController'
import CalendarController from './CalendarController'
import EstimateController from './EstimateController'
import DispatchController from './DispatchController'
import BillingController from './BillingController'
import ReportingController from './ReportingController'
import SettingsController from './SettingsController'
import InvoiceController from './InvoiceController'
import StripeController from './StripeController'
const Owner = {
    SetupController: Object.assign(SetupController, SetupController),
CustomerController: Object.assign(CustomerController, CustomerController),
PropertyController: Object.assign(PropertyController, PropertyController),
JobController: Object.assign(JobController, JobController),
CalendarController: Object.assign(CalendarController, CalendarController),
EstimateController: Object.assign(EstimateController, EstimateController),
DispatchController: Object.assign(DispatchController, DispatchController),
BillingController: Object.assign(BillingController, BillingController),
ReportingController: Object.assign(ReportingController, ReportingController),
SettingsController: Object.assign(SettingsController, SettingsController),
InvoiceController: Object.assign(InvoiceController, InvoiceController),
StripeController: Object.assign(StripeController, StripeController),
}

export default Owner