import axios from "axios";
import appService from "../../services/appService";
import createCrudModule from '../moduleFactory';

export const deliveryBoy = createCrudModule({
    url: 'admin/delivery-boy',
    hasExport: true,
    state: {
        myOrders: [],
        orderPage: {},
        orderPagination: [],
    },
    getters: {
        myOrders: function (state) {
            return state.myOrders;
        },
        orderPagination: function (state) {
            return state.orderPagination;
        },
        orderPage: function (state) {
            return state.orderPage;
        },
    },
    actions: {
        changePassword: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/delivery-boy/change-password/${payload.id}`, payload.form).then((res) => {
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        changeImage: function (context, payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/delivery-boy/change-image/${payload.id}`, payload.form, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                ).then((res) => {
                    context.commit("show", res.data.data);
                    resolve(res);
                }).catch((err) => {
                    reject(err);
                });
            });
        },
        myOrders: function (context, payload) {
            return new Promise((resolve, reject) => {
                let url = `admin/delivery-boy/my-order/${payload.id}`;
                if (payload.search) {
                    url = url + appService.requestHandler(payload.search);
                }
                axios.get(url).then((res) => {
                        if (typeof payload.vuex === "undefined" || payload.vuex === true) {
                            context.commit("myOrders", res.data.data);
                            context.commit("orderPage", res.data.meta);
                            context.commit("orderPagination", res.data);
                        }
                        resolve(res);
                    })
                    .catch((err) => {
                        reject(err);
                    });
            });
        },
    },
    mutations: {
        myOrders: function (state, payload) {
            state.myOrders = payload;
        },
        orderPagination: function (state, payload) {
            state.orderPagination = payload;
        },
        orderPage: function (state, payload) {
            if (typeof payload !== "undefined" && payload !== null) {
                state.orderPage = {
                    from: payload.from,
                    to: payload.to,
                    total: payload.total,
                };
            }
        },
    },
});
