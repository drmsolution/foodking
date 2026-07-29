import axios from 'axios'
import appService from "../services/appService";

function omit(obj, keys) {
    const result = {};
    for (const key of Object.keys(obj)) {
        if (!keys.includes(key)) {
            result[key] = obj[key];
        }
    }
    return result;
}

export default function createCrudModule(config) {
    const {
        url,
        state: customState = {},
        actions: customActions = {},
        mutations: customMutations = {},
        getters: customGetters = {},
        hasExport = false,
        excludeActions = [],
        excludeMutations = [],
        excludeGetters = [],
        excludeState = [],
    } = config;

    const state = {
        lists: [],
        page: {},
        pagination: [],
        show: {},
        temp: {
            temp_id: null,
            isEditing: false,
        },
        ...customState,
    };

    excludeState.forEach(function (key) {
        delete state[key];
    });

    const getters = omit({
        lists: function (state) {
            return state.lists;
        },
        pagination: function (state) {
            return state.pagination;
        },
        page: function (state) {
            return state.page;
        },
        show: function (state) {
            return state.show;
        },
        temp: function (state) {
            return state.temp;
        },
    }, excludeGetters);

    const mutations = omit({
        lists: function (state, payload) {
            state.lists = payload;
        },
        pagination: function (state, payload) {
            state.pagination = payload;
        },
        page: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.page = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
        show: function (state, payload) {
            state.show = payload;
        },
        temp: function (state, payload) {
            state.temp.temp_id = payload;
            state.temp.isEditing = true;
        },
        reset: function (state) {
            state.temp.temp_id = null;
            state.temp.isEditing = false;
        },
    }, excludeMutations);

    const standardActions = {
        lists: function ({ commit }, payload) {
            return new Promise((resolve, reject) => {
                let reqUrl = url;
                if (payload) {
                    reqUrl = reqUrl + appService.requestHandler(payload);
                }
                axios.get(reqUrl).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        commit('lists', res.data.data);
                        commit('page', res.data.meta);
                        commit('pagination', res.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        save: function ({ commit, dispatch, state }, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let reqUrl = `/${url}`;
                if (state.temp.isEditing) {
                    method = axios.put;
                    reqUrl = `/${url}/${state.temp.temp_id}`;
                }
                method(reqUrl, payload.form).then((res) => {
                    dispatch('lists', payload.search).catch(() => {});
                    commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        edit: function ({ commit }, payload) {
            commit('temp', payload);
        },
        destroy: function ({ commit, dispatch }, payload) {
            return new Promise((resolve, reject) => {
                axios.delete(`${url}/${payload.id}`).then((res) => {
                    dispatch('lists', payload.search).catch(() => {});
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        show: function ({ commit }, payload) {
            return new Promise((resolve, reject) => {
                axios.get(`${url}/show/${payload}`).then((res) => {
                    commit('show', res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        reset: function ({ commit }) {
            commit('reset');
        },
    };

    if (hasExport) {
        standardActions.export = function ({ commit }, payload) {
            return new Promise((resolve, reject) => {
                let exportUrl = `${url}/export`;
                if (payload) {
                    exportUrl = exportUrl + appService.requestHandler(payload);
                }
                axios.get(exportUrl, { responseType: 'blob' }).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        };
    }

    const actions = { ...omit(standardActions, excludeActions), ...customActions };

    return {
        namespaced: true,
        state,
        getters: { ...getters, ...customGetters },
        mutations: { ...mutations, ...customMutations },
        actions,
    };
}
