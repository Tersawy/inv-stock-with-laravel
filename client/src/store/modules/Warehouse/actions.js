export const all = ({ commit }) => {
    let response = "This where is api action go"
    commit("all", response)
}

export const one = ({ commit }, warehouseId) => {
    let response = "This where is api action go"
    console.log(warehouseId);
    commit("one", response)
}

export const create = ({ commit }, warehouse) => {
    let response = "This where is api action go"
    console.log(warehouse);
    commit("create", response)
}

export const update = ({ commit }, warehouse) => {
    let response = "This where is api action go"
    console.log(warehouse);
    commit("update", response)
}

export const moveToTrash = ({ commit }, warehouseId) => {
    let response = "This where is api action go"
    console.log(warehouseId);
    commit("moveToTrash", response)
}

export const remove = ({ commit }, warehouseId) => {
    let response = "This where is api action go"
    console.log(warehouseId);
    commit("remove", response)
}