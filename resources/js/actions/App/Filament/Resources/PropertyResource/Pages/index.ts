import ListProperties from './ListProperties'
import CreateProperty from './CreateProperty'
import EditProperty from './EditProperty'
const Pages = {
    ListProperties: Object.assign(ListProperties, ListProperties),
CreateProperty: Object.assign(CreateProperty, CreateProperty),
EditProperty: Object.assign(EditProperty, EditProperty),
}

export default Pages