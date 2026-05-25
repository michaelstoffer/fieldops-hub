import Auth from './Auth'
import LegalController from './LegalController'
import Owner from './Owner'
import PublicEstimateController from './PublicEstimateController'
import Technician from './Technician'
import HealthController from './HealthController'
import StripeWebhookController from './StripeWebhookController'
import Settings from './Settings'
const Controllers = {
    Auth: Object.assign(Auth, Auth),
LegalController: Object.assign(LegalController, LegalController),
Owner: Object.assign(Owner, Owner),
PublicEstimateController: Object.assign(PublicEstimateController, PublicEstimateController),
Technician: Object.assign(Technician, Technician),
HealthController: Object.assign(HealthController, HealthController),
StripeWebhookController: Object.assign(StripeWebhookController, StripeWebhookController),
Settings: Object.assign(Settings, Settings),
}

export default Controllers