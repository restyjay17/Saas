<script setup>
    import { onMounted, ref } from 'vue';

    let users = ref([]);
    let logs = ref([]);

    let filter = ref({
        'search': '',
        'user': '',
        'startDate': '',
        'endDate': ''
    });

    let page = ref({
        'loading': false,
        'paginate': [],
        'totalData': '',
        'currentPage': ''
    });

    onMounted(async () => {
        getLogUsers();
        getLogs();
    });

    const getLogUsers = async () => {
        await axios.get(`/api/logs/users`).then((response) => {
            users.value = response.data.users;
        });
    }

    const getLogs = async (url = `/api/logs?page=1`) => {
        page.value.loading = true;

        await axios.get(url + `&search=${filter.value.search}&user=${filter.value.user}&startdate=${filter.value.startDate}&enddate=${filter.value.endDate}`).then((response) => {
            logs.value = response.data.logs.data;
            page.value.paginate = response.data.logs.links;
            page.value.totalData = response.data.logs.total;
            page.value.currentPage = response.data.logs.path + '?page=' + response.data.logs.current_page;

            page.value.loading = false;
        }).catch((error) => {
            logs.value = [];
            
            for (const [key, value] of Object.entries(page.value)) {
                if (typeof value == 'string') page.value[key] = '';
                else if (typeof value == 'boolean') page.value[key] = false;
                else if (typeof value == 'object') page.value[key] = [];
            }
        });
    }
</script>




<template>
    <div class="logs">
        <section class="table-panel">
            <h3 class="title">LOGS</h3>

            <div class="filter">
                <div class="form-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" autocomplete="off" v-model="filter.search" @input="getLogs()">
                </div>
                <div class="form-group">
                    <label for="user">User</label>
                    <select id="user" v-model="filter.user" @change="getLogs()">
                        <option value=""></option>
                        <option v-for="item in users" :key="item.id" v-if="users.length > 0" v-bind:value="item.logged_by">{{ item.logged_by }}</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sdate">Start Date</label>
                    <input type="date" id="sdate" autocomplete="off" v-model="filter.startDate" @input="getLogs()">
                </div>
                <div class="form-group">
                    <label for="edate">End Date</label>
                    <input type="date" id="edate" autocomplete="off" v-model="filter.endDate" @input="getLogs()">
                </div>
            </div>

            <table id="tblLogs" v-bind:class="(page.loading) ? 'loading' : ''">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Logged By</th>
                        <th>Logged At</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in logs" :key="item.id" v-if="!page.loading && logs.length > 0">
                        <td>
                            <span>{{ item.action }}</span>
                        </td>
                        <td>
                            <span>{{ item.logged_by }}</span>
                        </td>
                        <td>
                            <span>{{ item.logged_at }}</span>
                        </td>
                    </tr>
                    <tr class="no-data" v-else-if="!page.loading && logs.length === 0">
                        <td colspan="3">No data available.</td>
                    </tr>
                    <tr v-for="index in 10" :key="index " v-else>
                        <td><span></span></td>
                        <td><span></span></td>
                        <td><span></span></td>
                    </tr>
                </tbody>
            </table>

            <div class="pagination" v-if="page.totalData > 10">
                <button type="button" v-for="item in page.paginate" :key="item.id" v-if="page.paginate.length > 0" v-bind:disabled="(item.active || item.url === null) ? true : false" v-bind:class="(item.active) ? 'active' : ''" @click="getLogs(item.url)">
                    <span v-html="item.label"></span>
                </button>
            </div>
        </section>
    </div>
</template>