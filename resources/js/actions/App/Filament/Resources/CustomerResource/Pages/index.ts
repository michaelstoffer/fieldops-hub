import ListCustomers from './ListCustomers'
import CreateCustomer from './CreateCustomer'
import EditCustomer from './EditCustomer'
const Pages = {
    ListCustomers: Object.assign(ListCustomers, ListCustomers),
CreateCustomer: Object.assign(CreateCustomer, CreateCustomer),
EditCustomer: Object.assign(EditCustomer, EditCustomer),
}

export default Pages