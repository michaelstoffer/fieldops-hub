import SubscriptionController from './SubscriptionController'
import SetupController from './SetupController'
import TeamController from './TeamController'
import CustomerController from './CustomerController'
import PropertyController from './PropertyController'
import JobController from './JobController'
import CalendarController from './CalendarController'
import EstimateController from './EstimateController'
import DispatchController from './DispatchController'
import BillingController from './BillingController'
import ReportingController from './ReportingController'
import SettingsController from './SettingsController'
import JobTypeController from './JobTypeController'
import ItemController from './ItemController'
import InvoiceController from './InvoiceController'
import StripeController from './StripeController'
const Owner = {
    SubscriptionController: Object.assign(SubscriptionController, SubscriptionController),
SetupController: Object.assign(SetupController, SetupController),
TeamController: Object.assign(TeamController, TeamController),
CustomerController: Object.assign(CustomerController, CustomerController),
PropertyController: Object.assign(PropertyController, PropertyController),
JobController: Object.assign(JobController, JobController),
CalendarController: Object.assign(CalendarController, CalendarController),
EstimateController: Object.assign(EstimateController, EstimateController),
DispatchController: Object.assign(DispatchController, DispatchController),
BillingController: Object.assign(BillingController, BillingController),
ReportingController: Object.assign(ReportingController, ReportingController),
SettingsController: Object.assign(SettingsController, SettingsController),
JobTypeController: Object.assign(JobTypeController, JobTypeController),
ItemController: Object.assign(ItemController, ItemController),
InvoiceController: Object.assign(InvoiceController, InvoiceController),
StripeController: Object.assign(StripeController, StripeController),
}

export default Owner