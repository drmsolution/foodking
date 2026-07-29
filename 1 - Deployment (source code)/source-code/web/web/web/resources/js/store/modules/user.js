import axios from 'axios'
import appService from "../../services/appService";
import createCrudModule from '../moduleFactory';

export const user = createCrudModule({
    url: 'admin/users',
    state: {
        addressLists: [],
        addressPage: {},
        addressPagination: [],
    },
    getters: {
        addressLists: function (state) {
            return state.addressLists;
        },
        addressPagination: function (state) {
            return state.addressPagination;
        },
        addressPage: function (state) {
            return state.addressPage;
        },
    },
    actions: {
        save: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = '/admin/users';
                if (context.state.temp.isEditing) {
                    method = axios.post;
                    url = `/admin/users/${context.state.temp.temp_id}`;
                }
                method(url, payload.form).then((res) => {
                    context.dispatch('lists', payload.search).catch(() => {});
                    context.commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        addressLists: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/users/address/${payload.id}`;
                if (payload) {
                    url = url + appService.requestHandler(payload.search);
                }
                axios.get(url).then((res) => {
                    if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                        context.commit("addressLists", res.data.data);
                        context.commit("addressPage", res.data.meta);
                        context.commit("addressPagination", res.data);
                    }
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        saveAddress: function (context, payload) {
            return new Promise((resolve, reject) => {
                let method = axios.post;
                let url = `/admin/users/address/${payload.id}`;
                if (context.state.temp.isEditing) {
                    method = axios.put;
                    url = `/admin/users/address/${payload.id}/${context.state.temp.temp_id}`;
                }
                method(url, payload.form).then(res => {
                    context.dispatch('addressLists', { id: payload.id, search: payload.search }).catch(() => {});
                    context.commit('reset');
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        editAddress: function (context, payload) {
            context.commit('temp', payload);
        },
    },
    mutations: {
        addressLists: function (state, payload) {
            state.addressLists = payload;
        },
        addressPagination: function (state, payload) {
            state.addressPagination = payload;
        },
        addressPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.addressPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
    },
    excludeActions: ['edit', 'destroy', 'show'],
    excludeState: ['show'],
    excludeGetters: ['show'],
    excludeMutations: ['show'],
});
