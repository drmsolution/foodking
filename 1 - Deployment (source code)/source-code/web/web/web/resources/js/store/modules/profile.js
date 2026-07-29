import axios from "axios";
import createCrudModule from '../moduleFactory';

const actions = {
    changePassword: function (context, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`/admin/employee/change-password/${payload.id}`, payload.form).then((res) => {
                resolve(res);
            }).catch((err) => {
                reject(err);
            });
        });
    },
    changeImage: function (context, payload) {
        return new Promise((resolve, reject) => {
            axios.post(`/admin/employee/change-image/${payload.id}`, payload.form, {
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
};

export const employee = createCrudModule({
    url: 'admin/employee',
    hasExport: true,
    actions,
});
