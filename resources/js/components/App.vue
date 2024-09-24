<script setup>
    import { onMounted, ref } from 'vue';
    
    const currentPage = window.location.pathname.replace('/', '');

    let user = ref([]);

    let page = ref({
        'activeLink': currentPage,
        'logout': false
    });

    onMounted(async () => {
        if (page.activeLink === '') {
            
        } else {
            
        }
    })

    const logoImage = () => {
        return './img/logo.png';
    }

    const logout = async () => {
        page.value.logout = true;

        await axios.post('/api/session/destroy').then((response) => {
            window.location.href = response.data.redirect;
        })
    }
</script>




<template>
    <div class="wrapper" v-if="page.activeLink === ''">
        <router-view />
    </div>

    
    <div class="rty" v-else>
        <div class="topbar">
            <div class="logo">
                <img :src="logoImage()" alt="logo">
                <h3>Identity Account Manager</h3>
            </div>

            
            <div class="user-info">
                <p>Jane Doe</p>
                <button type="button" title="Notifications">
                    <i class="fa fa-bell"></i>
                </button>
                <button type="button" title="Logout" @click="logout">
                    <i v-bind:class="(page.logout) ? 'fa fa-spinner fa-spin' : 'fa fa-power-off'"></i>
                </button>
            </div>
        </div>


        <main>
            <div class="sidebar">
                <nav>
                    <li>
                        <router-link to="/users" exact-active-class="active">
                            <i class="fa fa-users"></i> 
                            <span>Users</span>
                        </router-link>
                    </li>
                        
                    <li>
                        <router-link to="/administrators" exact-active-class="active">
                            <i class="fa fa-user-secret"></i> 
                            <span>Administrators</span>
                        </router-link>
                    </li>
                    
                    <li>
                        <router-link to="/logs" exact-active-class="active">
                            <i class="fa fa-file-text"></i> 
                            <span>Logs</span>
                        </router-link>
                    </li>
                </nav>
            </div>

            <div class="content">
                <router-view />
            </div>
        </main>
    </div>
</template>