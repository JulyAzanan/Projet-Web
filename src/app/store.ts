import { createStore } from 'vuex'

export default createStore({
  state: {
    loggedIn: !!localStorage.getItem("loggedIn"),
    user: localStorage.getItem("user") ?? "",
  },
  mutations: {
    signIn(state, [user, password]: [string, string]) {
      state.loggedIn = false;
      state.user = user;
      localStorage.removeItem("loggedIn");
      localStorage.setItem("user", user);
      localStorage.setItem("password", password)
    },
    login(state, [user, password]: [string, string]) {
      state.loggedIn = true;
      state.user = user;
      localStorage.setItem("loggedIn", "success")
      localStorage.setItem("user", user);
      localStorage.setItem("password", password)
    },
    logout(state) {
      state.loggedIn = false;
      state.user = "";
      localStorage.clear();
    }
  },
})
