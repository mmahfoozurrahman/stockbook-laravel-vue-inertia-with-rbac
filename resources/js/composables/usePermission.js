import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermission() {
    const page = usePage();
    const permissions = computed(() => page.props.auth?.permissions || []);
    const roles = computed(() => page.props.auth?.roles || []);
    const can = (permission) => roles.value.includes('super-admin') || permissions.value.includes(permission);
    const hasRole = (role) => roles.value.includes(role);
    return { can, hasRole, permissions, roles };
}
