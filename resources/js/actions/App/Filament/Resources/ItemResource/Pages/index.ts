import ListItems from './ListItems'
import CreateItem from './CreateItem'
import EditItem from './EditItem'
const Pages = {
    ListItems: Object.assign(ListItems, ListItems),
CreateItem: Object.assign(CreateItem, CreateItem),
EditItem: Object.assign(EditItem, EditItem),
}

export default Pages