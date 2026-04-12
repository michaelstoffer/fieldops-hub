import CustomerResource from './CustomerResource'
import ItemResource from './ItemResource'
import JobResource from './JobResource'
import JobTypeResource from './JobTypeResource'
import MessageTemplateResource from './MessageTemplateResource'
import PropertyResource from './PropertyResource'
const Resources = {
    CustomerResource: Object.assign(CustomerResource, CustomerResource),
ItemResource: Object.assign(ItemResource, ItemResource),
JobResource: Object.assign(JobResource, JobResource),
JobTypeResource: Object.assign(JobTypeResource, JobTypeResource),
MessageTemplateResource: Object.assign(MessageTemplateResource, MessageTemplateResource),
PropertyResource: Object.assign(PropertyResource, PropertyResource),
}

export default Resources