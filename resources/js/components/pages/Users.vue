<script setup>
    import { ref, onMounted } from 'vue';
    import { resetArrayValues } from '../../functions.js';

    let users = ref([]);

    let form = ref({
        'target': '',
        'lname': '',
        'fname': '',
        'mname': '',
        'contactnumber': '',
        'email': '',
        'company': '',
        'companycontactnumber': '',
        'companyemail': '',
        'companyaddress': '',
        'status': 0,
        'submit': false
    });

    let filter = ref({
        'search': '',
        'status': ''
    });

    let page = ref({
        'loading': false,
        'paginate': [],
        'totalData': '',
        'currentPage': ''
    });

    onMounted(async () => {
        getUsers();
    });

    const resetForm = () => {
        resetArrayValues(form.value);
        form.value.contactnumber = '';
    }

    const acceptNumberOnly = (event) => {
        event = (event) ? event : window.event;
        var charCode = (event.which) ? event.which : event.keyCode;
        
        if (charCode > 31 && (charCode < 48 || charCode > 57)) event.preventDefault();
    }

    const getUsers = async (url = `/api/users?page=1`) => {
        page.value.loading = true;

        await axios.get(url + `&search=${filter.value.search}&status=${filter.value.status}`).then((response) => {
            users.value = response.data.users.data;
            page.value.paginate = response.data.users.links;
            page.value.totalData = response.data.users.total;
            page.value.currentPage = response.data.users.path + '?page=1' + response.data.users.current_page;

            page.value.loading = false;
        }).catch((error) => {
            users.value = [];
            resetArrayValues(page.value);
        });
    }

    const saveUser = async (e) => {
        if (!form.value.email.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
            toast.fire({
                icon: 'error',
                title: 'Invalid email address'
            });
            return;
        }
        
        if (form.value.companyemail !== '' && !form.value.companyemail.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
            toast.fire({
                icon: 'error',
                title: 'Invalid company email address'
            });
            return;
        }
        
        form.value.submit = true;

        const formData = new FormData();
        for (const [key, value] of Object.entries(form.value)) {
            if (typeof value != 'boolean') formData.append(key, value);
        }

        await axios.post('/api/user', formData)
            .then((response) => {
                resetForm();

                form.value.submit = false;

                toast.fire({
                    icon: 'success',
                    title: response.data.message
                });

                getUsers();
            }).catch((error) => {
                toast.fire({
                    icon: 'error',
                    title: error.response.data.message
                });

                form.value.submit = false;
            });
    }

    const editUser = async (e, id) => {
        if (e.target.nodeName === 'BUTTON') e.target.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
        else e.srcElement.className = 'fa fa-spinner fa-spin';

        await axios.get(`/api/user/${id}`).then((response) => {
            form.value.target = response.data.details.id;
            form.value.lname = response.data.details.lname;
            form.value.fname = response.data.details.fname;
            form.value.mname = response.data.details.mname;
            form.value.contactnumber = response.data.details.contact_number;
            form.value.email = response.data.details.email;
            form.value.company = response.data.details.company_name;
            form.value.companycontactnumber = response.data.details.company_contact_number;
            form.value.companyemail = response.data.details.company_email;
            form.value.companyaddress = response.data.details.company_address;
            form.value.status = response.data.details.status;

            if (e.target.nodeName === 'BUTTON') e.target.innerHTML = '<i class="fa fa-edit"></i>';
            else e.srcElement.className = 'fa fa-edit';
        });
    }
</script>




<template>
    <div class="users">
        <section class="table-panel">
            <h3 class="title">Users</h3>


            <div class="filter">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" autocomplete="off" v-model="filter.search" @input="getUsers()">
                </div>
                <div class="form-group">
                    <label for="searchstatus">Status</label>
                    <select id="searchstatus" v-model="filter.status" @change="getUsers()">
                        <option value=""></option>
                        <option value="0">for Verification</option>
                        <option value="1">Active</option>
                        <option value="2">Suspended</option>
                        <option value="3">Deactivate</option>
                    </select>
                </div>
            </div>


            <table id="tblUsers" v-bind:class="(page.loading) ? 'loading' : ''">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Contact Number</th>
                        <th>Email</th>
                        <th>Company</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in users" :key="item.id" v-if="!page.loading && users.length > 0">
                        <td>
                            <span>{{ item.fname + ' ' + item.lname }}</span>
                        </td>
                        <td>
                            <span>+639{{ item.contact_number }}</span>
                        </td>
                        <td>
                            <span>{{ item.email }}</span>
                        </td>
                        <td>
                            <span>{{ item.company_name }}</span>
                        </td>
                        <td>
                            <span>
                                <span class="badge badge-success" v-if="item.status === 1">ACTIVE</span>
                                <span class="badge badge-warning" v-else-if="item.status === 2">SUSPENDED</span>
                                <span class="badge badge-danger" v-else-if="item.status === 3">DEACTIVATED</span>
                                <span class="badge badge-primary" v-else>FOR VERIFICATION</span>
                            </span>
                        </td>
                        <td>
                            <span>
                                <button type="button" title="EDIT" @click="editUser($event, item.id)">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button type="button" title="DELETE">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </span>
                        </td>
                    </tr>
                    <tr class="no-data" v-else-if="!page.loading && users.length === 0">
                        <td colspan="6">No data available.</td>
                    </tr>
                    <tr v-for="index in 10" :key="index" v-else>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination" v-if="page.totalData > 1">
                <button type="button" v-for="item in page.paginate" :key="item.id" v-if="page.paginate.length > 0" v-bind:disabled="(item.active || item.url === null) ? true : false" v-bind:class="(item.active) ? 'active' : ''" @click="getUsers(item.url)">
                    <span v-html="item.label"></span>
                </button>
            </div>
        </section>


        <section class="form-panel">
            <form v-on:submit.prevent="saveUser($event)">
                <div class="form-group">
                    <label for="lname">Last Name</label>
                    <input type="text" id="lname" autocomplete="off" required v-model="form.lname" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="fname">First Name</label>
                    <input type="text" id="fname" autocomplete="off" required v-model="form.fname" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="mname">Middle Name</label>
                    <input type="text" id="mname" autocomplete="off" required v-model="form.mname" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="contactnumber">Contact Number</label>
                    <div class="input-number-group">
                        <span>+639</span>
                        <input type="text" id="contactnumber" autocomplete="off" :maxlength="9" :minlength="9" required @keypress="acceptNumberOnly($event)" v-model="form.contactnumber" v-bind:disabled="form.submit">
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" autocomplete="off" required v-model="form.email" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="company">Company Name</label>
                    <input type="text" id="company" autocomplete="off" required v-model="form.company" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="companycontact">Company Contact Number</label>
                    <input type="text" id="companycontact" autocomplete="off" required v-model="form.companycontactnumber" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="companyemail">Company Email Address</label>
                    <input type="email" id="companyemail" autocomplete="off" required v-model="form.companyemail" v-bind:disabled="form.submit">
                </div>
                <div class="form-group">
                    <label for="companyaddress">Company Address</label>
                    <textarea id="companyaddress" cols="5" rows="3" v-model="form.companyaddress" v-bind:disabled="form.submit"></textarea>
                </div>
                <div class="form-group" v-if="form.target !== ''">
                    <label for="status">Status</label>
                    <select id="status" required v-model="form.status" v-bind:disabled="form.submit">
                        <option value=""></option>
                        <option value="0">for Verification</option>
                        <option value="1">Active</option>
                        <option value="2">Suspended</option>
                        <option value="3">Deactivate</option>
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