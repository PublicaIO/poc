export default {
    state: {
        authUser: null
    },

    mutations: {
        setAuthUser(state, newAuthUser) {
            state.authUser = newAuthUser;
        }
    }
}
