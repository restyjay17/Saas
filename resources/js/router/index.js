import { createRouter, createWebHistory } from "vue-router";

const routes = [
    {
        path: '/',
        component: () => import('../components/pages/Login.vue')
    },
    {
        path: '/users',
        component: () => import('../components/pages/Users.vue'),
		beforeEnter: (to, from, next) => {
			axios.get('/api/authenticated')
				.then(() => {
					next();
				}).catch(() => {
					window.location.href = '/';
				})
		}
    },
    {
        path: '/administrators',
        component: () => import('../components/pages/Administrators.vue'),
		beforeEnter: (to, from, next) => {
			axios.get('/api/authenticated')
				.then(() => {
					next();
				}).catch(() => {
					window.location.href = '/';
				})
		}
    },
    {
        path: '/logs',
        component: () => import('../components/pages/Logs.vue'),
		beforeEnter: (to, from, next) => {
			axios.get('/api/authenticated')
				.then(() => {
					next();
				}).catch(() => {
					window.location.href = '/';
				})
		}
    },
	{
		path: '/:pathMatch(.*)*',
		component: () => import('../components/NotFound.vue')
	}
];

const router = createRouter({
    history: createWebHistory(process.env.BASE_URL),
    routes,
    linkExactActiveClass: 'active'
});

export default router;