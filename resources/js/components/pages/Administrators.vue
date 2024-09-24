<script setup>
    import { onMounted, ref } from 'vue';
    import { resetArrayValues } from '../../functions';

    let administrators = ref([]);

    let page = ref({
        'loading': false,
        'paginate': [],
        'totalData': '',
        'currentPage': ''
    });

    let form = ref({
        'target': '',
        'uname': '',
        'pword': '',
        'name': '',
        'email': '',
        'status': 0,
        'showPassword': false,
        'submit': false
    });

    onMounted(async () => {
        getAdministrators();
    });

    const showPassword = () => {
        form.value.showPassword = !form.value.showPassword;
    }

    const resetForm = () => {
        resetArrayValues(form.value);
    }

    const getAdministrators = async (url = `/api/administrators?page=1`) => {
        page.value.loading = true;

        await axios.get(url).then((response) => {
            administrators.value = response.data.administrators.data;
            page.value.paginate = response.data.administrators.links;
            page.value.totalData = response.data.administrators.total;
            page.value.currentPage = response.data.administrators.path + '?page=' + response.data.administrators.current_page;

            page.value.loading = false;
        }).catch((error) => {
            administrators.value = [];
            resetArrayValues(page.value);
        });
    };

    const saveAdministrator = async (e) => {
        if (form.value.target === '' && form.value.pword === '') {
            toast.fire({
                icon: 'error',
                title: 'Password is required'
            });
            return;
        }

        if (!form.value.email.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
            toast.fire({
                icon: 'error',
                title: 'Invalid email address'
            });
            return;
        }


        form.value.submit = true;

        const formData = new FormData();
        for (const [key, value] of Object.entries(form.value)) {
            if (typeof value != 'boolean') formData.append(key, value);
        }

        await axios.post('/api/administrator', formData)
            .then((response) => {
                resetForm();

                form.value.submit = false;

                toast.fire({
                    icon: 'success',
                    title: response.data.message
                });

                getAdministrators();
            }).catch((error) => {
                toast.fire({
                    icon: 'error',
                    title: error.response.data.message
                });

                form.value.submit = false;
            });
    }

    const editAdministrator = async (e, id) => {
        if (e.target.nodeName === 'BUTTON') {
            e.target.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        } else {
            e.srcElement.className = 'fa fa-spinner fa-spin';
        }

        await axios.get(`/api/administrator/${id}`).then((response) => {
            form.value.target = response.data.details.id;
            form.value.uname = response.data.details.uname;
            form.value.name = response.data.details.name;
            form.value.email = response.data.details.email;
            form.value.status = response.data.details.status;

            if (e.target.nodeName === 'BUTTON') {
                e.target.innerHTML = '<i class="fa fa-edit"></i>';
            } else {
                e.srcElement.className = 'fa fa-edit';
            }
        });
    }

    const deleteAdministrator = (id, name) => {
        Swal.fire({
            text: `Are you sure you want to delete ${name} details?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
            showLoaderOnConfirm: true,
            preConfirm: function(result) {
                return new Promise(function(resolve) {
                    if (result) {
                        axios.delete(`/api/administrator/${id}`)
                            .then((response) => {
                                toast.fire({
                                    icon: 'success',
                                    title: response.data.message
                                });
                                
                                getAdministrators();

                                resolve();
                            }).catch((error) => {
                                toast.fire({
                                    icon: 'error',
                                    title: error.response.data.message
                                });
                            });
                    }
                })
            }
        });
    }
</script>




<template>
    <div class="admin">
        <section class="table-panel">
            <h3 class="title">Administrators</h3>

            <table id="tblAdministrators" v-bind:class="(page.loading) ? 'loading' : ''">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in administrators" :key="item.id" v-if="!page.loading && administrators.length > 0">
                        <td>
                            <span>{{ item.uname }}</span>
                        </td>
                        <td>
                            <span>{{ item.name }}</span>
                        </td>
                        <td>
                            <span>{{ item.email }}</span>
                        </td>
                        <td>
                            <span>
                                <span class="badge badge-success" v-if="item.status === 1">ACTIVE</span>
                                <span class="badge badge-danger" v-else>INACTIVE</span>
                            </span>
                        </td>
                        <td>
                            <span>
                                <button type="button" title="EDIT" @click="editAdministrator($event, item.adm_id)">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" title="DELETE" @click="deleteAdministrator(item.adm_id, item.name)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                    <tr class="no-data" v-else-if="!page.loading && administrators.length === 0">
                        <td colspan="5">No data available.</td>
                    </tr>
                    <tr v-for="index in 10" :key="index " v-else>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination" v-if="page.totalData > 10">
                <button type="button" v-for="item in page.paginate" :key="item.id" v-if="page.paginate.length > 0" v-bind:disabled="(item.active || item.url === null) ? true : false" v-bind:class="(item.active) ? 'active' : ''" @click="getAdministrators(item.url)">
                    <span v-html="item.label"></span>
                </button>
            </div>
        </section>


        <section class="form-panel">
            <form v-on:submit.prevent="saveAdministrator($event)">
                <div class="form-group">
                    <label for="uname">Username</label>
                    <input type="text" id="uname" v-model="form.uname" v-bind:disabled="form.submit" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="pword">Password</label>
                    <div class="input-button-group">
                        <input v-bind:type="(form.showPassword) ? 'text' : 'password'" id="pword" v-model="form.pword" v-bind:disabled="form.submit">
						<button type="button" @click="showPassword()">
							<i v-bind:class="(form.showPassword) ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
						</button>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" v-model="form.name" v-bind:disabled="form.submit" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" v-model="form.email" v-bind:disabled="form.submit" autocomplete="off" required>
                </div>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" v-model="form.status" v-bind:disabled="form.submit" required>
                        <option value=""></option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                </div>
                <div class="button-group">
                    <button type="submit" v-bind:disabled="form.submit">
						<span v-if="form.submit">SAVING <i class="fa fa-spinner fa-spin"></i></span>
						<span v-else>SAVE</span>
					</button>
					<button type="reset" v-bind:disabled="form.submit" @click="resetForm()">RESET</button>
                </div>
            </form>
        </section>
    </div>
</template>