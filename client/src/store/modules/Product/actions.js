export const all = ({ commit }) => {
    let response = "This where is api action go"
    commit("all", response)
}

export const one = ({ commit }, productId) => {
    let response = "This where is api action go"
    console.log(productId);
    commit("one", response)
}

export const create = ({ commit }, product) => {
    let response = "This where is api action go"
    console.log(product);
    commit("create", response)
}

export const update = ({ commit }, product) => {
    let response = "This where is api action go"
    console.log(product);
    commit("update", response)
}

export const moveToTrash = ({ commit }, productId) => {
    let response = "This where is api action go"
    console.log(productId);
    commit("moveToTrash", response)
}

export const remove = ({ commit }, productId) => {
    let response = "This where is api action go"
    console.log(productId);
    commit("remove", response)
}