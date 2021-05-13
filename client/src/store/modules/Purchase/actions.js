export const all = ({ commit }) => {
    let response = "This where is api action go"
    commit("all", response)
}

export const one = ({ commit }, purchaseId) => {
    let response = "This where is api action go"
    console.log(purchaseId);
    commit("one", response)
}

export const create = ({ commit }, purchase) => {
    let response = "This where is api action go"
    console.log(purchase);
    commit("create", response)
}

export const update = ({ commit }, purchase) => {
    let response = "This where is api action go"
    console.log(purchase);
    commit("update", response)
}

export const moveToTrash = ({ commit }, purchaseId) => {
    let response = "This where is api action go"
    console.log(purchaseId);
    commit("moveToTrash", response)
}

export const remove = ({ commit }, purchaseId) => {
    let response = "This where is api action go"
    console.log(purchaseId);
    commit("remove", response)
}