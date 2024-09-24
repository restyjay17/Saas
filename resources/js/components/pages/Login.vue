<script setup>
    import { ref } from 'vue';

    let page = ref({
        'login': false,
        'error': false
    });

    let form = ref({
        'uname': '',
        'pword': '',
        'errorMsg': '',
        'showErrorMsg': false
    });

    const logoImage = () => {
        return './img/logo.png';
    }

    const login = async () => {
        if (form.value.uname === '' || form.value.pword === '') {
            page.value.error = true;
            return;
        } else {
            page.value.error = false;
        }

        page.value.login = true;

        const formData = new FormData();
        formData.append('uname', form.value.uname);
        formData.append('pword', form.value.pword);

        await axios.post('/api/authenticate', formData)
            .then((response) => {
                if (!response.data.status) form.value.showErrorMsg = true;
                else window.location.href = '/administrators';

                setTimeout(() => {
                    form.value.showErrorMsg = false;
                    page.value.login = false;
                }, 2000);
            }).catch((error) => {
                form.value.errorMsg = error.response.data.status;
                form.value.showErrorMsg = true;

                setTimeout(() => {
                    form.value.showErrorMsg = false;
                    page.value.login = false;
                }, 2000);
            });
    }
</script>




<template>
    <div class="login">
        <div class="container">
            <form>

                <div v-if="form.showErrorMsg" class="error show">
                    <p class="message">
                        <i class="fa fa-exclamation-triangle"></i>
                        <span>{{ form.errorMsg }}</span>
                    </p>
                </div>

                <div class="logo">
                    <img :src="logoImage()" alt="logo">
                </div>

                <div class="input-container">
                    <div class="form-group">
                        <label for="uname">Username</label>
                        <input type="text" id="uname" autocomplete="off" required v-bind:class="(page.error && form.uname === '') ? 'required' : ''" v-model="form.uname" v-bind:disabled="page.login">
                    </div>
                    
                    <div class="form-group">
                        <label for="pword">Password</label>
                        <input type="password" id="pword" autocomplete="off" required v-bind:class="(page.error && form.pword === '') ? 'required' : ''" v-model="form.pword" v-bind:disabled="page.login">
                    </div>

                    <div class="button-group">
                        <button @click="login($event)" v-bind:disabled="page.login">
                            <span v-if="page.login">VERIFYING <i class="fa fa-spinner fa-spin"></i></span>
                            <span v-else>LOGIN</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>