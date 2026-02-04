<script setup lang="ts">
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Users, FileText, Briefcase, Package, ClipboardList, DollarSign, BarChart3, Settings, Shield, Receipt, CreditCard, ChevronRight } from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    SidebarMenuSub,
    SidebarMenuSubButton,
    SidebarMenuSubItem,
} from '@/components/ui/sidebar';
import {
    Collapsible,
    CollapsibleContent,
    CollapsibleTrigger,
} from '@/components/ui/collapsible';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const page = usePage();

// Mapeo de iconos por nombre
const iconMap: Record<string, any> = {
    'LayoutGrid': LayoutGrid,
    'Users': Users,
    'FileText': FileText,
    'Briefcase': Briefcase,
    'Package': Package,
    'ClipboardList': ClipboardList,
    'DollarSign': DollarSign,
    'BarChart3': BarChart3,
    'Settings': Settings,
    'Shield': Shield,
    'Receipt': Receipt,
    'CreditCard': CreditCard,
};

// Obtener módulos jerárquicos del usuario
const userModules = computed(() => {
    try {
        const permissions = page.props.permissions as any;
        return Array.isArray(permissions?.modules) ? permissions.modules : [];
    } catch (e) {
        console.error('Error loading modules:', e);
        return [];
    }
});

// Preparar módulos para el menú
const menuModules = computed(() => {
    return userModules.value.map((moduloPadre: any) => {
        return {
            title: moduloPadre.nombre,
            icon: iconMap[moduloPadre.icono] || LayoutGrid,
            slug: moduloPadre.slug,
            ruta: moduloPadre.ruta,
            children: (moduloPadre.children || []).map((child: any) => {
                // Determinar el href basado en el tipo de ruta
                let href = '';
                
                if (child.ruta) {
                    // Si la ruta contiene un punto, es un nombre de ruta de Laravel
                    if (child.ruta.includes('.')) {
                        if (typeof route !== 'undefined') {
                            try {
                                href = route(child.ruta);
                                console.log(`✓ Route ${child.ruta} → ${href}`);
                            } catch (e) {
                                console.warn(`✗ Route ${child.ruta} not found, using slug`, e);
                                href = `/${child.slug}`;
                            }
                        } else {
                            console.warn('route() function not available, using slug');
                            href = `/${child.slug}`;
                        }
                    } 
                    // Si empieza con '/' o '#', es una URL directa
                    else if (child.ruta.startsWith('/') || child.ruta.startsWith('#')) {
                        href = child.ruta;
                    }
                    // Si no, es una ruta relativa, agregar '/'
                    else {
                        href = `/${child.ruta}`;
                    }
                } else {
                    // Fallback: usar el slug
                    href = `/${child.slug}`;
                }
                
                return {
                    title: child.nombre,
                    href: href,
                    icon: iconMap[child.icono] || LayoutGrid,
                    slug: child.slug,
                };
            }),
        };
    });
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <!-- Dashboard siempre visible (si existe en Principal) -->
            <SidebarGroup v-if="menuModules.length > 0 && menuModules[0]?.children?.length > 0">
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton as-child>
                            <Link href="/dashboard">
                                <LayoutGrid />
                                <span>Dashboard</span>
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarGroup>

            <!-- Módulos jerárquicos con acordeón -->
            <template v-if="menuModules.length > 0">
                <SidebarGroup>
                    <SidebarMenu>
                        <Collapsible
                            v-for="module in menuModules"
                            :key="module.slug"
                            v-show="module.slug !== 'principal'"
                            as-child
                            :default-open="false"
                            class="group/collapsible"
                        >
                            <SidebarMenuItem>
                                <!-- Trigger del acordeón (módulo padre) -->
                                <CollapsibleTrigger as-child>
                                    <SidebarMenuButton :tooltip="module.title">
                                        <component :is="module.icon" />
                                        <span>{{ module.title }}</span>
                                        <ChevronRight class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90" />
                                    </SidebarMenuButton>
                                </CollapsibleTrigger>
                                
                                <!-- Contenido del acordeón (submódulos) -->
                                <CollapsibleContent>
                                    <SidebarMenuSub>
                                        <SidebarMenuSubItem 
                                            v-for="child in module.children" 
                                            :key="child.slug"
                                        >
                                            <SidebarMenuSubButton as-child>
                                                <Link :href="child.href">
                                                    <component :is="child.icon" />
                                                    <span>{{ child.title }}</span>
                                                </Link>
                                            </SidebarMenuSubButton>
                                        </SidebarMenuSubItem>
                                    </SidebarMenuSub>
                                </CollapsibleContent>
                            </SidebarMenuItem>
                        </Collapsible>
                    </SidebarMenu>
                </SidebarGroup>
            </template>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
