export const all = ({ commit }) => {
    let response = "This where is api action go"
    commit("all", response)
}

export const one = ({ commit }, expenseId) => {
    let response = "This where is api action go"
    console.log(expenseId);
    commit("one", response)
}

export const create = ({ commit }, expense) => {
    let response = "This where is api action go"
    console.log(expense);
    commit("create", response)
}

export const update = ({ commit }, expense) => {
    let response = "This where is api action go"
    console.log(expense);
    commit("update", response)
}

export const moveToTrash = ({ commit }, expenseId) => {
    let response = "This where is api action go"
    console.log(expenseId);
    commit("moveToTrash", response)
}

export const remove = ({ commit }, expenseId) => {
    let response = "This where is api action go"
    console.log(expenseId);
    commit("remove", response)
}